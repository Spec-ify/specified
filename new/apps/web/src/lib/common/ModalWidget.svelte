<!-- 
 A widget that expands into a modal view/pop up when clicked.
-->
<script lang="ts">

	let {
		title = 'Modal',
		modalSpecial = '',
		/** what's displayed when the widget is not expanded */
		widgetContents,
		/** what's displayed when the widget is in modal mode*/
		modalContents,
	} = $props();

	// TODO: support for "more info" is not currently
	// implemented. When it is, it should not make use
	// of IDs
	let expanded = $state(false);
</script>

<div
	onclick={() => {expanded = true;}}
	class={'widget hover'}
	data-mdb-toggle="modal"
>
	<h1>{title}</h1>
	<div  class="widget-values">
			{@render widgetContents()}
	</div>
</div>

{#if expanded}
	<div class="modal fade" tabindex="-1" aria-labelledby={title} aria-hidden="true">
		<div class={'modal-dialog ' + modalSpecial}>
			<div class="modal-content">
				<!-- modal header -->
				<div class="modal-header">
					<h5 class="modal-title">{title}</h5>
					<button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"
					></button>
				</div>

				<div class="modal-body">
					{@render modalContents()}
				</div>

				<!-- more info -->
				<!-- <div class="modal-body">
					{@render extraModalContents()}
				</div> -->

				<!-- footer -->
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
						data-mdb-dismiss="modal">Close</button
					>
				</div>
			</div>
		</div>
	</div>
{/if}
<style>
.widget-values div {
	display: flex;
	flex-direction: column;
	flex-grow: 1;
	text-align: center;
	font-size: 16pt;
}

.widget {
	cursor: pointer;

}
</style>