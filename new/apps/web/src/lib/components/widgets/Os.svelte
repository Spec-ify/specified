<!-- NOT YET IMPLEMENTED IN NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	interface TpmInfo {
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

	interface SecurityInfo {
		AvList: Array<string>;
		ExclusionPath: Array<string>;
		ExclusionExtension: Array<string>,
		ExclusionProcess: Array<string>,
		ExclusionIpAddresses: Array<string>,
		FwList: Array<string>;
		UacEnabled: boolean;
		SecureBootEnabled: boolean;
		UacLevel: number;
		Tpm: TpmInfo;
		WriteSuccess: boolean;
		ErrorCount: number;
	}

	interface BasicInfo {
		Edition: string;
		Version: string;
		Sku: string;
		FriendlyVersion: string;
		InstallDate: string;
		Uptime: number;
		Hostname: string;
		Username: string;
		Domain: string;
		BootMode: string;
		BootState: string;
		SMBiosRamInformation: boolean;
		WriteSuccess: boolean;
		ErrorCount: number;
	}

	interface Props {
		security: SecurityInfo;
		basicInfo: BasicInfo;
	}

	let {
		security,
		basicInfo
	}: Props = $props();

	let tpmStatus = 'Disabled';
	let tpmManufacturer: string, tpmVersion: string;

	if (security.Tpm && security.Tpm.IsEnabled_InitialValue) {
		tpmStatus = 'Enabled';
		tpmManufacturer = `${security.Tpm.ManufacturerVersionInfo} ${security.Tpm.ManufacturerVersion}`;
		tpmVersion = `${security.Tpm.SpecVersion}`;
	}
</script>

<!-- OS -->

<Widget title="Operating System">
	{#snippet widgetContents()}
		<div class="widget-value">
			<span>{basicInfo.Edition}</span>
			<div>{basicInfo.FriendlyVersion}</div>
		</div>
	{/snippet}

	{#snippet modalContents()}
		<table class="table">
			<thead> </thead>
			<tbody>
				<tr>
					<td>Edition</td>
					<td>{basicInfo.Edition}</td>
				</tr>
				<tr>
					<td>Version</td>
					<td>{basicInfo.Version}</td>
				</tr>
				<tr>
					<td>Friendly Version</td>
					<td>{basicInfo.FriendlyVersion}</td>
				</tr>
				<tr>
					<td>Install Date</td>
					<td>{basicInfo.InstallDate}</td>
				</tr>
				<tr>
					<td>Uptime</td>
					<td>{basicInfo.Uptime}</td>
				</tr>
				<tr>
					<td>Hostname</td>
					<td>{basicInfo.Hostname}</td>
				</tr>
				<tr>
					<td>Username</td>
					<td>{basicInfo.Username}</td>
				</tr>
				<tr>
					<td>Domain</td>
					<td>{basicInfo.Domain}</td>
				</tr>
				<tr>
					<td>UAC Status</td>
					<td>
						{security.UacEnabled ? 'Enabled' : 'Disabled' }
					</td>
				</tr>
				<tr>
					<td>UAC Level</td>
					<td>{security.UacLevel}</td>
				</tr>
				<tr>
					<td>Boot Mode</td>
					<td>{basicInfo.BootMode}</td>
				</tr>
				<tr>
					<td>Secure Boot</td>
					<td>
						{security.SecureBootEnabled ? 'Enabled' : 'Disabled' }
					</td>
				</tr>
				<tr>
					<td>Boot State</td>
					<td>{basicInfo.BootState}</td>
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