import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';

export const MAX_REPORT_SIZE_BYTES = 10 * 1024 * 1024; // 10 MB
export const REPORT_MAX_AGE_MS = 24 * 60 * 60 * 1000; // 24 hours

/**
 * Returns the absolute path to the reports/ directory.
 * Works both in dev mode (cwd is apps/web) and production.
 */
export function getReportsDir(): string {
	if (process.env.REPORTS_DIR) {
		return path.resolve(process.env.REPORTS_DIR);
	}
	const candidates = [
		path.resolve(process.cwd(), 'reports'),
		path.resolve(process.cwd(), '../../reports'),
		path.resolve(process.cwd(), '../reports')
	];
	for (const candidate of candidates) {
		if (fs.existsSync(candidate)) {
			return candidate;
		}
	}
	// Fallback
	return path.resolve(process.cwd(), 'reports');
}

/**
 * Generates an 8-character deterministic hex slug for a report
 * based on Version, GenerationDate, and Hostname.
 */
export function generateReportSlug(report: unknown): string {
	const reportObj = (report && typeof report === 'object' ? report : {}) as Record<
		string,
		unknown
	>;
	const version = typeof reportObj.Version === 'string' ? reportObj.Version : '';
	const meta = (
		reportObj.Meta && typeof reportObj.Meta === 'object' ? reportObj.Meta : {}
	) as Record<string, unknown>;
	const basicInfo = (
		reportObj.BasicInfo && typeof reportObj.BasicInfo === 'object' ? reportObj.BasicInfo : {}
	) as Record<string, unknown>;

	const date = typeof meta.GenerationDate === 'string' ? meta.GenerationDate : '';
	const hostname = typeof basicInfo.Hostname === 'string' ? basicInfo.Hostname : '';

	let rawString = `${version}${date}${hostname}`;
	if (!rawString.trim()) {
		try {
			rawString = JSON.stringify(report) || crypto.randomUUID();
		} catch {
			rawString = crypto.randomUUID();
		}
	}

	return crypto.createHash('md5').update(rawString).digest('hex').substring(0, 8);
}

/**
 * Minimal validation to verify if an object is a Specify report.
 */
export function isValidSpecifyReport(data: unknown): boolean {
	if (!data || typeof data !== 'object' || Array.isArray(data)) {
		return false;
	}
	const obj = data as Record<string, unknown>;
	if (typeof obj.Version !== 'string' || obj.Version.trim() === '') {
		return false;
	}
	if (!obj.BasicInfo || typeof obj.BasicInfo !== 'object' || Array.isArray(obj.BasicInfo)) {
		return false;
	}
	return true;
}

/**
 * Checks whether a filename or path has the test prefix.
 */
export function isTestReport(filenameOrPath: string): boolean {
	const base = path.basename(filenameOrPath);
	return base.startsWith('test');
}

/**
 * Extracts the effective file creation time in milliseconds.
 * Uses the minimum of valid birthtime, ctime, and mtime to handle
 * filesystems where birthtime is not available or differs from creation time.
 */
export function getFileCreationTimeMs(stats: {
	birthtimeMs: number;
	ctimeMs: number;
	mtimeMs: number;
}): number {
	const validTimes = [stats.birthtimeMs, stats.ctimeMs, stats.mtimeMs].filter((t) => t && t > 0);
	if (validTimes.length === 0) {
		return Date.now();
	}
	return Math.min(...validTimes);
}

interface RateLimitRecord {
	timestamps: number[];
}

const rateLimitMap = new Map<string, RateLimitRecord>();

/**
 * Performs rate limiting based on client IP.
 * Defaults to 10 requests per minute per IP.
 */
export function checkRateLimit(
	ip: string,
	maxRequests = 10,
	windowMs = 60_000
): { allowed: boolean; remaining: number; retryAfterSeconds: number } {
	const now = Date.now();
	const record = rateLimitMap.get(ip) ?? { timestamps: [] };

	// Purge timestamps older than the sliding window
	record.timestamps = record.timestamps.filter((t) => now - t < windowMs);

	if (record.timestamps.length >= maxRequests) {
		const oldest = record.timestamps[0];
		const retryAfterSeconds = Math.max(1, Math.ceil((oldest + windowMs - now) / 1000));
		return {
			allowed: false,
			remaining: 0,
			retryAfterSeconds
		};
	}

	record.timestamps.push(now);
	rateLimitMap.set(ip, record);

	// Garbage collect stale IPs if map grows
	if (rateLimitMap.size > 10_000) {
		for (const [key, val] of rateLimitMap.entries()) {
			val.timestamps = val.timestamps.filter((t) => now - t < windowMs);
			if (val.timestamps.length === 0) {
				rateLimitMap.delete(key);
			}
		}
	}

	return {
		allowed: true,
		remaining: maxRequests - record.timestamps.length,
		retryAfterSeconds: 0
	};
}

/**
 * Clear in-memory rate limits (primarily for tests).
 */
export function resetRateLimits(): void {
	rateLimitMap.clear();
}
