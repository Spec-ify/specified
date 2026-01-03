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
		/** "more info" contents*/
		extraModalContents,
	} = $props();

	// TODO: support for "more info" is not currently
	// implemented. When it is, it should not make use
	// of IDs
	let modalExpanded = $state(false);
	let moreInfoExpanded = $state(false);
</script>

<button
	onclick={() => {
			modalExpanded = true;
		}}
	class="_widget">
	<h1>{title}</h1>
	<div class="widget-values">
			{@render widgetContents()}
	</div>
</button>

{#if modalExpanded}
<span class="backdrop" onclick={() => {modalExpanded = false}} role="none">
</span>
<div class="_modal">
	<!-- modal header -->
	<div>
		<h5>{title}</h5>
		<button onclick={() => {modalExpanded = false;}} type="button" aria-label="Close"
		></button>
	</div>

	<div class="modal-body">
		{@render modalContents()}
	</div>

	<!-- more info -->	
	{#if extraModalContents && moreInfoExpanded == true}
		<div class="modal-body">
			{@render extraModalContents()}
		</div>
	{/if}

	<!-- footer -->
	<div>
		{#if extraModalContents && moreInfoExpanded == false}
			<button
				type="button"
				class="btn btn-secondary"
				onclick={() => {moreInfoExpanded = true}}>More Info</button
			>
		{/if}
		<button
			onclick={() => {modalExpanded = false;}}
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

/*
Underscore needed to stop bootstrap from interfering with css
can be removed when bootstrap is
*/
._widget {
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

._widget h1 {
	font-size: 13pt;
	font-weight: 400;
	margin: 0;
	padding-top: 5px;
	padding-bottom: 8px;
	text-align: center;
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

._modal {
	/* pending removal of bootstrap */
	max-width: 500px;
	z-index: 1055;
	background-color: var(--color-surface-900);
	color: var(--base-font-color-dark);
}
</style>