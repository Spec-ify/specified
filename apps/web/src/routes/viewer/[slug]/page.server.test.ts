import { describe, it, expect, vi, afterEach } from 'vitest';
import { load } from './+page.server';
import { getReportsDir } from '$lib/server/report-service';
import fs from 'node:fs/promises';
import path from 'node:path';

describe('/viewer/[slug] load', () => {
	const expiredSlug = 'expired_sample_report';
	const expiredFilePath = path.join(getReportsDir(), `${expiredSlug}.json`);

	afterEach(async () => {
		try {
			await fs.unlink(expiredFilePath);
		} catch {
			// Ignore
		}
	});

	it('loads test1 report without deleting it', async () => {
		const res = await (load as any)({
			params: { slug: 'test1' },
			fetch: vi.fn().mockImplementation(() =>
				Promise.resolve({
					json: () => Promise.resolve([])
				})
			)
		});

		expect(res).toBeDefined();
		expect(res.report).toBeDefined();
		expect(res.report.BasicInfo.Hostname).toBe('thomas');
	});

	it('deletes expired report (> 24 hours) and throws 404', async () => {
		const dummyReport = {
			Version: 'v1.4.0',
			Meta: { GenerationDate: '2024-01-01T00:00:00Z' },
			BasicInfo: { Hostname: 'expired-host' },
			Hardware: { Cpu: { Name: 'Dummy CPU' } }
		};

		await fs.writeFile(expiredFilePath, JSON.stringify(dummyReport), 'utf8');

		// Set mtime and atime to 48 hours ago
		const oldTime = (Date.now() - 48 * 60 * 60 * 1000) / 1000;
		await fs.utimes(expiredFilePath, oldTime, oldTime);

		let thrownError: any = null;
		try {
			await (load as any)({
				params: { slug: expiredSlug },
				fetch: vi.fn().mockImplementation(() =>
					Promise.resolve({
						json: () => Promise.resolve([])
					})
				)
			});
		} catch (err) {
			thrownError = err;
		}

		expect(thrownError).toBeDefined();
		expect(thrownError.status).toBe(404);
		expect(thrownError.body?.message).toContain('expired');

		// Verify the file was indeed nuked
		let fileExists = true;
		try {
			await fs.access(expiredFilePath);
		} catch {
			fileExists = false;
		}
		expect(fileExists).toBe(false);
	});

	it('returns 404 for non-existent report', async () => {
		let thrownError: any = null;
		try {
			await (load as any)({
				params: { slug: 'does-not-exist' },
				fetch: vi.fn()
			});
		} catch (err) {
			thrownError = err;
		}

		expect(thrownError).toBeDefined();
		expect(thrownError.status).toBe(404);
	});
});
