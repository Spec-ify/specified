<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
</script>

<!-- CPU Usage -->
<div class="widget widget-cpu hover">
    <h1>CPU
        <span>(Used)</span>
    </h1>
    <div class="widget-values">
        <div class="widget-value">
            <div class="widget-value">
                <span class="green">
                    <!-- <?= $json_data['Hardware']['Cpu']['LoadPercentage'] ?? '--' ?>% -->
                </span>
            </div>
        </div>
    </div>
</div>