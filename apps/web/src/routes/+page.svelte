<script lang="ts">
	import { onMount } from 'svelte';
	import type { PageData } from './$types';
	import Widgets from '$lib/components/Widgets.svelte';
	import TabbedInfo from '$lib/components/TabbedInfo.svelte';
	import Accordions from '$lib/components/Accordions.svelte';
	import { page } from '$app/state';

	let { data: pageData }: { data: PageData } = $props();

	/*
	onMount(() => {
		// console.log(data.widgetResponse);
		const wrapperDiv = document.getElementById('legacy-content');
		wrapperDiv!.innerHTML = pageData.widgetHTML + pageData.tabbedInfoHTML + pageData.tableHTML;
		// document.appendChild(wrapperDiv);
	});
	*/
</script>

<!-- <div class="container h-full mx-auto flex justify-center items-center"> -->
<div class="flex items-center justify-center min-w-full">
	<div class="flex flex-col gap-4 w-full max-w-[98%]">
		<Widgets report={pageData.report} cpuMoreInfo={pageData.cpuMoreInfo}/>
		<TabbedInfo report={pageData.report} eolList={pageData.eolList}/>
		<Accordions report={pageData.report}/>
	</div>
</div>

<style>

	#legacy-content {
		/* Some legacy content is escaping and it should not
		be allowed to do so */
		overflow: hidden;
	}
</style>