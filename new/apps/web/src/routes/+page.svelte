<script lang="ts">
	import { onMount } from 'svelte';
	import type { PageData } from './$types';
	import Widgets from '$lib/components/Widgets.svelte';

	let { data: pageData }: { data: PageData } = $props();

	onMount(() => {
		// console.log(data.widgetResponse);
		const wrapperDiv = document.getElementById('legacy-content');
		wrapperDiv!.innerHTML = pageData.widgetHTML + pageData.tabbedInfoHTML + pageData.tableHTML;
		// document.appendChild(wrapperDiv);
	});
</script>

<!-- <div class="container h-full mx-auto flex justify-center items-center"> -->
<div class="vp">
	<Widgets report={pageData.report} />
	<div id="legacy-content"></div>
	<!-- <div class="space-y-5">
		<h1 class="h1">This is the page</h1>
	</div> -->
</div>

<style>
	.vp {
		background-color: var(--color-surface-950);
		margin: 0;
		padding: 0;
		width: 100vw !important;
		/* flex-grow: 0;
		flex-shrink: 0; */
	}

	:global(body) {
		width: 100vw !important;
	}
</style>