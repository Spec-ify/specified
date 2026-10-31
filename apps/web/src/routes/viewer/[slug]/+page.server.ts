import { type ServerLoad } from '@sveltejs/kit';
import { dev } from "$app/environment";
import fs from 'node:fs/promises';
export const prerender = false;
import { error } from '@sveltejs/kit';

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
	releaseLabel: string
	releaseDate: string
	eol: string
	latest: string
	link: string
	lts: boolean
	support: string
	extendedSupport: boolean
}

async function eolLookup(): Promise<Array<EolListEntry>> {
	return await (
		await fetch(
			`https://endoflife.date/api/windows.json`,
			{
				method: 'GET',
				mode: 'cors'
			}
		)
	).json();
}
/*
 * Looks up CPU name on HWAPI database
 * 
 * @param CPU info of report
 * @return (Response) Response of database with info
 */
async function cpuLookup(cpu: CpuInfo): Response {
	let response: Response | undefined;

	if (dev) {
		console.info('Trying local server for hwapi');
		try {
			response = await (
				await fetch(
					`http://localhost:3000/api/cpus/?name=${encodeURIComponent(cpu.Name)}`,
					{
						method: 'GET',
						mode: 'cors'
					}
				)
			).json();
		} catch (e) {
			console.warn(
				'Could not connect to local hwapi instance, falling back to spec-ify.com'
			);
		}
	}

	if (!response) {
		response = await (
			await fetch(`https://spec-ify.com/api/cpus/?name=${encodeURIComponent(cpu.Name)}`, {
				method: 'GET',
				mode: 'cors'
			})
		).json();
	}

	if (response){
		return response;
	}
}

export const load: ServerLoad = async ({ fetch, params }) => {
    let filePath = `reports/${params.slug}.json`;
	if (dev) {
		// "../.." included because turbo starts the process for each app from wherever the package.json is for that app,
		// but the root of the repository is preferred
		filePath = "../../" + filePath;
	}
    let file;
    try {
        file = await fs.readFile(filePath, {encoding: "utf8"});
    } catch {
        error(404, "failed to load report, make sure `reports/` exists in the root of the repository and that the report name is spelled correctly")
    }
    const report: any = JSON.parse(file);
    const eolList = await eolLookup();
	const cpuMoreInfo = await cpuLookup(report.Hardware.Cpu);
	return { eolList, report, cpuMoreInfo };
};
