<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
</script>

<!-- GPU -->
<div class="widget widget-gpu hover" type="button" data-mdb-toggle="modal" data-mdb-target="#gpu-modal">
    <h1>GPU</h1>
    <div class="widget-values">
        <div class="widget-value">
            <div class="green">
                <!-- <?php

                if (!isset($json_data['Hardware']['Monitors'])) {
                    echo $json_data['Hardware']['Gpu'][0]['Description'];
                } else {
                    echo $json_data['Hardware']['Monitors'][0]['Name'];
                }

                ?> -->
            </div>
            <div>Model</div>
        </div>
    </div>

</div>
<div class="modal fade" id="gpu-modal" tabindex="-1" aria-labelledby="gpu-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label">GPU and Monitor Info</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- <?php
                if (isset($json_data['Hardware']['Gpu'])) {
                    $html = '<h5> GPU Info </h5>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Name</th>
                                                                <th scope="col">VRAM</th>
                                                                <th scope="col">Resolution</th>
                                                                <th scope="col">Refresh Rate</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>';

                    foreach ($json_data['Hardware']['Gpu'] as $gpu) {
                        $vram = $gpu['AdapterRAM'] / 1048576 . ' MB';
                        $res = $gpu['CurrentHorizontalResolution'] . ' x ' . $gpu['CurrentVerticalResolution'];
                        $refrate = $gpu['CurrentRefreshRate'] . 'Hz';

                        $html .= '<tr>
                                                            <td>' . $gpu['Description'] . '</td>
                                                            <td>' . $vram . '</td>
                                                            <td>' . $res . '</td>
                                                            <td>' . $refrate . '</td>
                                                        </tr>';
                    }

                    $html .= '</tbody>
                                            </table>';

                    echo $html;

                    $html = '';
                }

                if (isset($json_data['Hardware']['Monitors'])) {
                    $html = '<h5> Monitor Info </h5>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Name</th>
                                                                <th scope="col">VRAM</th>
                                                                <th scope="col">Mode</th>
                                                                <th scope="col">Monitor</th>
                                                                <th scope="col">Connection</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>';

                    foreach ($json_data['Hardware']['Monitors'] as $monitor) {
                        $html .= '<tr>
                                                            <td>' . $monitor['Name'] . '</td>
                                                            <td>' . $monitor['DedicatedMemory'] . '</td>
                                                            <td>' . $monitor['CurrentMode'] . '</td>
                                                            <td>' . $monitor['MonitorModel'] . '</td>
                                                            <td>' . $monitor['ConnectionType'] . '</td>
                                                        </tr>';
                    }

                    $html .= '</tbody>
                                            </table>';

                    echo $html;

                    $html = '';
                }

                ?> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>