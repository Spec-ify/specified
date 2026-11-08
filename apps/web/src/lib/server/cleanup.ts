import { Worker } from 'node:worker_threads';
import { getReportsDir, REPORT_MAX_AGE_MS } from './report-service';

export interface CleanupResult {
	checkedCount: number;
	deletedCount: number;
	deletedFiles: string[];
}

/**
 * Worker thread script to inspect reports/ and delete expired files.
 */
const WORKER_SCRIPT = `
const { parentPort, workerData } = require('node:worker_threads');
const fs = require('node:fs');
const path = require('node:path');

const { reportsDir, maxAgeMs } = workerData;
const now = Date.now();
const result = {
	checkedCount: 0,
	deletedCount: 0,
	deletedFiles: []
};

try {
	if (fs.existsSync(reportsDir)) {
		const files = fs.readdirSync(reportsDir);
		for (const filename of files) {
			// Skip non-json or files with the 'test' prefix
			if (!filename.endsWith('.json') || filename.startsWith('test')) {
				continue;
			}
			result.checkedCount++;
			const fullPath = path.join(reportsDir, filename);
			try {
				const stats = fs.statSync(fullPath);
				const validTimes = [stats.birthtimeMs, stats.ctimeMs, stats.mtimeMs].filter((t) => t && t > 0);
				const creationTime = validTimes.length > 0 ? Math.min(...validTimes) : now;
				if (now - creationTime > maxAgeMs) {
					fs.unlinkSync(fullPath);
					result.deletedCount++;
					result.deletedFiles.push(filename);
				}
			} catch (err) {
				console.error('[Cleanup Worker] Error processing file ' + filename + ':', err);
			}
		}
	}
	parentPort.postMessage({ success: true, result });
} catch (err) {
	parentPort.postMessage({ success: false, error: err.message || String(err) });
}
`;

/**
 * Routine that spawns a worker thread to clean up reports older than 24 hours.
 */
export function spawnCleanupWorker(
	reportsDir = getReportsDir(),
	maxAgeMs = REPORT_MAX_AGE_MS
): Promise<CleanupResult> {
	return new Promise((resolve, reject) => {
		const worker = new Worker(WORKER_SCRIPT, {
			eval: true,
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
export function startCleanupSchedule(
	intervalMs: number = parseInt(process.env.CLEANUP_INTERVAL_MS || String(30 * 60 * 1000), 10)
): void {
	// 1. Run immediately on startup
	spawnCleanupWorker()
		.then((res) => {
			if (res.deletedCount > 0) {
				console.info(`[Cleanup Worker] Startup cleanup: removed ${res.deletedCount} expired report(s).`);
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
