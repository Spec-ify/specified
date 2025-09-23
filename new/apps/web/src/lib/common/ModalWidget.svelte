<!-- 
 A widget that expands into a modal view/pop up when clicked. 
-->
<script lang="ts">
	import { onMount } from "svelte";


	let {
		title = 'Modal',
		modalId = 'widget-modal',
		modalSpecial = '',
		widgetContents,
		modalContents,
	} = $props();
	// TODO: support for "more info" is not currently
	// implemented. When it is, it should not make use
	// of IDs
	function infoClick(id: String) {
		let element = document.getElementById(id + '-info-table');
		if (element) {
			element.style.display = 'block';
		}

		let button = document.getElementById(id + '-more-info-button');
		if (button) {
			button.style.display = 'none';
		}
	}
</script>

<div
	class={'widget hover widget-' + modalId}
	data-mdb-toggle="modal"
	data-mdb-target={'#' + modalId}
>
	<h1>{title}</h1>
	<div class="widget-values">
			{@render widgetContents()}
	</div>
</div>

<div class="modal fade" id={modalId} tabindex="-1" aria-labelledby={modalId} aria-hidden="true">
	<div class={'modal-dialog ' + modalSpecial}>
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal-label">{title}</h5>
				<button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"
				></button>
			</div>
			<div class="modal-body">
				{@render modalContents()}
			</div>
			<!-- <div class="modal-body">
				{@render extraModalContents()}
			</div> -->
			<div class="modal-footer">
				<!-- {#if extraModalContents}
					<button
						type="button"
						class="btn btn-secondary"
						id={modalId + '-more-info-button'}
						onclick={() => infoClick(modalId)}>More Info</button
					>
				{/if} -->
				<button
					type="button"
					class="btn btn-secondary"
					id={modalId + '-close-button'}
					data-mdb-dismiss="modal">Close</button
				>
			</div>
		</div>
	</div>
</div>

<style>
.widget-values div {
	display: flex;
	flex-direction: column;
	flex-grow: 1;
	text-align: center;
}
</style>