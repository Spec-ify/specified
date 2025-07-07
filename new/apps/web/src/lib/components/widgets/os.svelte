<script lang="ts">
    import Widget from './modal-widget.svelte';

    export let data;

    let tpmStatus = 'Disabled';
    let tpmManufacturer: string, tpmVersion: string;

    if (data.Security.Tpm && data.Security.Tpm.IsEnabled_InitialValue){
        tpmStatus = 'Enabled';
        tpmManufacturer = `${data.Security.Tpm.ManufacturerVersionInfo} ${data.Security.Tpm.ManufacturerVersion}`;
        tpmVersion = `${data.Security.Tpm.SpecVersion}`;
    }
</script>

<!-- OS -->

<Widget title="Operating System" modalId="os-modal">
    <div slot="values">
        <div class="widget-value">
            <div class="widget-value">
                <span class="green">
                    {data.BasicInfo.Edition}
                </span>
            </div>
            <div>
                {data.BasicInfo.FriendlyVersion}
            </div>
        </div>
    </div>

    <table slot="modal-body">
        <thead>

        </thead>
        <tbody>
            <tr>
                <td>Edition</td>
                <td>{data.BasicInfo.Edition}</td>
            </tr>
            <tr>
                <td>Version</td>
                <td>{data.BasicInfo.Version}</td>
            </tr>
            <tr>
                <td>Friendly Version</td>
                <td>{data.BasicInfo.FriendlyVersion}</td>
            </tr>
            <tr>
                <td>Install Date</td>
                <td>{data.BasicInfo.InstallDate}</td>
            </tr>
            <tr>
                <td>Uptime</td>
                <td>{data.BasicInfo.Uptime}</td>
            </tr>
            <tr>
                <td>Hostname</td>
                <td>{data.BasicInfo.Hostname}</td>
            </tr>
            <tr>
                <td>Username</td>
                <td>{data.BasicInfo.Username}</td>
            </tr>
            <tr>
                <td>Domain</td>
                <td>{data.BasicInfo.Domain}</td>
            </tr>
            <tr>
                <td>UAC Status</td>
                <td>
                    {#if (data.Security.UacEnabled)}
                        Enableed
                    {:else}
                        Disabled
                    {/if}
                </td>
            </tr>
            <tr>
                <td>UAC Level</td>
                <td>{data.Security.UacLevel}</td>
            </tr>
            <tr>
                <td>Boot Mode</td>
                <td>{data.BasicInfo.BootMode}</td>
            </tr>
            <tr>
                <td>Secure Boot</td>
                <td>
                    {#if (data.Security.SecureBootEnabled)}
                        Enableed
                    {:else}
                        Disabled
                    {/if}
                </td>
            </tr>
            <tr>
                <td>Boot State</td>
                <td>{data.BasicInfo.BootState}</td>
            </tr>
            <tr>
                <td>TPM Status</td>
                <td>{#if (tpmStatus == "Enabled")}
                        Enabled
                    {:else}
                        Disabled
                    {/if}
                </td>
            </tr>
            {#if (tpmStatus == "Enabled")}
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