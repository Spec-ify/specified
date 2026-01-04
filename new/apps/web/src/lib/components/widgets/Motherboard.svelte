<!-- NOT YET IMPLEMENTED IN NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';
	import { lists } from '$lib/common/lists';

	interface motherboardInfo {
		Manufacturer: string;
		Product: string;
		SerialNumber: string;
	}

	interface tpmInfo {
		IsActivated_InitialValue: boolean,
		IsEnabled_InitialValue: boolean,
		IsOwned_InitialValue: boolean,
		ManufacturerId: number,
		ManufacturerIdTxt: string,
		ManufacturerVersion: string,
		ManufacturerVersionFull20: string,
		ManufacturerVersionInfo: string,
		PhysicalPresenceVersionInfo: string,
		SpecVersion: string,
		IsPresent: boolean
	}
	
	interface Props {
		tpm: tpmInfo;
		motherboard: motherboardInfo;
		bios: Array<any>;
	}

	let {
		tpm,
		motherboard,
		bios
	}: Props = $props();

	let tpmStatus = 'Disabled';
	let tpmManufacturer: string, tpmVersion: string;

	if (tpm && tpm.IsEnabled_InitialValue) {
		tpmStatus = 'Enabled';
		tpmManufacturer = `${tpm.ManufacturerVersionInfo} ${tpm.ManufacturerVersion}`;
		tpmVersion = `${tpm.SpecVersion}`;
	}

	function dateConversion(date: string) {
		const input = date.match(/^(\d{4})(\d{2})(\d{2})/);

		if (!input) {
			return 'Unknown';
		}

		const [, year, month, day] = input;
		const formatted = `${month}/${day}/${year}`;

		return formatted;
	}

	function filterBiosCharacteristics(rawList: [string, any]) {
		let finalCharac = [''];
		const characList = lists['biosCharacteristicsList'];

		rawList.forEach((key: number) => {
			finalCharac.push(characList[key]);
		});

		return finalCharac.filter((item) => item !== null && item !== undefined && item !== '');
	}
</script>

<!-- Motherboard -->
<Widget title="Motherboard">
	{#snippet widgetContents()}
		{#if motherboard.Manufacturer}
			<div class="widget-values">
				<div>
					<span>{motherboard.Manufacturer}</span>
					<div>OEM</div>
				</div>
				<div>
					<span>{motherboard.Product}</span>
					<div>Chipset</div>
				</div>
			</div>
		{:else}
			<div class="widget-value">
				<div class="red">Error!</div>
				<div>Error retrieving motherboard information.</div>
			</div>
		{/if}
	{/snippet}

	{#snippet modalContents()}
		<table class="table">
			<thead> </thead>
			<tbody>
				<tr>
					<td>Motherboard Product</td>
					<td>{motherboard.Manufacturer} {motherboard.Product}</td>
				</tr>
				<tr>
					<td>Motherboard Manufacturer</td>
					<td>{motherboard.Manufacturer}</td>
				</tr>
				<tr>
					<td>BIOS Manufacturer</td>
					<td>{bios[0]['Manufacturer']}</td>
				</tr>
				<tr>
					<td>Version</td>
					<td>{bios[0]['SMBIOSBIOSVersion']}</td>
				</tr>
				<tr>
					<td>Release Date</td>
					<td>{dateConversion(bios[0]['ReleaseDate'])}</td>
				</tr>
				<tr>
					<td>Base</td>
					<td>{bios[0]['BIOSVersion'][2]}</td>
				</tr>
				<tr>
					<td>Serial Number</td>
					<td>{bios[0]['SerialNumber']}</td>
				</tr>
				<tr>
					<td>TPM Status</td>
					<td>{tpmStatus}</td>
				</tr>
				{#if tpmStatus == 'Enabled'}
					<tr>
						<td>TPM Manufacturer</td>
						<td>{tpmManufacturer}</td>
					</tr>
					<tr>
						<td>TPM Version</td>
						<td>{tpmVersion}</td>
					</tr>
				{/if}
			</tbody>
		</table>
	{/snippet}

	{#snippet extraModalContents()}
		<div class="modal-body">
			<table class="table">
				<tbody>
					{#each Object.entries(bios[0]) as [key, value]}
						{#if key == 'BiosCharacteristics'}
							<tr>
								<td>{key}</td>
								<td>
									<p>
										{#each filterBiosCharacteristics(bios[0]['BiosCharacteristics']) as characteristic}
											{characteristic}<br />
										{/each}
									</p>
								</td>
							</tr>
						{:else if key == 'BIOSVersion' || key == 'ListOfLanguages'}
							<tr>
								<td>{key}</td>
								<td>
									<p>
										{#each value as indivValue}
											{indivValue}<br />
										{/each}
									</p>
								</td>
							</tr>
						{:else}
							<tr>
								<td>{key}</td>
								<td>{value}</td>
							</tr>
						{/if}
					{/each}
				</tbody>
			</table>
		</div>
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

	.widget-values {
		display: flex;
		flex-flow: row wrap;
		justify-content: space-evenly;
	}
</style>