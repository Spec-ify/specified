<script lang="ts">
	import { goto } from '$app/navigation';
	import { resolve } from '$app/paths';

	let isDragging = $state(false);
	let isUploading = $state(false);
	let errorMessage = $state<string | null>(null);
	let fileInput = $state<HTMLInputElement | null>(null);

	function onDragOver(e: DragEvent) {
		e.preventDefault();
		isDragging = true;
	}

	function onDragLeave(e: DragEvent) {
		e.preventDefault();
		isDragging = false;
	}

	function onDrop(e: DragEvent) {
		e.preventDefault();
		isDragging = false;
		if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
			handleFile(e.dataTransfer.files[0]);
		}
	}

	function onFileInputChange(e: Event) {
		const target = e.target as HTMLInputElement;
		if (target.files && target.files.length > 0) {
			handleFile(target.files[0]);
		}
	}

	async function handleFile(file: File) {
		errorMessage = null;

		if (!file.name.toLowerCase().endsWith('.json') && file.type !== 'application/json') {
			errorMessage = 'Only Specify .json files are accepted.';
			return;
		}

		if (file.size > 10 * 1024 * 1024) {
			errorMessage = 'File exceeds the 10MB limit.';
			return;
		}

		isUploading = true;

		try {
			const text = await file.text();
			let parsed: unknown;
			try {
				parsed = JSON.parse(text);
			} catch {
				errorMessage = 'File contains invalid JSON.';
				isUploading = false;
				return;
			}

			const response = await fetch('/upload', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(parsed)
			});

			const result = await response.json().catch(() => null);

			if (!response.ok) {
				errorMessage = result?.error || `Upload failed with status ${response.status}`;
				isUploading = false;
				return;
			}

			const targetSlug = result?.slug || result?.id;
			if (targetSlug) {
				await goto(resolve('/viewer/[slug]', { slug: targetSlug }));
			} else {
				errorMessage = 'Upload succeeded but no report slug was returned.';
				isUploading = false;
			}
		} catch (err: unknown) {
			errorMessage =
				err instanceof Error ? err.message : 'An error occurred while uploading.';
			isUploading = false;
		}
	}
</script>

<div class="flex flex-col items-center w-full max-w-xl mx-auto">
	<button
		type="button"
		class="w-full flex flex-col items-center justify-center p-8 border-2 border-dashed rounded-xl transition-all cursor-pointer select-none text-left {isDragging
			? 'border-primary-500 bg-primary-500/10'
			: 'border-surface-400/50 hover:border-surface-400 bg-surface-800/20'}"
		ondragover={onDragOver}
		ondragleave={onDragLeave}
		ondrop={onDrop}
		onclick={() => fileInput?.click()}
		onkeydown={(e) => {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				fileInput?.click();
			}
		}}
		aria-label="Upload specify json report"
	>
		<input
			bind:this={fileInput}
			type="file"
			accept=".json,application/json"
			class="hidden"
			onchange={onFileInputChange}
		/>

		{#if isUploading}
			<div class="flex flex-col items-center gap-3">
				<div
					class="w-8 h-8 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"
				></div>
				<p class="text-sm font-medium">Processing & uploading report...</p>
			</div>
		{:else}
			<div class="flex flex-col items-center gap-2 text-center pointer-events-none">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					class="w-12 h-12 text-surface-400 mb-1"
					fill="none"
					viewBox="0 0 24 24"
					stroke="currentColor"
					stroke-width="1.5"
				>
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
					/>
				</svg>
				<p class="text-lg font-semibold">
					{isDragging
						? 'Drop it here!'
						: 'Drag & drop a JSON file here, or click to browse'}
				</p>
				<p class="text-xs text-surface-400">
					Only valid <code>Specify .json</code> reports up to 10MB are supported
				</p>
			</div>
		{/if}
	</button>

	{#if errorMessage}
		<div
			class="mt-4 p-3 w-full rounded-lg bg-error-500/10 border border-error-500/30 text-error-400 text-sm flex items-center gap-2"
		>
			<svg
				xmlns="http://www.w3.org/2000/svg"
				class="w-5 h-5 shrink-0"
				viewBox="0 0 20 20"
				fill="currentColor"
			>
				<path
					fill-rule="evenodd"
					d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
					clip-rule="evenodd"
				/>
			</svg>
			<span>{errorMessage}</span>
		</div>
	{/if}
</div>
