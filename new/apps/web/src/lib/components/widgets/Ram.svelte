<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	interface RamInfo {
		DeviceLocation: string;
		BankLocator: string;
		Manufacturer: string;
		SerialNumber: string;
		PartNumber: string;
		ConfiguredSpeed: number;
		Capacity: number;
	}

	interface PagefileInfo {
		AllocatedBaseSize: number;
		Caption: string;
		CurrentUsage: number;
		PeakUsage: number;
	}

	interface Props {
		ram: Array<RamInfo>;
		pagefile: PagefileInfo;
	}

	let {
		ram,
		pagefile
	}: Props = $props();

	const flexBasis: string = `${100 / (Object.keys(ram).length % 4 ? Object.keys(ram).length % 4 : 4)}%`;

</script>

<!-- RAM -->

<Widget title="Memory">

	{#snippet widgetContents()}
		<div class="flex-container">
			{#each ram as ramStick, i}

				{#if ramStick.Capacity > 0}
					<div style="flex: 1 1 {flexBasis};">
						<span>{Math.floor(ramStick.Capacity / 1000)} GB</span>
						<div>DIMM {i+1}</div>
					</div>
				{:else}
					<div style="flex: 1 1 {flexBasis};">
						<span style="color: rgb(215,27,27);">--</span>
						<div>DIMM {i+1}</div>
					</div>
				{/if}

				<!-- 
					Checks if report has more than 4 RAM Modules, 
					and if each block has reached the 4th module in this row
				-->
				{#if Object.keys(ram).length > 4 && (i + 1) % 4 == 0}
					<div style="flex-basis: 100%;"></div>
				{/if}
			{/each}
		</div>
	{/snippet}

	{#snippet modalContents()}
		<h5>Physical Memory</h5>
		<table class="table">
			<thead>
				<tr>
					<th scope="col">DIMM</th>
					<th scope="col">Manufacturer</th>
					<th scope="col">Model</th>
					<th scope="col">Speed</th>
					<th scope="col">Capacity</th>
				</tr>
			</thead>
			<tbody>
				{#each ram as ramStick}
					{#if ramStick['Capacity'] <= 0}
						<tr>
							<td>{ramStick['DeviceLocation']}</td>
							<td colspan="4" class="td-center">Not Detected</td>
						</tr>
					{:else}
						<tr>
							<td>{ramStick['DeviceLocation']}</td>
							<td>{ramStick['Manufacturer']}</td>
							<td>{ramStick['PartNumber']}</td>
							<td>{ramStick['ConfiguredSpeed']} MHz</td>
							<td>{ramStick['Capacity']} MB</td>
						</tr>
					{/if}
				{/each}
			</tbody>
		</table>
		<h5>Pagefile</h5>
		<table class="table">
			<tbody>
				<tr>
					<td>File Path</td>
					<td>{pagefile.Caption}</td>
				</tr>
				<tr>
					<td>Allocated Base Size</td>
					<td>{pagefile.AllocatedBaseSize} MB</td>
				</tr>
				<tr>
					<td>Current Usage</td>
					<td>{pagefile.CurrentUsage} MB</td>
				</tr>
				<tr>
					<td>Peak Usage</td>
					<td>{pagefile.PeakUsage} MB</td>
				</tr>
			</tbody>
		</table>
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

	.flex-container {
		display: flex;
		flex-flow: row wrap;
		max-width: inherit;
	}
</style>