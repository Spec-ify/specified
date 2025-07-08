<script lang="ts">
    import Widget from './modal-widget.svelte';

    export let data;
</script>

<!-- CPU Usage -->
<Widget title="CPU Usage" type="" modalId="cpu-usage-modal">
    <div slot="values">
        <div class="widget-value">
            <div class="widget-value">
                <span class="green">
                    {data.Hardware.Cpu.LoadPercentage ?? '--'}%
                    <!-- <?= $json_data['Hardware']['Cpu']['LoadPercentage'] ?? '--' ?>% -->
                </span>
            </div>
        </div>
    </div>
</Widget>