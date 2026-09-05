<script lang="ts">
	import type { RamModule } from '$lib/common/report/hardware';
	import type { PageFile } from '$lib/common/report/system';

	import Widget from '../../common/ModalWidget.svelte';

	interface Props {
		ram: Array<RamModule>;
		pagefile: PageFile;
	}

	let {
		ram,
		pagefile
	}: Props = $props();

	let gridcols = `grid grid-rows-${Math.floor(((ram.length - 1) / 4) + 1)} grid-cols-${ram.length > 4 ? 4 : ((ram.length - 1) % 4) + 1} gap-x-${ram.length > 4 ? 0.5 : 4 - (ram.length - 1 % 4) + 1} w-full text-center`;

	console.log(gridcols);
</script>

<!-- RAM -->

<Widget title="Memory">

	{#snippet widgetContents()}
		<div class={gridcols}>
			{#each ram as ramStick, i}

				{#if ramStick.Capacity > 0}
					<div>
						<span class="widget-cap">{Math.floor(ramStick.Capacity / 1000)} GB</span>
						<div>DIMM {i+1}</div>
					</div>
				{:else}
					<div>
						<span style="color: rgb(215,27,27);">--</span>
						<div>DIMM {i+1}</div>
					</div>
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
	}
</style>