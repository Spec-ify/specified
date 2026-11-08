import { describe, it, expect, beforeEach, afterEach } from 'vitest';
import { POST } from './+server';
import { resetRateLimits, getReportsDir } from '$lib/server/report-service';
import fs from 'node:fs/promises';
import path from 'node:path';

describe('POST /upload', () => {
	const validReport = {
		Version: 'v1.4.0',
		Meta: { GenerationDate: '2026-08-09T20:37:04.0328748-06:00' },
		BasicInfo: { Hostname: 'thomas' }
	};
	const expectedSlug = 'e5fcd31b';
	const uploadedFilePath = path.join(getReportsDir(), `${expectedSlug}.json`);

	beforeEach(() => {
		resetRateLimits();
	});

	afterEach(async () => {
		// Clean up any test artifact created
		try {
			await fs.unlink(uploadedFilePath);
		} catch {
			// Ignore if not created
		}
	});

	function createMockEvent(request: Request, clientIp = '10.0.0.1') {
		return {
			request,
			getClientAddress: () => clientIp,
			locals: {},
			params: {},
			platform: {},
			route: { id: '/upload' },
			url: new URL('http://localhost:3000/upload'),
			cookies: {} as any,
			fetch: fetch as any,
			setHeaders: () => {},
			isDataRequest: false
		} as any;
	}

	it('accepts valid JSON payload and stores report', async () => {
		const req = new Request('http://localhost:3000/upload', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(validReport)
		});

		const res = await POST(createMockEvent(req));
		expect(res.status).toBe(201);
		expect(res.headers.get('Location')).toBe(`/viewer/${expectedSlug}`);

		const body = await res.json();
		expect(body.success).toBe(true);
		expect(body.slug).toBe(expectedSlug);
		expect(body.url).toBe(`/viewer/${expectedSlug}`);

		// Verify file was written to disk
		const content = await fs.readFile(uploadedFilePath, 'utf8');
		const saved = JSON.parse(content);
		expect(saved.BasicInfo.Hostname).toBe('thomas');
	});

	it('accepts multipart/form-data upload', async () => {
		const formData = new FormData();
		const file = new File([JSON.stringify(validReport)], 'report.json', {
			type: 'application/json'
		});
		formData.append('specify', file);

		const req = new Request('http://localhost:3000/upload', {
			method: 'POST',
			body: formData
		});

		const res = await POST(createMockEvent(req));
		expect(res.status).toBe(201);
		const body = await res.json();
		expect(body.slug).toBe(expectedSlug);
	});

	it('rejects invalid JSON syntax', async () => {
		const req = new Request('http://localhost:3000/upload', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: '{ invalid json'
		});

		const res = await POST(createMockEvent(req));
		expect(res.status).toBe(400);
		const body = await res.json();
		expect(body.error).toContain('Malformed JSON');
	});

	it('rejects non-Specify JSON structure', async () => {
		const req = new Request('http://localhost:3000/upload', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ random: 'data' })
		});

		const res = await POST(createMockEvent(req));
		expect(res.status).toBe(400);
		const body = await res.json();
		expect(body.error).toContain('Invalid Specify report format');
	});

	it('enforces maximum payload size limit', async () => {
		const req = new Request('http://localhost:3000/upload', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Content-Length': String(15 * 1024 * 1024)
			},
			body: JSON.stringify(validReport)
		});

		const res = await POST(createMockEvent(req));
		expect(res.status).toBe(413);
		const body = await res.json();
		expect(body.error).toContain('Payload too large');
	});

	it('enforces rate limiting on repeated requests from same IP', async () => {
		const ip = '192.168.1.100';
		for (let i = 0; i < 10; i++) {
			const req = new Request('http://localhost:3000/upload', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(validReport)
			});
			const res = await POST(createMockEvent(req, ip));
			expect(res.status).toBe(201);
		}

		// 11th request should be rate-limited
		const req = new Request('http://localhost:3000/upload', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(validReport)
		});
		const res = await POST(createMockEvent(req, ip));
		expect(res.status).toBe(429);
		expect(res.headers.get('Retry-After')).toBeTruthy();
	});
});
