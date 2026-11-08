import { type ServerLoad } from '@sveltejs/kit';
import { dev } from '$app/environment';
import fs from 'node:fs/promises';
import path from 'node:path';
export const prerender = false;
import { error } from '@sveltejs/kit';
import type { Report } from '$lib/common/report/report';
import { getReportsDir, isTestReport, REPORT_MAX_AGE_MS } from '$lib/server/report-service';

interface CpuInfo {
	CurrentClockSpeed: number;
	LoadPercentage: number;
	Manufacturer: string;
	Name: string;
	// Appears to be a typo in the schema
	NumberOfEnabledCore: number;
	SocketDesignation: string;
	ThreadCount: number;
}

interface EolListEntry {
	cycle: string;
	releaseLabel: string;
	releaseDate: string;
	eol: string;
	latest: string;
	link: string;
	lts: boolean;
	support: string;
	extendedSupport: boolean;
}

async function eolLookup(): Promise<Array<EolListEntry>> {
	try {
		return await (
			await fetch(`https://endoflife.date/api/windows.json`, {
				method: 'GET',
				mode: 'cors'
			})
		).json();
	} catch (e) {
		console.warn('Could not fetch windows endoflife list:', e);
		return [];
	}
}
/*
 * Looks up CPU name on HWAPI database
 *
 * @param CPU info of report
 * @return (Response) Response of database with info
 */
async function cpuLookup(cpu: CpuInfo): Promise<Response> {
	let response: Response | undefined;

	if (dev) {
		console.info('Trying local server for hwapi');
		try {
			response = await (
				await fetch(
					`http://localhost:3000/api/cpus/?name=${encodeURIComponent(cpu?.Name || '')}`,
					{
						method: 'GET',
						mode: 'cors'
					}
				)
			).json();
		} catch {
			console.warn('Could not connect to local hwapi instance, falling back to spec-ify.com');
		}
	}

	if (!response && cpu?.Name) {
		try {
			response = await (
				await fetch(`https://spec-ify.com/api/cpus/?name=${encodeURIComponent(cpu.Name)}`, {
					method: 'GET',
					mode: 'cors'
				})
			).json();
		} catch {
			console.warn('Could not connect to spec-ify.com for hwapi');
		}
	}

	if (response) {
		return response;
	}
	return new Response(JSON.stringify({}));
}

export const load: ServerLoad = async ({ params }) => {
	const slug = params.slug;
	if (!slug || slug.includes('/') || slug.includes('\\') || slug.includes('..')) {
		error(400, 'Invalid report name');
	}

	const filePath = path.join(getReportsDir(), `${slug}.json`);

	let stats;
	try {
		stats = await fs.stat(filePath);
	} catch {
		error(
			404,
			'Report not found, make sure the report exists and the name is spelled correctly'
		);
	}

	// If not a test report, check creation time and delete if > 24 hours old
	if (!isTestReport(slug)) {
		const creationTime = Math.min(stats.mtimeMs, stats.ctimeMs);
		const ageMs = Date.now() - creationTime;
		if (ageMs > REPORT_MAX_AGE_MS) {
			try {
				await fs.unlink(filePath);
			} catch (e) {
				console.error(`Failed to delete expired report at ${filePath}:`, e);
			}
			error(
				404,
				'Report has expired and is no longer available (reports are kept for 24 hours)'
			);
		}
	}

	let file;
	try {
		file = await fs.readFile(filePath, { encoding: 'utf8' });
	} catch {
		error(404, 'Failed to read report file');
	}
	const report: Report = JSON.parse(file);
	const eolList = await eolLookup();
	const cpuMoreInfo = await cpuLookup(report.Hardware.Cpu);
	return { eolList, report, cpuMoreInfo };
};
