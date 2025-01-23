<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
</script>

<!-- Audio Devices -->
<div class="widget widget-audio hover" type="button" data-mdb-toggle="modal" data-mdb-target="#audio-modal">
    <h1>Audio Devices</h1>
    <div class="widget-values">
        <!-- <?php
        $internalaudio = 0;
        $externalaudio = 0;
        foreach ($json_data['Hardware']['AudioDevices'] as $audioDevice) {
            if (str_contains(strtolower($audioDevice['DeviceID']), 'hdaudio')) {
                $internalaudio = $internalaudio + 1;
            } else {
                $externalaudio = $externalaudio + 1;
            };
        }
        echo '<div class="widget-value green">Internal : ' . $internalaudio . '</div>';
        echo '<div class="widget-value yellow">External : ' . $externalaudio . '</div>';
        ?> -->
    </div>
</div>
<div class="modal fade " id="audio-modal" tabindex="-1" aria-labelledby="audio-modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label">Audio Devices</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="audio-table" class="table">
                    <thead>
                        <tr>
                            <th>Device ID</th>
                            <th>Manufacturer</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>