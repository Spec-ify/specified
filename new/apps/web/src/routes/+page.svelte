<script lang="ts">
	import { onMount } from 'svelte';
	import type { PageData } from './$types';
	import Widgets from '$lib/components/widgets.svelte';

	let { data }: { data: PageData } = $props();

	onMount(() => {
		// console.log(data.widgetResponse);
		const wrapperDiv = document.getElementById('legacy-content');
		wrapperDiv!.innerHTML = data.widgetHTML + data.tabbedInfoHTML + data.tableHTML;
		// document.appendChild(wrapperDiv);
	});
</script>

<!-- <div class="container h-full mx-auto flex justify-center items-center"> -->
<div>
	<Widgets/>
	<!-- <div class="space-y-5">
		<h1 class="h1">This is the page</h1>
	</div> -->
</div>
