<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
    import Widget from './modal-widget.svelte';
</script>

<!-- RAM -->

<Widget title="Memory" modalId="ram-modal">
    <div slot="values">
        <!-- <div class="widget-values" <?php
                                    if (safe_count($json_data['Hardware']['Ram']) > 4) {
                                        echo 'style="display: flex; flex-flow: row wrap;"';
                                    }
                                    ?>>
            <?php
            $ram_sticks = safe_count($json_data['Hardware']['Ram']);
            $ram_stick = 0;
            if ($ram_sticks > 0) {
                for ($ram_stick; $ram_stick < $ram_sticks; $ram_stick++) {
                    $current_ram_stick = $ram_stick + 1;
                    $flex_basis = 100 / ($ram_sticks / 2) . '%';
                    if ($json_data['Hardware']['Ram'][$ram_stick]['Capacity'] != 0) {
                        $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity'] / 1000);
                        echo '
                                    <div class="widget-value" style="flex: 1 1 ' . $flex_basis . ';">
                                        <div class="green">' . $ram_size . 'GB</div>
                                        <div>DIMM' . $current_ram_stick . '</div>
                                    </div>';
                    } else {
                        echo '
                                    <div class="widget-value" style="flex: 1 1 ' . $flex_basis . ';">
                                        <div style="color: rgb(215,27,27);">--</div>
                                        <div>DIMM' . $current_ram_stick . '</div>
                                    </div>';
                    }
                    if (safe_count($json_data['Hardware']['Ram']) > 4 && ($current_ram_stick % 4) == 0) {
                        echo '<div style="flex-basis: 100%;"></div>';
                    }
                }
            } else {
                echo '
                                    <div class="widget-value">
                                        <div class="red"> Error! </div>
                                        <div>Error retrieving memory information.</div>
                                    </div>';
            }
            ?>
        </div> -->
    </div>

    <div slot="modal-body">
        <h5>Physical Memory</h5>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">DIMM</th>
                    <th scope="col">Manufacturer</th>
                    <th scope="col">Model</th>
                    <th scope="col">Speed</th>
                    <th scope="col">Capacity</th>
                </tr>
            </thead>
            <tbody>
                <!-- <?php
                $ram_sticks = safe_count($json_data['Hardware']['Ram']);
                $ram_stick = 0;
                for ($ram_stick; $ram_stick < $ram_sticks; $ram_stick++) {
                    $ram_location = $json_data['Hardware']['Ram'][$ram_stick]['DeviceLocation'];
                    $ram_manufacturer = $json_data['Hardware']['Ram'][$ram_stick]['Manufacturer'];
                    $ram_part = $json_data['Hardware']['Ram'][$ram_stick]['PartNumber'];
                    $ram_speed = $json_data['Hardware']['Ram'][$ram_stick]['ConfiguredSpeed'];
                    $ram_size = floor($json_data['Hardware']['Ram'][$ram_stick]['Capacity']);
                    if ($ram_size == 0) {
                        echo '
                                    <tr>
                                        <td>' . $ram_location . '</td>
                                        <td colspan="4" class="td-center">Not Detected</td>
                                    </tr>
                                                ';
                    } else {
                        echo '
                                    <tr>
                                        <td>' . $ram_location . '</td>
                                        <td>' . $ram_manufacturer . '</td>
                                        <td>' . $ram_part . '</td>
                                        <td>' . $ram_speed . 'MHz</td>
                                        <td>' . $ram_size . 'MB</td>
                                    </tr>
                                                ';
                    }
                }
                ?> -->
            </tbody>
        </table>
        <h5>Pagefile</h5>
        <table class="table">
            <tbody>
                <tr>
                    <td>File Path</td>
                    <!-- <td> <?= $json_data['System']['PageFile']['Caption'] ?> </td> -->
                </tr>
                <tr>
                    <td>Allocated Base Size</td>
                    <!-- <td> <?= $json_data['System']['PageFile']['AllocatedBaseSize'] ?> MB</td> -->
                </tr>
                <tr>
                    <td>Current Usage</td>
                    <!-- <td> <?= $json_data['System']['PageFile']['CurrentUsage'] ?> MB</td> -->
                </tr>
                <tr>
                    <td>Peak Usage</td>
                    <!-- <td> <?= $json_data['System']['PageFile']['PeakUsage'] ?> MB</td> -->
                </tr>
            </tbody>
        </table>
    </div>
</Widget>