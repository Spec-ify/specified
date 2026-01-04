<!-- NOT YET MIGRATED TO NEW WIDGET SYSTEM -->

<script lang="ts">
	import { onMount } from 'svelte';
	import Widget from '../../common/ModalWidget.svelte';

	interface CpuInfo {
		CurrentClockSpeed: number;
		LoadPercentage: number;
		Manufacturer: string;
		Name: string;
		// Appears to be a typo in the schema
		NumberOfEnabledCore: number;
		SocketDesignation: string;
		ThreadCount: number;
	}

	interface Props {
		cpu: CpuInfo;
		cpuMoreInfo: Promise<any>;
	}

	let {
		cpu,
		cpuMoreInfo
	}: Props = $props();

</script>

<!-- CPU -->
<Widget title="CPU">
	{#snippet widgetContents()}
		<span>
			{cpu.Name}
		</span>
		<div>Callsign</div>
	{/snippet}

	{#snippet modalContents()}
		<table class="table">
			<tbody>
				<tr>
					<td>Name</td>
					<td>{cpu.Name}</td>
				</tr>
				<tr>
					<td>Manufacturer</td>
					<td>{cpu.Manufacturer}</td>
				</tr>
				<tr>
					<td>Socket Designation</td>
					<td>{cpu.SocketDesignation}</td>
				</tr>
				<tr>
					<td>Current Clock Speed</td>
					<td>{cpu.CurrentClockSpeed}</td>
				</tr>
				<tr>
					<td># of Enabled Cores</td>
					<td>{cpu.NumberOfEnabledCore}</td>
				</tr>
				<tr>
					<td>Thread Count</td>
					<td>{cpu.ThreadCount}</td>
				</tr>
			</tbody>
		</table>
	{/snippet}

	{#snippet extraModalContents()}
		{#await cpuMoreInfo}
			<h6 class="modal-title">Database results for: ...</h6>
		{:then reposnse}
			<h6 class="modal-title">Database results for: {reposnse.name}</h6>
			<table class="table">
				<tbody>
					{#each Object.entries(reposnse.attributes) as [key, value]}
						<tr>
							<td>{key}</td>
							<td>{value}</td>
						</tr>
					{/each}
				</tbody>
			</table>
		{/await}
	{/snippet}

</Widget>

<style>
	span {
		color: var(--color-secondary-50);
	}

	div {
		color: var(--color-surface-300);
		font-size: 13pt;
	}
</style>