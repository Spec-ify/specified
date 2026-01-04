<!-- NOT YET MIGRATED TO NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	interface GpuInfo {
		AdapterRAM: number;
		CurrentBitsPerPixel: number;
		CurrentHorizontalResolution: number;
		CurrentRefreshRate: number;
		CurrentVerticalResolution: number;
		Description: string;
		DriverDate: string;
		DriverVersion: string;
	}

	interface MonitorInfo {
		Source: string;
		Name: string;
		ChipType: string;
		DedicatedMemory: string;
		MonitorModel: string;
		CurrentMode: string;
		ConnectionType: string;
	}

	interface Props {
		gpus: Array<GpuInfo>;
		monitors: Array<MonitorInfo>;
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
			{#if !(gpus == null)}
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
								<td>{gpu.AdapterRAM / 1048576} MB</td>
								<td>{gpu.CurrentHorizontalResolution} x {gpu.CurrentVerticalResolution}</td>
								<td>{gpu.CurrentRefreshRate} Hz</td>
							</tr>
						{/each}
					</tbody>
				</table>
			{/if}

			{#if !(monitors == null)}
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
						{#each Object.values(monitors) as monitor}
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