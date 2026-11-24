import { describe, it, expect, beforeEach } from 'vitest';
import { generateReportSlug, checkRateLimit, resetRateLimits } from './report-service';

describe('report-service', () => {
	beforeEach(() => {
		resetRateLimits();
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
