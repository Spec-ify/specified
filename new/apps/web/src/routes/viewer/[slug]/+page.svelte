<script lang="ts">
	import { onMount } from 'svelte';
	import type { PageData } from './$types';

	let { data }: { data: PageData } = $props();
	let body: HTMLBodyElement;
	onMount(() => {
		// console.log(data.widgetResponse);
		const wrapperDiv = document.getElementById('legacy-content');
		wrapperDiv!.innerHTML = data.widgetHTML + data.tabbedInfoHTML + data.tableHTML;
	});
</script>
<svelte:body bind:this={body}/>
<!-- <div class="container h-full mx-auto flex justify-center items-center"> -->
<div>
	<div id="legacy-content">
		<!-- populated on js side -->
	</div>
</div>

<style>
	:global(body) {
		background-color: var(--body-background-color);
	}
</style>