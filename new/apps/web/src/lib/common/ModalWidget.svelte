<!-- 
 A widget that expands into a modal view/pop up when clicked.
-->
<script lang="ts">
	import { Dialog, Portal } from '@skeletonlabs/skeleton-svelte';

	interface Widget {
		title: string,
		widgetContents: any,
		modalContents?: any,
		extraModalContents?: any,
		fullScreen?: any,
	}

	let {
		title = 'Modal',
		/** what's displayed when the widget is not expanded */
		widgetContents,
		/** what's displayed when the widget is in modal mode*/
		modalContents,
		/** "more info" contents*/
		extraModalContents,
		fullScreen
	}: Widget = $props();

	// TODO: support for "more info" is not currently
	// implemented. When it is, it should not make use
	// of IDs
	let moreInfoExpanded = $state(false);

	const widget = 'min-h-32 self-stretch max-w-96 basis-72 flex flex-col grow bg-surface-900 p-1 py-4 rounded-md',
		  h1 = 'font-normal text-[13pt] m-0 pt-1 pb-2 text-center text-surface-50',
		  contents = 'flex flex-col text-center flex-1 justify-center text-[1rem]',
		animation = 'transition transition-discrete translate-y-[50px] starting:data-[state=open]:opacity-0 starting:data-[state=open]:translate-y-[50px] data-[state=open]:opacity-100 data-[state=open]:translate-y-0 starting:data-[state=closed]:opacity-100 starting:data-[state=closed]:translate-y-0 data-[state=closed]:opacity-0 data-[state=closed]:translate-y-[50px] duration-150';
</script>

{#if !modalContents}
<div class="{widget}">
	<h1 class="{h1}">{title}</h1>
	<div class="{contents}">
			{@render widgetContents()}
	</div>
</div>
{:else}
<Dialog>
	<Dialog.Trigger class={widget}>
		<div class="flex flex-col h-full">
			<h1 class="{h1}">{title}</h1>
			<div class="{contents}">
					{@render widgetContents()}
			</div>
		</div>
	</Dialog.Trigger>
	<Portal>
		<Dialog.Backdrop class="fixed inset-0 z-50 bg-surface-50-950/50" />
		<Dialog.Positioner class="fixed inset-0 z-100 flex justify-center items-center p-8">
			<Dialog.Content class="flex flex-col card bg-surface-100-900 {fullScreen ? "w-full h-full max-h-[98%]" : "w-fit max-w-svw max-h-svh"} p-8 space-y-4 shadow-xl rounded-md {animation}">
				<header class="flex justify-between items-center">
					<Dialog.Title class="text-lg font-bold">{title}</Dialog.Title>
					<Dialog.CloseTrigger class="btn-icon hover:preset-tonal">
						X
					</Dialog.CloseTrigger>
				</header>

				<div class="max-h- overflow-auto">
					{@render modalContents()}

					<!-- more info -->	
					{#if extraModalContents && moreInfoExpanded}
						<div class="modal-body">
							{@render extraModalContents()}
						</div>
					{/if}
				</div>
				
				<footer class="flex justify-end gap-2">
					{#if extraModalContents && moreInfoExpanded == false}
						<button onclick={() => {moreInfoExpanded = true}} type="button" class="btn preset-filled">More Info</button>
					{/if}
					<Dialog.CloseTrigger class="btn preset-filled">Close</Dialog.CloseTrigger>
				</footer>
			</Dialog.Content>
		</Dialog.Positioner>
	</Portal>
</Dialog>
{/if}