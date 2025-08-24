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

</script>

<!-- Audio Devices -->
<Widget title="Audio Devices" modalId="audio-modal">
	{#snippet widgetContents()}
		<!-- <?php
            $internalaudio = 0;
            $externalaudio = 0;
            foreach ($json_data['Hardware']['AudioDevices'] as $audioDevice) {
                if (str_contains(strtolower($audioDevice['DeviceID']), 'hdaudio')) {
                    $internalaudio = $internalaudio + 1;
                } else {
                    $externalaudio = $externalaudio + 1;
                };
            }
            echo '<div class="widget-value green">Internal : ' . $internalaudio . '</div>';
            echo '<div class="widget-value yellow">External : ' . $externalaudio . '</div>';
        ?> -->
	{/snippet}

	{#snippet modalContents()}
	<div class="modal-body">
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
	</div>
	{/snippet}
</Widget>
