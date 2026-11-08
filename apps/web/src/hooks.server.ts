import type { ServerInit } from '@sveltejs/kit';
import { startCleanupSchedule } from '$lib/server/cleanup';

/**
 * Server initialization hook.
 * Spawns the worker thread on startup and sets up the periodic setTimeout loop.
 */
export const init: ServerInit = async () => {
	startCleanupSchedule();
};
