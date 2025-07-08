<script lang="ts">
    import Widget from '../../common/modal-widget.svelte';
    import { lists } from '$lib/common/lists';

    export let tpmData;
    export let motherboardData;
    export let biosData;

    let tpmStatus = 'Disabled';
    let tpmManufacturer: string, tpmVersion: string;

    if (tpmData && tpmData.IsEnabled_InitialValue){
        tpmStatus = 'Enabled';
        tpmManufacturer = `${tpmData.ManufacturerVersionInfo} ${tpmData.ManufacturerVersion}`;
        tpmVersion = `${tpmData.SpecVersion}`;
    }

    function dateConversion(date: string){
        const input = date.match(/^(\d{4})(\d{2})(\d{2})/);

        if (!input) {
            return "Unknown"
        }

        const [ , year, month, day ] = input;
        const formatted = `${month}/${day}/${year}`;

        return formatted;
    }

    function filterBiosCharacteristics(rawList: [string, any]){
        let finalCharac = [""];
        const characList = lists["biosCharacteristicsList"];

        rawList.forEach((key: number) => {
            finalCharac.push(characList[key]);
        });

        return finalCharac.filter(item => item !== null && item !== undefined && item !== "");
    };
</script>

<!-- Motherboard -->
<Widget title="Motherboard" modalId="board-modal">
    <div slot="values">
        {#if (motherboardData.Manufacturer)}
            <div class="widget-values">
                <div class="widget-value">
                    <div class="green">
                        {motherboardData.Manufacturer}
                    </div>
                    <div>
                        OEM
                    </div>
                </div>
                <div class="widget-value">
                    <div class="green">
                        {motherboardData.Product}
                    </div>
                    <div>
                        Chipset                        
                    </div>
                </div>
            </div>
        {:else}
            <div class="widget-value">
                <div class="red"> Error! </div>
                <div>Error retrieving motherboard information.</div>
            </div>
        {/if}
    </div>

    <table slot="modal-body" class="table">
        <thead>
        </thead>
        <tbody>
            <tr>
                <td>Motherboard Product</td>
                <td>{motherboardData.Manufacturer} {motherboardData.Product}</td>
            </tr>
            <tr>
                <td>Motherboard Manufacturer</td>
                <td>{motherboardData.Manufacturer}</td>
            </tr>
            <tr>
                <td>BIOS Manufacturer</td>
                <td>{biosData[0]['Manufacturer']}</td>
            </tr>
            <tr>
                <td>Version</td>
                <td>{biosData[0]['SMBIOSBIOSVersion']}</td>
            </tr>
            <tr>
                <td>Release Date</td>
                <td>{dateConversion(biosData[0]['ReleaseDate'])}</td>
            </tr>
            <tr>
                <td>Base</td>
                <td>{biosData[0]['BIOSVersion'][2]}</td>
            </tr>
            <tr>
                <td>Serial Number</td>
                <td>{biosData[0]['SerialNumber']}</td>
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
        </tbody>
    </table>

    <div slot="extras" class="modal-body" id="board-modal-info-table" style="display:none;">
        <table class="table">
            <tbody>
                {#each Object.entries(biosData[0]) as [key, value]}
                    {#if (key == "BiosCharacteristics")}
                        <tr>
                            <td>{key}</td>
                            <td>
                                <p>
                                    {#each filterBiosCharacteristics(biosData[0]["BiosCharacteristics"]) as characteristic}
                                        {characteristic}<br/>
                                    {/each}
                                </p>
                            </td>
                        </tr>
                    {:else if (key == "BIOSVersion" || key == "ListOfLanguages")}
                        <tr>
                            <td>{key}</td>
                            <td>
                                <p>
                                    {#each value as indivValue}
                                        {indivValue}<br/>
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
</Widget>