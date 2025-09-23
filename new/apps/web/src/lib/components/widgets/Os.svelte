<!-- NOT YET IMPLEMENTED IN NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	export let securityData;
	export let basicinfoData;

	let tpmStatus = 'Disabled';
	let tpmManufacturer: string, tpmVersion: string;

	if (securityData.Tpm && securityData.Tpm.IsEnabled_InitialValue) {
		tpmStatus = 'Enabled';
		tpmManufacturer = `${securityData.Tpm.ManufacturerVersionInfo} ${securityData.Tpm.ManufacturerVersion}`;
		tpmVersion = `${securityData.Tpm.SpecVersion}`;
	}
</script>

<!-- OS -->

<Widget title="Operating System" modalId="os-modal">
	<div slot="values">
		<div class="widget-value">
			<div class="widget-value">
				<span class="green">
					{basicinfoData.Edition}
				</span>
			</div>
			<div>
				{basicinfoData.FriendlyVersion}
			</div>
		</div>
	</div>

	<table slot="modal-body">
		<thead> </thead>
		<tbody>
			<tr>
				<td>Edition</td>
				<td>{basicinfoData.Edition}</td>
			</tr>
			<tr>
				<td>Version</td>
				<td>{basicinfoData.Version}</td>
			</tr>
			<tr>
				<td>Friendly Version</td>
				<td>{basicinfoData.FriendlyVersion}</td>
			</tr>
			<tr>
				<td>Install Date</td>
				<td>{basicinfoData.InstallDate}</td>
			</tr>
			<tr>
				<td>Uptime</td>
				<td>{basicinfoData.Uptime}</td>
			</tr>
			<tr>
				<td>Hostname</td>
				<td>{basicinfoData.Hostname}</td>
			</tr>
			<tr>
				<td>Username</td>
				<td>{basicinfoData.Username}</td>
			</tr>
			<tr>
				<td>Domain</td>
				<td>{basicinfoData.Domain}</td>
			</tr>
			<tr>
				<td>UAC Status</td>
				<td>
					{#if securityData.UacEnabled}
						Enableed
					{:else}
						Disabled
					{/if}
				</td>
			</tr>
			<tr>
				<td>UAC Level</td>
				<td>{securityData.UacLevel}</td>
			</tr>
			<tr>
				<td>Boot Mode</td>
				<td>{basicinfoData.BootMode}</td>
			</tr>
			<tr>
				<td>Secure Boot</td>
				<td>
					{#if securityData.SecureBootEnabled}
						Enableed
					{:else}
						Disabled
					{/if}
				</td>
			</tr>
			<tr>
				<td>Boot State</td>
				<td>{basicinfoData.BootState}</td>
			</tr>
			<tr>
				<td>TPM Status</td>
				<td
					>{#if tpmStatus == 'Enabled'}
						Enabled
					{:else}
						Disabled
					{/if}
				</td>
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
			<!-- 
            <tr>
                <td>TPM Status</td>
                <td>$tpm_status</td>
            </tr>
            <tr>
                <td>Manufacturer Version</td>
                <td>$tpm_manufacturer</td>
            </tr>
            <tr>
                <td>Version</td>
                <td>$tpm_version</td>
            </tr> 
-->
		</tbody>
	</table>
</Widget>
