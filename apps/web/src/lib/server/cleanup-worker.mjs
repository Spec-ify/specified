import { parentPort, workerData } from 'node:worker_threads';
import fs from 'node:fs';
import path from 'node:path';

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
			if (!filename.endsWith('.json') || filename.startsWith('test')) {
				continue;
			}
			result.checkedCount++;
			const fullPath = path.join(reportsDir, filename);
			try {
				const stats = fs.statSync(fullPath);
				const validTimes = [stats.birthtimeMs, stats.ctimeMs, stats.mtimeMs].filter(
					(t) => t && t > 0
				);
				const creationTime = validTimes.length > 0 ? Math.min(...validTimes) : now;
				if (now - creationTime > maxAgeMs) {
					fs.unlinkSync(fullPath);
					result.deletedCount++;
					result.deletedFiles.push(filename);
				}
			} catch (err) {
				console.error(`[Cleanup Worker] Error processing file ${filename}:`, err);
			}
		}
	}
	parentPort?.postMessage({ success: true, result });
} catch (err) {
	parentPort?.postMessage({
		success: false,
		error: err instanceof Error ? err.message : String(err)
	});
}
