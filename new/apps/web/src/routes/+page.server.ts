import { type ServerLoad } from '@sveltejs/kit';
import { error } from '@sveltejs/kit';
export const prerender = false;

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
	return { report, widgetHTML, tableHTML, tabbedInfoHTML };
};
