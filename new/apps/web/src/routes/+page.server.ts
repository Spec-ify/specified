import { type ServerLoad } from '@sveltejs/kit';
import { dev } from '$app/environment';
import { error } from '@sveltejs/kit';
export const prerender = false;

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
			await fetch(`https://spec-ifygoon.com/api/cpus/?name=${encodeURIComponent(cpu.Name)}`, {
				method: 'GET',
				mode: 'cors'
			})
		).json();
	}

	if (response){
		return response;
	}
}

export const load: ServerLoad = async ({ fetch }) => {
	const FILE_PATH = 'files/test1.json';
	let report;
	try {
		const reportResponse: Response = await fetch(`http://localhost:8080/${FILE_PATH}`);
		report = await reportResponse.json();
	} catch {
		error(503, "no spec-ify instance found at localhost:8080");
	}
	const widgetHTML = await (
		await fetch(`http://localhost:8080/widgets.php?file=${FILE_PATH}`)
	).text();
	const tableHTML = await (
		await fetch(`http://localhost:8080/tables.php/?file=${FILE_PATH}`)
	).text();
	const tabbedInfoHTML = await (
		await fetch(`http://localhost:8080/tabbed_info.php/?file=${FILE_PATH}`)
	).text();
	const cpuMoreInfo = cpuLookup(report.Hardware.Cpu);
	return { report, cpuMoreInfo, widgetHTML, tableHTML, tabbedInfoHTML };
};
