import { Worker } from 'node:worker_threads';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { getReportsDir, REPORT_MAX_AGE_MS } from './report-service';

export interface CleanupResult {
	checkedCount: number;
	deletedCount: number;
	deletedFiles: string[];
}

/**
 * Resolves the path to the worker script file across dev and production.
 */
export function getWorkerPath(): string {
	const candidates = [
		path.resolve(process.cwd(), 'src/lib/server/cleanup-worker.mjs'),
		path.resolve(process.cwd(), 'apps/web/src/lib/server/cleanup-worker.mjs'),
		path.resolve(process.cwd(), 'build/cleanup-worker.mjs'),
		path.resolve(process.cwd(), 'build/server/cleanup-worker.mjs'),
		fileURLToPath(new URL('./cleanup-worker.mjs', import.meta.url))
	];
	for (const candidate of candidates) {
		if (fs.existsSync(candidate)) {
			return candidate;
		}
	}
	return fileURLToPath(new URL('./cleanup-worker.mjs', import.meta.url));
}

/**
 * Routine that spawns a worker thread to clean up reports older than 24 hours.
 */
export function spawnCleanupWorker(
	reportsDir = getReportsDir(),
	maxAgeMs = REPORT_MAX_AGE_MS
): Promise<CleanupResult> {
	return new Promise((resolve, reject) => {
		const workerPath = getWorkerPath();
		const worker = new Worker(workerPath, {
			workerData: { reportsDir, maxAgeMs }
		});

		worker.on('message', (message) => {
			if (message.success) {
				resolve(message.result);
			} else {
				reject(new Error(message.error));
			}
		});

		worker.on('error', (err) => {
			reject(err);
		});

		worker.on('exit', (code) => {
			if (code !== 0) {
				reject(new Error(`Worker stopped with exit code ${code}`));
			}
		});
	});
}

let cleanupTimeoutId: NodeJS.Timeout | null = null;

/**
 * Starts the cleanup routine on startup and registers a recurring setTimeout loop.
 */
export function startCleanupSchedule(intervalMs: number = 30 * 60 * 1000): void {
	// 1. Run immediately on startup
	spawnCleanupWorker()
		.then((res) => {
			if (res.deletedCount > 0) {
				console.info(
					`[Cleanup Worker] Startup cleanup: removed ${res.deletedCount} expired report(s).`
				);
			}
		})
		.catch((err) => {
			console.error('[Cleanup Worker] Startup cleanup failed:', err);
		});

	// 2. Set up recurring setTimeout loop
	function loop() {
		cleanupTimeoutId = setTimeout(async () => {
			try {
				const res = await spawnCleanupWorker();
				if (res.deletedCount > 0) {
					console.info(
						`[Cleanup Worker] Periodic cleanup: removed ${res.deletedCount} expired report(s).`
					);
				}
			} catch (err) {
				console.error('[Cleanup Worker] Periodic cleanup failed:', err);
			} finally {
				loop();
			}
		}, intervalMs);

		if (cleanupTimeoutId && typeof cleanupTimeoutId.unref === 'function') {
			cleanupTimeoutId.unref();
		}
	}

	loop();
}

/**
 * Stops the scheduled setTimeout cleanup loop.
 */
export function stopCleanupSchedule(): void {
	if (cleanupTimeoutId) {
		clearTimeout(cleanupTimeoutId);
		cleanupTimeoutId = null;
	}
}
