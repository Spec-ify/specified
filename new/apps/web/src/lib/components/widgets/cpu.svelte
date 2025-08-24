<script lang="ts">
	import { onMount } from 'svelte';
	import Widget from '../../common/ModalWidget.svelte';

	export let cpuData: any;

	async function cpuLookup() {
		/**
		 * @type string
		 */
		const cpuName: string = cpuData.Name;

		let response;
		if (window.location.host.startsWith('localhost')) {
			console.info('Trying local server for hwapi');
			try {
				response = await (
					await fetch(
						`http://localhost:3000/api/cpus/?name=${encodeURIComponent(cpuName)}`,
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
				await fetch(`https://spec-ify.com/api/cpus/?name=${encodeURIComponent(cpuName)}`, {
					method: 'GET',
					mode: 'cors'
				})
			).json();
		}
		// update the title element to reflect the name fetched from the database
		let infoTitle = document.getElementById('cpu-info-title');
		if (infoTitle) {
			infoTitle.innerHTML = infoTitle.innerHTML.slice(0, -3) + response.name;
		}

		let tableContents = '';
		// add new elements to the table for every kv in the database
		for (const [key, value] of Object.entries(response.attributes)) {
			tableContents += `<tr><td>${key}</td><td>${value}</td></tr>`;
		}
		// cpuTable.innerHTML = tableContents;
		let fetchedTitle = document.getElementById('fetched-cpu-info');
		if (fetchedTitle) {
			fetchedTitle.innerHTML = tableContents;
		}
	}

	onMount(() => {
		const observer = new IntersectionObserver((entries) => {
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
<Widget title="CPU" modalId="cpu-modal">
	<div slot="values">
		<div class="green">
			{cpuData.Name}
		</div>
		<div>Callsign</div>
	</div>

	<table slot="modal-body" class="table">
		<tbody>
			<tr>
				<td>Name</td>
				<td>{cpuData.Name}</td>
			</tr>
			<tr>
				<td>Manufacturer</td>
				<td>{cpuData.Manufacturer}</td>
			</tr>
			<tr>
				<td>Socket Designation</td>
				<td>{cpuData.SocketDesignation}</td>
			</tr>
			<tr>
				<td>Current Clock Speed</td>
				<td>{cpuData.CurrentClockSpeed}</td>
			</tr>
			<tr>
				<td># of Enabled Cores</td>
				<td>{cpuData.NumberOfEnabledCore}</td>
			</tr>
			<tr>
				<td>Thread Count</td>
				<td>{cpuData.ThreadCount}</td>
			</tr>
		</tbody>
	</table>

	<div slot="extras" class="modal-body" id="cpu-modal-info-table" style="display:none;">
		<!-- This content is populated javascript side -->
		<h6 class="modal-title" id="cpu-info-title">Database results for: ...</h6>
		<table class="table">
			<tbody id="fetched-cpu-info"> </tbody>
		</table>
	</div>
</Widget>
