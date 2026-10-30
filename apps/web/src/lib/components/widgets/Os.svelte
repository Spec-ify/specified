<!-- NOT YET IMPLEMENTED IN NEW WIDGET SYSTEM -->
<script lang="ts">
	import type { Security } from '$lib/common/report/security';
	import type { BasicInfo } from '$lib/common/report/basicinfo';

	import Widget from '../../common/ModalWidget.svelte';

	interface Props {
		security: Security;
		basic: BasicInfo;
	}

	let {
		security,
		basic
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
			<span>{basic.Edition}</span>
			<div>{basic.FriendlyVersion}</div>
		</div>
	{/snippet}

	{#snippet modalContents()}
		<table class="table">
			<thead> </thead>
			<tbody>
				<tr>
					<td>Edition</td>
					<td>{basic.Edition}</td>
				</tr>
				<tr>
					<td>Version</td>
					<td>{basic.Version}</td>
				</tr>
				<tr>
					<td>Friendly Version</td>
					<td>{basic.FriendlyVersion}</td>
				</tr>
				<tr>
					<td>Install Date</td>
					<td>{basic.InstallDate}</td>
				</tr>
				<tr>
					<td>Uptime</td>
					<td>{basic.Uptime}</td>
				</tr>
				<tr>
					<td>Hostname</td>
					<td>{basic.Hostname}</td>
				</tr>
				<tr>
					<td>Username</td>
					<td>{basic.Username}</td>
				</tr>
				<tr>
					<td>Domain</td>
					<td>{basic.Domain}</td>
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
					<td>{basic.BootMode}</td>
				</tr>
				<tr>
					<td>Secure Boot</td>
					<td>
						{security.SecureBootEnabled ? 'Enabled' : 'Disabled' }
					</td>
				</tr>
				<tr>
					<td>Boot State</td>
					<td>{basic.BootState}</td>
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