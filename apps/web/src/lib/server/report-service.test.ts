import { describe, it, expect, beforeEach } from 'vitest';
import {
	generateReportSlug,
	isValidSpecifyReport,
	isTestReport,
	checkRateLimit,
	resetRateLimits,
	getReportsDir
} from './report-service';

describe('report-service', () => {
	beforeEach(() => {
		resetRateLimits();
	});

	it('resolves reports directory', () => {
		const dir = getReportsDir();
		expect(dir).toBeTruthy();
		expect(dir).toContain('reports');
	});

	it('generates deterministic slug matching legacy algorithm', () => {
		const sample = {
			Version: 'v1.4.0',
			Meta: { GenerationDate: '2026-08-09T20:37:04.0328748-06:00' },
			BasicInfo: { Hostname: 'thomas' }
		};
		const slug = generateReportSlug(sample);
		expect(slug).toBe('e5fcd31b');
	});

	it('validates specify report minimally', () => {
		expect(isValidSpecifyReport(null)).toBe(false);
		expect(isValidSpecifyReport({})).toBe(false);
		expect(isValidSpecifyReport({ Version: 123 })).toBe(false);
		expect(isValidSpecifyReport({ Version: 'v1.0.0' })).toBe(false);
		expect(
			isValidSpecifyReport({
				Version: 'v1.4.0',
				BasicInfo: { Hostname: 'test' }
			})
		).toBe(true);
	});

	it('identifies test reports by prefix', () => {
		expect(isTestReport('test1.json')).toBe(true);
		expect(isTestReport('/path/to/test_sample.json')).toBe(true);
		expect(isTestReport('e5fcd31b.json')).toBe(false);
		expect(isTestReport('/path/to/reports/report.json')).toBe(false);
	});

	it('enforces rate limiting per IP', () => {
		const ip = '127.0.0.1';
		for (let i = 0; i < 5; i++) {
			const res = checkRateLimit(ip, 5, 1000);
			expect(res.allowed).toBe(true);
		}
		const blocked = checkRateLimit(ip, 5, 1000);
		expect(blocked.allowed).toBe(false);
		expect(blocked.retryAfterSeconds).toBeGreaterThan(0);

		// Different IP should still be allowed
		const other = checkRateLimit('127.0.0.2', 5, 1000);
		expect(other.allowed).toBe(true);
	});
});
