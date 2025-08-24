<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	export let cpuloadData;
</script>

<!-- CPU Usage -->
<Widget title="CPU Usage" type="" modalId="cpu-usage-modal">
	<div slot="values">
		<div class="widget-value">
			<div class="widget-value">
				<span class="green">
					{cpuloadData ?? '--'}%
					<!-- <?= $json_data['Hardware']['Cpu']['LoadPercentage'] ?? '--' ?>% -->
				</span>
			</div>
		</div>
	</div>
</Widget>
