<script lang="ts">
	import type { Gpu, Monitor } from '$lib/common/report/hardware';
	
	import Widget from '../../common/ModalWidget.svelte';
	import { bytesToMegabytes } from '$lib/common/constants';
	
	interface Props {
		gpus: Array<Gpu>;
		monitors: Array<Monitor>;
	}

	let {
		gpus,
		monitors
	}: Props = $props();
</script>

<!-- GPU -->

<Widget title="GPU">
	{#snippet widgetContents()}
		<div class="widget-value">
			<span>
				{#if !monitors}
					{gpus[0]['Description']}
				{:else}
					{monitors[0]['Name']}
				{/if}
			</span>
			<div>Model</div>
		</div>
	{/snippet}

	{#snippet modalContents()}
		<div class="modal-body">
			{#if gpus !== null}
				<h5>GPU Info</h5>
				<table class="table">
					<thead>
						<tr>
							<th scope="col">Name</th>
							<th scope="col">VRAM</th>
							<th scope="col">Resolution</th>
							<th scope="col">Refresh Rate</th>
						</tr>
					</thead>
					<tbody>
						{#each Object.values(gpus) as gpu}
							<tr>
								<td>{gpu.Description}</td>
								<td>{Number(gpu.AdapterRAM) / bytesToMegabytes} MB</td>
								<td>{gpu.CurrentHorizontalResolution} x {gpu.CurrentVerticalResolution}</td>
								<td>{gpu.CurrentRefreshRate} Hz</td>
							</tr>
						{/each}
					</tbody>
				</table>
			{/if}

			{#if monitors !== null}
				<h5>Monitor Info</h5>
				<table class="table">
					<thead>
						<tr>
							<th scope="col">Name</th>
							<th scope="col">VRAM</th>
							<th scope="col">Mode</th>
							<th scope="col">Monitor</th>
							<th scope="col">Connection</th>
						</tr>
					</thead>
					<tbody>
						{#each monitors as monitor}
							<tr>
								<td>{monitor.Name}</td>
								<td>{monitor.DedicatedMemory} MB</td>
								<td>{monitor.CurrentMode}</td>
								<td>{monitor.MonitorModel} Hz</td>
								<td>{monitor.ConnectionType}</td>
							</tr>
						{/each}
					</tbody>
				</table>
			{/if}
		</div>
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