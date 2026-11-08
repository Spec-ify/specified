import { json, type RequestHandler } from '@sveltejs/kit';
import fs from 'node:fs/promises';
import path from 'node:path';
import {
	checkRateLimit,
	generateReportSlug,
	getReportsDir,
	isValidSpecifyReport,
	MAX_REPORT_SIZE_BYTES
} from '$lib/server/report-service';

export const POST: RequestHandler = async (event) => {
	// 1. Rate Limiting Check
	let clientIp = '127.0.0.1';
	try {
		clientIp = event.getClientAddress();
	} catch {
		// Fallback for environments where getClientAddress is unavailable
		clientIp =
			event.request.headers.get('x-forwarded-for')?.split(',')[0].trim() || '127.0.0.1';
	}

	const rateLimit = checkRateLimit(clientIp);
	if (!rateLimit.allowed) {
		return json(
			{
				error: 'Too many requests. Rate limit exceeded, please try again later.',
				retryAfterSeconds: rateLimit.retryAfterSeconds
			},
			{
				status: 429,
				headers: {
					'Retry-After': String(rateLimit.retryAfterSeconds)
				}
			}
		);
	}

	// 2. Early Content-Length Size Check
	const contentLength = event.request.headers.get('content-length');
	if (contentLength && parseInt(contentLength, 10) > MAX_REPORT_SIZE_BYTES) {
		return json(
			{
				error: `Payload too large. Maximum allowed size is ${MAX_REPORT_SIZE_BYTES / (1024 * 1024)}MB.`
			},
			{ status: 413 }
		);
	}

	// 3. Parse Payload
	const contentType = event.request.headers.get('content-type') || '';
	let rawText = '';

	try {
		if (contentType.includes('multipart/form-data')) {
			const formData = await event.request.formData();
			// Look for file under common keys ('specify', 'file') or any File in entries
			let fileCandidate: File | null = null;
			const specifyField = formData.get('specify');
			const fileField = formData.get('file');

			if (specifyField instanceof File) {
				fileCandidate = specifyField;
			} else if (fileField instanceof File) {
				fileCandidate = fileField;
			} else {
				for (const value of formData.values()) {
					if (value instanceof File) {
						fileCandidate = value;
						break;
					}
				}
			}

			if (!fileCandidate) {
				return json({ error: 'No file found in multipart upload.' }, { status: 400 });
			}

			if (fileCandidate.size > MAX_REPORT_SIZE_BYTES) {
				return json(
					{
						error: `Payload too large. Maximum allowed size is ${MAX_REPORT_SIZE_BYTES / (1024 * 1024)}MB.`
					},
					{ status: 413 }
				);
			}

			rawText = await fileCandidate.text();
		} else {
			rawText = await event.request.text();
		}
	} catch (err: unknown) {
		const message = err instanceof Error ? err.message : String(err);
		return json({ error: `Failed to read request body: ${message}` }, { status: 400 });
	}

	// Double check body size in bytes
	if (Buffer.byteLength(rawText, 'utf8') > MAX_REPORT_SIZE_BYTES) {
		return json(
			{
				error: `Payload too large. Maximum allowed size is ${MAX_REPORT_SIZE_BYTES / (1024 * 1024)}MB.`
			},
			{ status: 413 }
		);
	}

	// 4. JSON Parse & Validation
	let parsedReport: unknown;
	try {
		parsedReport = JSON.parse(rawText);
	} catch {
		return json({ error: 'Malformed JSON payload.' }, { status: 400 });
	}

	if (!isValidSpecifyReport(parsedReport)) {
		return json(
			{
				error: 'Invalid Specify report format. Report must contain Version and BasicInfo object.'
			},
			{ status: 400 }
		);
	}

	// 5. Generate Slug & Save Report
	const slug = generateReportSlug(parsedReport);
	const reportsDir = getReportsDir();

	try {
		await fs.mkdir(reportsDir, { recursive: true });
		const targetFile = path.join(reportsDir, `${slug}.json`);
		await fs.writeFile(targetFile, JSON.stringify(parsedReport, null, 2), 'utf8');
	} catch (err: unknown) {
		const message = err instanceof Error ? err.message : String(err);
		return json({ error: `Failed to save report to disk: ${message}` }, { status: 500 });
	}

	const viewerUrl = `/viewer/${slug}`;

	return json(
		{
			success: true,
			slug,
			id: slug,
			url: viewerUrl
		},
		{
			status: 201,
			headers: {
				Location: viewerUrl
			}
		}
	);
};
