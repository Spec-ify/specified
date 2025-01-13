import { type ServerLoad } from "@sveltejs/kit";

export const prerender = false;

export const load: ServerLoad = async ({ fetch }) => {
    const res = await fetch('http://localhost:8080/widgets.php?file=files/test1.json');
    return {widgetResponse: await res.text()};
};