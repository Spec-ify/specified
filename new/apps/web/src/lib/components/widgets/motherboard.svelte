<script lang="ts">
    import Widget from './modal-widget.svelte';

    export let data;

    let tpmStatus = 'Disabled';
    let tpmManufacturer: string, tpmVersion: string;

    if (data.Security.Tpm && data['Security']['Tpm']['IsEnabled_InitialValue']){
        tpmStatus = 'Enabled';
        tpmManufacturer = `${data.Security.Tpm.ManufacturerVersionInfo} ${data.Security.Tpm.ManufacturerVersion}`;
        tpmVersion = `${data.Security.Tpm.SpecVersion}`;
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
</script>

<!-- Motherboard -->
<Widget title="Motherboard" modalId="board-modal">
    <div slot="values">
        {#if (data.Hardware.Motherboard.Manufacturer)}
            <div class="widget-values">
                <div class="widget-value">
                    <div class="green">
                        {data.Hardware.Motherboard.Manufacturer}
                    </div>
                    <div>
                        OEM
                    </div>
                </div>
                <div class="widget-value">
                    <div class="green">
                        {data.Hardware.Motherboard.Product}
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
                <td>{data.Hardware.Motherboard.Manufacturer} {data.Hardware.Motherboard.Product}</td>
            </tr>
            <tr>
                <td>Motherboard Manufacturer</td>
                <td>{data.Hardware.Motherboard.Manufacturer}</td>
            </tr>
            <tr>
                <td>BIOS Manufacturer</td>
                <td>{data['Hardware']['BiosInfo'][0]['Manufacturer']}</td>
            </tr>
            <tr>
                <td>Version</td>
                <td>{data['Hardware']['BiosInfo'][0]['SMBIOSBIOSVersion']}</td>
            </tr>
            <tr>
                <td>Release Date</td>
                <td>{dateConversion(data['Hardware']['BiosInfo'][0]['ReleaseDate'])}</td>
            </tr>
            <tr>
                <td>Base</td>
                <td>{data['Hardware']['BiosInfo'][0]['BIOSVersion'][2]}</td>
            </tr>
            <tr>
                <td>Serial Number</td>
                <td>{data['Hardware']['BiosInfo'][0]['SerialNumber']}</td>
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

    <div slot="extras">
        <table class="table">
            <tbody>
                <!-- <?php
                foreach ($json_data['Hardware']['BiosInfo'][0] as $key => $value) {
                    if ($key == 'BiosCharacteristics') {
                        $bcStringList = [];
                        foreach ($value as $characteristic) {
                            if (isset($biosCharacteristics[$characteristic])) {
                                $bcStringList[] = $biosCharacteristics[$characteristic];
                            }
                        }
                        echo '
                                        <tr>
                                            <td>' . $key . '</td>
                                            <td>' . implode('<br/>', $bcStringList) . '</td>
                                        </tr>
                                        ';
                        continue;
                    }
                    if ($key == 'BIOSVersion' || $key == 'ListOfLanguages') {
                        echo '
                                        <tr>
                                            <td>' . $key . '</td>
                                            <td>' . safe_implode('<br/>', $value) . '</td>
                                        </tr>
                                        ';
                        continue;
                    }

                    echo "
                                    <tr>
                                        <td>$key</td>
                                        <td>$value</td>
                                    </tr>
                                    ";
                }
                ?> -->
            </tbody>
        </table>
    </div>
</Widget>