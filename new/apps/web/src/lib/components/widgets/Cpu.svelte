<!-- NOT YET MIGRATED TO NEW WIDGET SYSTEM -->

<script lang="ts">
	import { onMount } from 'svelte';
	import { dev } from '$app/environment';
	import Widget from '../../common/ModalWidget.svelte';

	interface CpuInfo {
		CurrentClockSpeed: number;
		LoadPercentage: number;
		Manufacturer: string;
		Name: string;
		// Appears to be a typo in the schema
		NumberOfEnabledCore: number;
		SocketDesignation: string;
		ThreadCount: number;
	}

	interface Props {
		cpu: CpuInfo;
	}

	let {
		cpu
	}: Props = $props();

	let response: any = $state();
	let fetched: boolean = $state(false);
	let cpuName: string = $state('...');

	async function cpuLookup() {
		if (dev) {
			console.info('Trying local server for hwapi');
			try {
				response = await (
					await fetch(
						`http://localhost:3000/api/cpus/?name=${encodeURIComponent(cpu.Name)}`,
						{
							method: 'GET',
							mode: 'cors'
						}
					)
				).json();
			} catch (e) {
				console.warn(
					'Could not connect to local hwapi instance, falling back to spec-ify.com'
				);
			}
		}
		if (!response) {
			response = await (
				await fetch(`https://spec-ify.com/api/cpus/?name=${encodeURIComponent(cpu.Name)}`, {
					method: 'GET',
					mode: 'cors'
				})
			).json();
		}

		if (response){
			fetched = true;
			cpuName = response.name;
		}
	}

	onMount(() => {
		cpuLookup().then();
		const  observer = new IntersectionObserver((entries) => {
			if (entries[0].isIntersecting) {
				cpuLookup().then();
				unobserve();
			}
		});

		let cpuDatabase = document.getElementById('cpu-info-title');
		if (cpuDatabase) {
			observer.observe(cpuDatabase);
		}

		function unobserve() {
			if (cpuDatabase) {
				observer.unobserve(cpuDatabase);
			}
		}
	});
</script>

<!-- CPU -->
<Widget title="CPU">
	{#snippet widgetContents()}
		<span>
			{cpu.Name}
		</span>
		<div>Callsign</div>
	{/snippet}

	{#snippet modalContents()}
		<table class="table">
			<tbody>
				<tr>
					<td>Name</td>
					<td>{cpu.Name}</td>
				</tr>
				<tr>
					<td>Manufacturer</td>
					<td>{cpu.Manufacturer}</td>
				</tr>
				<tr>
					<td>Socket Designation</td>
					<td>{cpu.SocketDesignation}</td>
				</tr>
				<tr>
					<td>Current Clock Speed</td>
					<td>{cpu.CurrentClockSpeed}</td>
				</tr>
				<tr>
					<td># of Enabled Cores</td>
					<td>{cpu.NumberOfEnabledCore}</td>
				</tr>
				<tr>
					<td>Thread Count</td>
					<td>{cpu.ThreadCount}</td>
				</tr>
			</tbody>
		</table>
	{/snippet}

	{#snippet extraModalContents()}
		<h6 class="modal-title" id="cpu-info-title">Database results for: {cpuName}</h6>
		<table class="table">
			<tbody id="fetched-cpu-info">
				{#if fetched}
					{#each Object.entries(response.attributes) as [key, value]}
						<tr>
							<td>{key}</td>
							<td>{value}</td>
						</tr>
					{/each}
				{/if}
			</tbody>
		</table>
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