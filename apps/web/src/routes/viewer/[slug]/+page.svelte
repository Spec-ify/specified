<script lang="ts">
	import type { PageData } from './$types';
	import Widgets from '$lib/components/Widgets.svelte';
	import TabbedInfo from '$lib/components/TabbedInfo.svelte';
	import Accordions from '$lib/components/Accordions.svelte';

	let { data: pageData }: { data: PageData } = $props();
	let report = pageData.report;
</script>

<div class="flex items-center justify-center min-w-full p-4">
	<div class="flex flex-col gap-4 w-full">
		<Widgets report={pageData.report} cpuMoreInfo={pageData.cpuMoreInfo} />
		<TabbedInfo report={pageData.report} eolList={pageData.eolList} />
		<Accordions report={pageData.report} />
	</div>
</div>
