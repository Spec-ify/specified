<script lang="ts">
    import Widget from './modal-widget.svelte';

    export let data;
</script>

<!-- OS -->

<Widget title="Operating System" modalId="os-modal">
    <div slot="values">
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

    <table slot="modal-body">
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
</Widget>