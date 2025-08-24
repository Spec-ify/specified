import { type ServerLoad } from '@sveltejs/kit';

export const prerender = false;

export const load: ServerLoad = async ({ fetch }) => {
	const FILE_PATH = 'files/test1.json';
	const rawFile = await (await fetch(`http://localhost:8080/${FILE_PATH}`)).json();
	const widgetHTML = await (
		await fetch(`http://localhost:8080/widgets.php?file=${FILE_PATH}`)
	).text();
	const tableHTML = await (
		await fetch(`http://localhost:8080/tables.php/?file=${FILE_PATH}`)
	).text();
	const tabbedInfoHTML = await (
		await fetch(`http://localhost:8080/tabbed_info.php/?file=${FILE_PATH}`)
	).text();
	return { rawFile, widgetHTML, tableHTML, tabbedInfoHTML };
};
