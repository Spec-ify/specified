<script lang="ts">
	import { onMount } from 'svelte';
	import type { PageData } from './$types';

	let { data }: { data: PageData } = $props();
	let body: HTMLBodyElement;
	onMount(() => {
		// console.log(data.widgetResponse);
		const wrapperDiv = document.getElementById('legacy-content');
		wrapperDiv!.innerHTML = data.widgetHTML + data.tabbedInfoHTML + data.tableHTML;
		// Hardcode the theme to classic-mode for now
		body.setAttribute("data-theme", "classic-mode")
	});
</script>
<svelte:body bind:this={body}/>
<!-- <div class="container h-full mx-auto flex justify-center items-center"> -->
<div>
	<div id="legacy-content"></div>
	<!-- <div class="space-y-5">
		<h1 class="h1">This is the page</h1>
	</div> -->
</div>
