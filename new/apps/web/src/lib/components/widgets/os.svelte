<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
</script>

<!-- OS -->
<div class="widget widget-os hover" type="button" data-mdb-toggle="modal" data-mdb-target="#os-modal">
    <h1>Operating System</h1>
    <div class="widget-values">
        <div class="widget-value">
            <div class="widget-value">
                <span class="green">
                    <!-- <?= $json_data['BasicInfo']['Edition'] ?> -->
                </span>
            </div>
            <div>
                <!-- <?= $json_data['BasicInfo']['FriendlyVersion'] ?> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade " id="os-modal" tabindex="-1" aria-labelledby="os-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label">System Information</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>

                    </thead>
                    <tbody>
                        <!-- <?php
                        echo
                        '<tr>
                                        <td>Edition</td>
                                        <td>' . $json_data['BasicInfo']['Edition'] . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Version</td>
                                        <td>' . $json_data['BasicInfo']['Version'] . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Friendly Version</td>
                                        <td>' . $json_data['BasicInfo']['FriendlyVersion'] . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Install Date</td>
                                        <td>' . $json_data['BasicInfo']['InstallDate'] . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Uptime</td>
                                        <td>' . $json_data['BasicInfo']['Uptime'] . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Hostname</td>
                                        <td>' . $json_data['BasicInfo']['Hostname'] . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Username</td>
                                        <td>' . $json_data['BasicInfo']['Username'] . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Domain</td>
                                        <td>' . $json_data['BasicInfo']['Domain'] . '</td>
                                    </tr>';
                        if ($json_data['Security']['UacEnabled']) {
                            $uac_status = 'Enabled';
                        } else {
                            $uac_status = 'Disabled';
                        }

                        echo

                        '<tr>
                                        <td>UAC Status</td>
                                        <td>' . $uac_status . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>UAC Level</td>
                                        <td>' . $json_data['Security']['UacLevel'] . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Boot Mode</td>
                                        <td>' . $json_data['BasicInfo']['BootMode'] . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Secure Boot</td>
                                        <td>' . ((bool) $json_data['Security']['SecureBootEnabled'] ? 'Enabled' : 'Disabled') . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Boot State</td>
                                        <td>' . $json_data['BasicInfo']['BootState'] . '</td>
                                    </tr>';

                        echo
                        '<tr>
                                        <td>TPM Status</td>
                                        <td>' . $tpm_status . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Manufacturer Version</td>
                                        <td>' . $tpm_manufacturer . '</td>
                                    </tr>';
                        echo
                        '<tr>
                                        <td>Version</td>
                                        <td>' . $tpm_version . '</td>
                                    </tr>';
                        ?> -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>