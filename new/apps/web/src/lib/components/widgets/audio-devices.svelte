<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
    import Widget from './modal-widget.svelte';
</script>

<!-- Audio Devices -->
<Widget title="Audio Devices" modalId="audio-modal">
    <div slot="values">
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
</Widget>