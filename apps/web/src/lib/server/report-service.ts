import path from 'node:path';
import crypto from 'node:crypto';
import { dev } from '$app/environment';

export const MAX_REPORT_SIZE_BYTES = 10 * 1024 * 1024; // 10 MB
export const REPORT_MAX_AGE_MS = 24 * 60 * 60 * 1000; // 24 hours

/**
 * Returns the path to the reports/ directory.
 * If dev mode, returns '../../reports/'. Otherwise, './reports/'.
 */
export function getReportsDir(): string {
	return dev ? '../../reports/' : './reports/';
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
 * Generates an 8-character deterministic hex slug for a report
 * based on Version, GenerationDate, and Hostname.
 */
export function generateReportSlug(report: unknown): string {
	if (!isValidSpecifyReport(report)) {
		return crypto.randomBytes(4).toString('hex');
	}
	const r = report as {
		Version?: string;
		Meta?: { GenerationDate?: string };
		BasicInfo?: { Hostname?: string };
	};
	const rawString = `${r.Version ?? ''}${r.Meta?.GenerationDate ?? ''}${r.BasicInfo?.Hostname ?? ''}`;
	return crypto.createHash('md5').update(rawString).digest('hex').substring(0, 8);
}

/**
 * Checks whether a filename or path has the test prefix.
 */
export function isTestReport(filenameOrPath: string): boolean {
	const base = path.basename(filenameOrPath);
	return base.startsWith('test');
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
