<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	interface AudioDevice {
		DeviceID: string,
		Manufacturer: string,
		Name: string,
		Status: string,
	};
	
	interface Props {
		audioDevices: AudioDevice[]
	}

	let {
		audioDevices
	}: Props = $props();

	const numInternal = audioDevices.filter(
		d => d.DeviceID.toLowerCase().includes("hdaudio")
		).length;
	const numExternal = audioDevices.length - numInternal;
</script>
<!-- Audio Devices -->
<Widget title="Audio Devices" modalId="audio-modal">
	{#snippet widgetContents()}
		<div class="widget-contents">
			<p class="green">Internal : {numInternal}</p>
			<p class="yellow">External : {numExternal}</p>
		</div>
	<style>
	</style>
	{/snippet}
	{#snippet modalContents()}
	<table id="audio-table" class="table">
		<thead>
			<tr>
				<th>Device ID</th>
				<th>Manufacturer</th>
				<th>Name</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			{#each audioDevices as audioDevice}
				<tr>
					<td>{audioDevice.DeviceID}</td>
					<td>{audioDevice.Manufacturer}</td>
					<td>{audioDevice.Name}</td>
					<td>{audioDevice.Status}</td>
				</tr>
			{/each}
		</tbody>
	</table>
	{/snippet}
</Widget>

<style>
	/* TODO: fix table overflow */
		.widget-contents {
			display: flex;
			justify-content: space-around;
			width: 100%;
			font-size: 1.5rem;
		}
		
		table {
			width: 100%;
		}

		td {
			white-space: nowrap !important;
			overflow: hidden;
		}
</style>