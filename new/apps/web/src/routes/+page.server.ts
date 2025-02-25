import { type ServerLoad } from '@sveltejs/kit';

export const prerender = false;

export const load: ServerLoad = async ({ fetch }) => {
    const FILE_PATH = 'files/test1.json';
	const widgetHTML = await (await fetch(`http://localhost:8080/widgets.php?file=${FILE_PATH}`)).text();
    const tableHTML = await (await fetch('http://localhost:8080/tables.php/?file=files/test1.json')).text();
    const tabbedInfoHTML = await (await fetch('http://localhost:8080/tabbed_info.php/?file=files/test1.json')).text();
	return { widgetHTML, tableHTML, tabbedInfoHTML};
};
