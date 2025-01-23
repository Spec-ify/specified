<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
</script>

<!-- Motherboard -->
<div class="widget widget-board hover" type="button" data-mdb-toggle="modal" data-mdb-target="#board-modal">
    <h1>Motherboard</h1>
    <!-- <?php
    if ($motherboard) {
        echo '
                            <div class="widget-values">
                                <div class="widget-value">
                                    <div class="green">'
            . $motherboard .
            '</div>
                                    <div>OEM</div>
                                </div>
                                <div class="widget-value">
                                    <div class="green">'
            . $json_data['Hardware']['Motherboard']['Product'] .
            '
                                    </div>
                                    <div>Chipset</div>
                                </div>
                            </div>';
    } else {
        echo '
                                <div class="widget-value">
                                    <div class="red"> Error! </div>
                                    <div>Error retrieving motherboard information.</div>
                                </div>';
    }

    ?> -->

</div>
<div class="modal fade" id="board-modal" tabindex="-1" aria-labelledby="board-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label">Board Information</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Motherboard Product</td>
                            <!-- <td><?= $motherboard ?> <?= $json_data['Hardware']['Motherboard']['Product'] ?></td> -->
                        </tr>
                        <tr>
                            <td>Motherboard Manufacturer</td>
                            <!-- <td><?= $json_data['Hardware']['Motherboard']['Manufacturer'] ?></td> -->
                        </tr>
                        <tr>
                            <td>BIOS Manufacturer</td>
                            <!-- <td><?= $json_data['Hardware']['BiosInfo'][0]['Manufacturer'] ?></td> -->
                        </tr>
                        <tr>
                            <td>Version</td>
                            <!-- <td><?= $json_data['Hardware']['BiosInfo'][0]['SMBIOSBIOSVersion'] ?></td> -->
                        </tr>
                        <tr>
                            <td>Release Date</td>
                            <!-- <?php
                            $biosdate = $json_data['Hardware']['BiosInfo'][0]['ReleaseDate'];

                            // Use the DateTime class to parse the date string
                            $datetime = DateTime::createFromFormat('YmdHis.uO', $biosdate);
                            if ($datetime) { // the one provided by the bios wasn't valid
                                // Format the date using the desired MMDDYYYY format
                                $formattedbiosdate = $datetime->format('m/d/Y');
                            } else {
                                $formattedbiosdate = "unknown";
                            }
                            ?> -->
                            <!-- <td><?= $formattedbiosdate ?></td> -->
                        </tr>
                        <tr>
                            <td>Base</td>
                            <!-- <td><?= $json_data['Hardware']['BiosInfo'][0]['BIOSVersion'][2] ?></td> -->
                        </tr>
                        <tr>
                            <td>Serial Number</td>
                            <!-- <td><?= $json_data['Hardware']['BiosInfo'][0]['SerialNumber'] ?></td> -->
                        </tr>
                        <!-- <?php
                        $tpm_status = 'Disabled';
                        $tpm_manufacturer = "N/A";
                        $tpm_version = "N/A";
                        if (is_null($json_data['Security']['Tpm']) || !(bool) $json_data['Security']['Tpm']['IsEnabled_InitialValue']) {
                        } else {
                            $tpm_status = 'Enabled';
                            $tpm_manufacturer = $json_data['Security']['Tpm']['ManufacturerVersionInfo'] . ' ' . $json_data['Security']['Tpm']['ManufacturerVersion'];
                            $tpm_version = $json_data['Security']['Tpm']['SpecVersion'];
                        }

                        echo

                        '<tr>
                                        <td>TPM Status</td>
                                        <td>' . $tpm_status . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>TPM Manufacturer</td>
                                        <td>' . $tpm_manufacturer . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>TPM Version</td>
                                        <td>' . $tpm_version . '</td>
                                    </tr>';
                        ?> -->
                    </tbody>
                </table>
                <div style="display: none;" id="board-info-more-info">
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="board-info-more-info-button">More Info</button>
                <button type="button" class="btn btn-secondary" id="board-info-close" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>