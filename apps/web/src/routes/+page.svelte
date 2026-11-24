<script lang="ts">
	import ManualUpload from '$lib/components/ManualUpload.svelte';
</script>

<svelte:head>
	<title>Specify - Report Viewer</title>
</svelte:head>

<div
	class="min-h-screen flex flex-col justify-between py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto"
>
	<div class="flex flex-col items-center gap-8">
		<!-- Header / Logo -->
		<header class="flex flex-col items-center text-center gap-3">
			<img src="/assets/logo.png" alt="Specify Logo" class="h-20 w-auto object-contain" />
			<h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Specify Report Viewer</h1>
			<p class="text-surface-400 max-w-md text-sm sm:text-base">
				Upload a Specify report JSON to inspect detailed hardware, system, and diagnostic
				metrics.
			</p>
		</header>

		<!-- Upload Box -->
		<main class="w-full">
			<ManualUpload />
		</main>

		<!-- Links / Info -->
		<section class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full max-w-xl mt-4">
			<a
				href="https://github.com/Spec-ify/specify/releases"
				target="_blank"
				rel="noreferrer"
				class="flex items-center gap-3 p-4 rounded-xl border border-surface-400/30 hover:border-surface-400 bg-surface-800/10 hover:bg-surface-800/30 transition-colors"
			>
				<img src="/assets/dl.png" alt="Downloads" class="h-9 w-9 object-contain" />
				<div>
					<h3 class="font-semibold text-sm">Download Specify</h3>
					<p class="text-xs text-surface-400">Get the latest Specify diagnostic tool</p>
				</div>
			</a>

			<a
				href="https://rtech.support/discord"
				target="_blank"
				rel="noreferrer"
				class="flex items-center gap-3 p-4 rounded-xl border border-surface-400/30 hover:border-surface-400 bg-surface-800/10 hover:bg-surface-800/30 transition-colors"
			>
				<div
					class="h-9 w-9 flex items-center justify-center rounded-lg bg-primary-500/10 text-primary-400 font-bold"
				>
					#
				</div>
				<div>
					<h3 class="font-semibold text-sm">Community Discord</h3>
					<p class="text-xs text-surface-400">Get support & chat on r/techsupport</p>
				</div>
			</a>
		</section>
	</div>

	<!-- Footer -->
	<footer class="mt-16 text-center text-xs text-surface-500">
		<p>
			Specify is open-source. Learn more on
			<a
				href="https://github.com/Spec-ify"
				target="_blank"
				rel="noreferrer"
				class="underline hover:text-surface-300"
			>
				GitHub
			</a>.
		</p>
	</footer>
</div>
