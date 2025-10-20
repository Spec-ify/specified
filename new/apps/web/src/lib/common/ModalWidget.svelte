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
	<div class="widget-values">
			{@render widgetContents()}
	</div>
</button>

{#if expanded}
<span class="backdrop" onclick={() => {expanded = false}}>
</span>
<div class="modal">
			<!-- modal header -->
			<div>
				<h5>{title}</h5>
				<button onclick={() => {expanded = false;}} type="button" aria-label="Close"
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
				<div>
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
						type="button">Close</button
					>
				</div>
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

.widget h1 {
	font-size: 13pt;
	font-weight: 400;
	margin: 0;
	padding-top: 5px;
	padding-bottom: 8px;
	text-align: center;
}

.widget {
	cursor: pointer;
	/* TODO: rem-ify */
	width: 260px;
	max-width: 340px;
	flex-grow: 1;
	padding: 2px 5px;

	background-color: var(--color-surface-900);
	border-radius: 6px;
	color: var(--base-font-color-dark);
}

.backdrop {
	z-index: 1054;
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: #00000099;
}

.modal {
	/* pending removal of bootstrap */
	display: inline !important;
	position: absolute;
	width: auto;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50);
	max-width: 500px;
	z-index: 1055;
	background-color: var(--color-surface-900);
}
</style>