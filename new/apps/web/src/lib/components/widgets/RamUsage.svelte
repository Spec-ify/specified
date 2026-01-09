<!-- NOT YET IMPLEMENTED IN NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';
	import { bytesToGigabytes } from '$lib/common/constants';

	interface RunningProcessInfo {
		ProcessName: string;
        Count: number;
        ExePath: string;
        Id: number;
        WorkingSet: number;
        CpuPercent: number;
	}

	interface RamInfo {
		DeviceLocation: string;
		BankLocator: string;
		Manufacturer: string;
		SerialNumber: string;
		PartNumber: string;
		ConfiguredSpeed: number;
		Capacity: number;
	}

	interface Props {
		runningProcesses: Array<RunningProcessInfo>;
		ram: Array<RamInfo>;
	}

	let {
		runningProcesses,
		ram
	}: Props = $props();

	let workingSet: number = 0;
	runningProcesses.forEach((process: any) => {
		workingSet += process.WorkingSet;
	});

	const ramUsed = Math.round((workingSet / bytesToGigabytes) * 100) / 100;
	
	let totalRam: number = 0;
	let ramUsedPercent: number = 0;

	if (ram) {
		ram.forEach((stick: any) => {
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