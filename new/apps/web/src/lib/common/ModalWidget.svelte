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

<button
	onclick={() => {
			expanded = true;
		}}
	class="widget">
	<h1>{title}</h1>
	<div  class="widget-values">
			{@render widgetContents()}
	</div>
</button>

{#if expanded}
<span class="backdrop">
</span>
<div tabindex="-1" aria-labelledby={title}>
		<div class="modal-content">
			<!-- modal header -->
			<div class="modal-header">
				<h5 class="modal-title">{title}</h5>
				<button onclick={() => {expanded = false;}} type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"
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
						onclick={() => {expanded = false;}}
						type="button"
						class="btn btn-secondary"
						data-mdb-dismiss="modal">Close</button
					>
				</div>
			</div>
		<!-- </div> -->
	</div>
{/if}
<style>
.widget-values {
	display: flex;
	flex-direction: column;
	flex-grow: 1;
	text-align: center;
	font-size: 16pt;
}

.widget {
	cursor: pointer;
	/* TODO: rem-ify */
	width: 260px;
	max-width: 340px;
	flex-grow: 1;
	padding: 2px 5px;

	background-color: var(--color-surface-950);
	border-radius: 6px;
	color: var(--base-font-color-dark);
}
</style>