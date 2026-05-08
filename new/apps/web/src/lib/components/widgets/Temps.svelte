<script lang="ts">
	import type { TemperatureSensor } from '$lib/common/report/hardware';

	import Widget from '../../common/ModalWidget.svelte';

	interface Props {
		temps: Array<TemperatureSensor>;
	}

	let {
		temps
	}: Props = $props();

</script>

<!-- Temps -->
<Widget title="Temps">
	{#snippet widgetContents()}
		<div class="widget-value">
			<div>🔥</div>
			<div>C</div>
		</div>
	{/snippet}

	{#snippet modalContents()}
		<table id="temp-table" class="table">
			<thead>
				<tr>
					<th>Hardware</th>
					<th>Sensor</th>
					<th>Temperature</th>
				</tr>
			</thead>
			<tbody>
				{#each temps as sensor}
					<tr>
						<td>{sensor.Hardware}</td>
						<td>{sensor.SensorName}</td>
						<td>{sensor.SensorValue}</td>
					</tr>
				{/each}
			</tbody>
		</table>
	{/snippet}
</Widget>
