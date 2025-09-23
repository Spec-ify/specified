<!-- NOT YET IMPLEMENTED IN NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';
</script>

<!-- Temps -->
<Widget title="Temps" modalId="temps-modal">
	<div slot="values">
		<div class="widget-value">
			<div>🔥</div>
			<div>C</div>
		</div>
	</div>

	<div class="modal-body">
		<table id="temp-table" class="table">
			<thead>
				<tr>
					<th>Hardware</th>
					<th>Sensor</th>
					<th>Temperature</th>
				</tr>
			</thead>
		</table>
	</div>
</Widget>
