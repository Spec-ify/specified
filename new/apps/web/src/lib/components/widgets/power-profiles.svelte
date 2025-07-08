<script lang="ts">
    import Widget from '../../common/modal-widget.svelte';
</script>

<!-- Power Profiles -->
<Widget title="Power Profiles" modalId="power-modal">
    <div slot="values">
        <div class="widget-value">

            <!-- <?php
            if ($json_data['System']['PowerProfiles']) {
                $profile_count = safe_count($json_data['System']['PowerProfiles']);
                $profile_color = '';
                $current_profile = '';
                if ($profile_count != 0) {
                    for ($profile = 0; $profile < $profile_count; $profile++) {
                        if ($json_data['System']['PowerProfiles'][$profile]['IsActive']) {
                            $current_profile =  $json_data['System']['PowerProfiles'][$profile]['ElementName'];
                        }
                    }
                    if (str_contains(strtolower($current_profile), 'balanced')) {
                        $profile_color = '--yellow';
                    } elseif (str_contains(strtolower($current_profile), 'high')) {
                        $profile_color = '--red';
                    } elseif (str_contains(strtolower($current_profile), 'saver')) {
                        $profile_color = '--green';
                    }

                    echo '<span style="color: var(' . $profile_color . ');">' . $current_profile . '</span>';
                }
            }
            ?> -->
            <div style="font-size: 10pt;">Current Profile</div>
        </div>
    </div>

    <div class="modal-body">
        <h4>Power Profiles</h4>
        <table id="power-table" class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Element</th>
                    <th>Instance Path</th>
                    <th>Status</th>
                </tr>
            </thead>
        </table> <br>
        <h4>Battery</h4>
        <table id="battery-table" class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Manufacturer</th>
                    <th>Chemistry</th>
                    <th>Design Capacity</th>
                    <th>Current Full Charge Capacity</th>
                </tr>
            </thead>
        </table>
    </div>
</Widget>