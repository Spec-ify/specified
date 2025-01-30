<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
    import Widget from './modal-widget.svelte';
</script>

<!-- CPU Usage -->
<Widget title="CPU Usage" type="" modalId="cpu-usage-modal">
    <div slot="values">
        <div class="widget-value">
            <div class="widget-value">
                <span class="green">
                    <!-- <?= $json_data['Hardware']['Cpu']['LoadPercentage'] ?? '--' ?>% -->
                </span>
            </div>
        </div>
    </div>
</Widget>