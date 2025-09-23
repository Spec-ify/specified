<!-- NOT YET MIGRATED TO NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';
</script>

<!--

# Drives
 
Could probably use a foreach on array that adds a widget component for each drive detected

-->
<div class="widgets-widgets widgets" data-hide="false">
	<!-- <?php
    $drives_amount = safe_count($json_data['Hardware']['Storage']);

    foreach ($json_data['Hardware']['Storage'] as $driveKey => $drive) {
        $drive_size_raw = $drive['DiskCapacity'];
        $drive_free_raw = getDriveFree($drive);
        $device_name = $drive['DeviceName'];
        $drive_taken_raw = $drive_size_raw - $drive_free_raw;
        $drive_size = floor(bytesToGigabytes($drive_size_raw));
        $drive_taken = floor(bytesToGigabytes($drive_taken_raw));
        // the drive size can sometimes be zero if the drive is failing
        if ($drive_taken != 0 && $drive_size != 0) {
            $drive_percentage = round($drive_taken / $drive_size * 100);
        } else $drive_percentage = 0;
        $flavor_color = '';

        if ($drive_percentage >= 80) {
            $flavor_color = "red";
        } elseif ($drive_percentage >= 50 && $drive_percentage <= 79) {
            $flavor_color = "yellow";
        } elseif ($drive_percentage >= 0 && $drive_percentage <= 49) {
            $flavor_color = "green";
        }
        if (abs(floor(bytesToGigabytes($drive['DiskCapacity'])) -
            floor(bytesToGigabytes(getDriveCapacity($drive)))) > 5) {
            $flavor_color = "red";
        }

        $letters = array_filter(
            array_column($drive['Partitions'], 'PartitionLetter')
        );
        $lettersString = implode(", ", $letters);

        echo '
                <div class="widget widget-disk hover" type="button" data-mdb-toggle="modal" data-mdb-target="#drive-modal' . $driveKey . '">
                    <h1>' . $device_name . ' ' . $lettersString . '</h1>
                    <div class="widget-values">
                        <div class="widget-value">
                            <div class="widget-single-value">
                                <span
                                                               class="' . $flavor_color . '">' . (int)$drive_taken . ' GB</span>
                                <span>/</span>
                                <span>' . (int) $drive_size . ' GB</span>
                            </div>
                            <div>' . $drive_percentage . '%</div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="drive-modal' . $driveKey . '" tabindex="-1" aria-labelledby="drive-modal" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modal-label">' . $device_name . ' ' . $lettersString . '</h5>
                                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <thead>
                                    <th>Name</th>
                                    <th>SN</th>
                                    <th>#</th>
                                    <th>Capacity</th>
                                    <th>Free</th>
                                    </thead>
                                    <tbody>
                                    ' . '<td>' . $drive['DeviceName'] . '</td>' . '
                                    ' . '<td>' . $drive['SerialNumber'] . '</td>' . '
                                    ' . '<td>' . $drive['DiskNumber'] . '</td>' . '
                                    ' . '<td>' . floor(bytesToGigabytes($drive['DiskCapacity'])) . 'GB</td>' . '
                                    ' . '<td>' . floor(bytesToGigabytes(getDriveFree($drive))) . 'GB</td>' . '
                                    </tbody>
                                </table>
                                <h5>Partitions</h5>
                                <div class="progress partition-whole-bar">
                            ';

        foreach ($drive['Partitions'] as $part) {
            $part_size = $part['PartitionCapacity'];
            $part_taken = $part_size - $part['PartitionFree'];
            $part_display = "";
            if (!empty($part['PartitionLabel'])) {
                $part_display .= $part['PartitionLabel'];
                if (isset($part['PartitionLetter'])) {
                    $part_display .= " ({$part['PartitionLetter']})";
                }
            } else if (isset($part['PartitionLetter'])) { // and not partition label
                $part_display = $part['PartitionLetter'];
            }
            if (!empty($part_display))
                $part_display .= '<br/>';
            $fs_display = $part['Filesystem'] ?? 'Unknown';

            // The "drive size + 1" is a terrible fix for division by 0 errors
            echo '
                                <div class="progress progress-bar partition-one-bar" style="width: ' . $part_size / ($drive_size_raw + 1) * 100 . '%;">
                                    <span class="partition-bar-label">
                                        ' . $part_display /* this will already have <br/> if not empty */ . '
                                        ' . $fs_display . '<br/>
                                        ' . "$part_taken / $part_size MB Used" . '
                                    </span>
                                    <div class="progress-bar partition-space-bar" style="width: ' . $part_taken / $part_size * 100 . '%;"></div>
                                </div>
                                ';
        }

        echo '
                                </div>
                                <table class="table">
                                    <thead>
                                        <th>Label</th>
                                        <th>Letter</th>
                                        <th>Capacity</th>
                                        <th>Free</th>
                                        <th>FS Type</th>
                                        <th>CfgMgr Error Code</th>
                                        <th>Last Error Code</th>
                                        <th>Dirty Bit</th>
                                    </thead>
                                    <tbody>
                                    ';
        foreach ($drive['Partitions'] as $part) {
            echo '
                                <tr>
                                    <td>' . $part['PartitionLabel'] . '</td>
                                    <td>' . $part['PartitionLetter'] . '</td>
                                    <td>' . floor(bytesToMegabytes($part['PartitionCapacity'])) . ' MB</td>
                                    <td>' . floor(bytesToMegabytes($part['PartitionFree'])) . ' MB</td>
                                    <td>' . $part['Filesystem'] . '</td>
                                    <td>' . $part['CfgMgrErrorCode'] . '</td>
                                    <td>' . $part['LastErrorCode'] . '</td>
                                    <td>' . $part['DirtyBitSet'] . '</td>
                                </tr>
                                ';
        }
        echo '
                                    </tbody>
                                </table>
                            <h5>SMART</h5>
                            ';
        if (is_array($drive['SmartData']) && count($drive['SmartData']) != 0) {
            echo
            '
                                <div class="smart-table-wrapper">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Index</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                ';

            // two SMART chunks for 2 columns of table
            list($smart1, $smart2) = array_chunk($drive['SmartData'], ceil(safe_count($drive['SmartData']) / 2));

            foreach ($smart1 as $smartEntry) {
                echo
                '
                                        <tr>
                                            <th scope="row">' . $smartEntry['Id'] . '</th>
                                            <td>' . $smartEntry['Name'] . '</td>
                                            <td>' . $smartEntry['RawValue'] . '</td>
                                        </tr>';
            }

            echo '
                                        </tbody>
                                    </table>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Index</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                ';

            foreach ($smart2 as $smartEntry) {
                echo
                '
                                        <tr>
                                            <th scope="row">' . $smartEntry['Id'] . '</th>
                                            <td>' . $smartEntry['Name'] . '</td>
                                            <td>' . $smartEntry['RawValue'] . '</td>
                                        </tr>';
            }

            echo
            '
                                        </tbody>
                                    </table>
                                </div>
                                ';
        } else {
            echo
            '
                        <h5>Sorry, no SMART data was found for this device.</h5>
                        ';
        }
        echo
        '
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
    }
    ?> -->
</div>
