<!-- NOT YET MIGRATED TO NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	interface Props {
		cpuLoad: number;
	}

	let {
		cpuLoad,
	}: Props = $props();
</script>

<!-- CPU Usage -->
<Widget title="CPU Usage">
	{#snippet widgetContents()}
		<div class="widget-value">
			<span class="green">
				{cpuLoad ?? '--'}%
			</span>
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