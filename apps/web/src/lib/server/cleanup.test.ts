import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import { spawnCleanupWorker, startCleanupSchedule, stopCleanupSchedule } from './cleanup';
import fs from 'node:fs/promises';
import path from 'node:path';
import os from 'node:os';

describe('cleanup worker thread and routine', () => {
	let tempReportsDir: string;

	beforeEach(async () => {
		tempReportsDir = await fs.mkdtemp(path.join(os.tmpdir(), 'reports-test-'));
	});

	afterEach(async () => {
		stopCleanupSchedule();
		try {
			await fs.rm(tempReportsDir, { recursive: true, force: true });
		} catch {
			// Ignore
		}
	});

	it('spawns a worker thread, nuking expired files but preserving test prefix files and fresh files', async () => {
		const oldTime = (Date.now() - 48 * 60 * 60 * 1000) / 1000;

		// 1. Expired file without test prefix (should be nuked)
		const expiredFile = path.join(tempReportsDir, 'expired123.json');
		await fs.writeFile(expiredFile, JSON.stringify({ name: 'expired' }), 'utf8');
		await fs.utimes(expiredFile, oldTime, oldTime);

		// 2. Expired file WITH test prefix (should be kept)
		const testExpiredFile = path.join(tempReportsDir, 'test_sample.json');
		await fs.writeFile(testExpiredFile, JSON.stringify({ name: 'test-expired' }), 'utf8');
		await fs.utimes(testExpiredFile, oldTime, oldTime);

		// 3. Fresh file without test prefix (should be kept)
		const freshFile = path.join(tempReportsDir, 'fresh123.json');
		await fs.writeFile(freshFile, JSON.stringify({ name: 'fresh' }), 'utf8');

		// 4. Non-json file (should be ignored)
		const txtFile = path.join(tempReportsDir, 'notes.txt');
		await fs.writeFile(txtFile, 'text content', 'utf8');
		await fs.utimes(txtFile, oldTime, oldTime);

		// Run the worker routine
		const result = await spawnCleanupWorker(tempReportsDir, 24 * 60 * 60 * 1000);

		expect(result.deletedCount).toBe(1);
		expect(result.deletedFiles).toEqual(['expired123.json']);

		// Verify expiredFile is deleted
		let expiredExists = true;
		try {
			await fs.access(expiredFile);
		} catch {
			expiredExists = false;
		}
		expect(expiredExists).toBe(false);

		// Verify testExpiredFile is still there
		const testFileContent = await fs.readFile(testExpiredFile, 'utf8');
		expect(testFileContent).toContain('test-expired');

		// Verify freshFile is still there
		const freshFileContent = await fs.readFile(freshFile, 'utf8');
		expect(freshFileContent).toContain('fresh');

		// Verify txtFile is still there
		const txtContent = await fs.readFile(txtFile, 'utf8');
		expect(txtContent).toBe('text content');
	});

	it('starts and stops cleanup schedule loop', async () => {
		vi.useFakeTimers();
		startCleanupSchedule(1000);
		// Advance time
		await vi.advanceTimersByTimeAsync(1000);
		stopCleanupSchedule();
		vi.useRealTimers();
	});
});
