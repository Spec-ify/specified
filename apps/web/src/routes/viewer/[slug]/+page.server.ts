import { type ServerLoad } from '@sveltejs/kit';

export const prerender = false;

export const load: ServerLoad = async ({ fetch, params }) => {

    const FILE_PATH = `files/${params.slug}.json`;
	const widgetHTML = await (await fetch(`http://localhost:8080/widgets.php?file=${FILE_PATH}`)).text();
    const tableHTML = await (await fetch(`http://localhost:8080/tables.php/?file=${FILE_PATH}`)).text();
    const tabbedInfoHTML = await (await fetch(`http://localhost:8080/tabbed_info.php/?file=${FILE_PATH}`)).text();
	return { widgetHTML, tableHTML, tabbedInfoHTML};
};
