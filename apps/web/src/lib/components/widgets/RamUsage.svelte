<!-- NOT YET IMPLEMENTED IN NEW WIDGET SYSTEM -->
<script lang="ts">
	import type { RunningProcess } from '$lib/common/report/system';
	import type { RamModule } from '$lib/common/report/hardware';

	import Widget from '../../common/ModalWidget.svelte';
	import { bytesToGigabytes } from '$lib/common/constants';

	interface Props {
		runningProcesses: Array<RunningProcess>;
		ram: Array<RamModule>;
	}

	let {
		runningProcesses,
		ram
	}: Props = $props();

	let workingSet: number = 0;
	runningProcesses.forEach((process: RunningProcess) => {
		workingSet += process.WorkingSet;
	});

	const ramUsed = Math.round((workingSet / bytesToGigabytes) * 100) / 100;
	
	let totalRam: number = 0;
	let ramUsedPercent: number = 0;

	if (ram) {
		ram.forEach((stick: RamModule) => {
			let capacity = stick.Capacity;
			if (capacity > 0) {
				totalRam += Math.floor(capacity / 1024);
			}
		});

		ramUsedPercent = Math.round((ramUsed / totalRam) * 100);
	}
</script>

<!-- RAM Usage -->
<Widget title="Memory Usage">
	{#snippet widgetContents()}
		<span class="green">
			{ramUsed} GB / {totalRam} GB
		</span>
		<div>
			{ramUsedPercent}%
		</div>
	{/snippet}
</Widget>

<style>
	.green {
		color: var(--color-secondary-50);
	}

	div {
		color: var(--color-surface-300);
		font-size: 13pt;
	}

	.flex-container {
		display: flex;
		flex-flow: row wrap;
		max-width: inherit;
	}
</style>