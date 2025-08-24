<script lang="ts">
    import Widget from '../../common/modal-widget.svelte';

    export let rawGPUData;
    export let rawMonitorData;

    const gpuData: Record<any, Record<string, any>> = rawGPUData;
    const monitorData: Record<any, Record<string, any>> = rawMonitorData;
</script>

<!-- GPU -->

<Widget title="GPU" modalId="gpu-modal">
    <div slot="values">
        <div class="widget-value">
            <div class="green">
                {#if (!monitorData)}
                    {gpuData[0]['Description']}
                {:else}
                    {monitorData[0]['Name']}
                {/if}
            </div>
            <div>Model</div>
        </div>
    </div>

    <div slot="modal-body" class="modal-body">

        {#if (!(gpuData == null))}
            <h5> GPU Info </h5>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">VRAM</th>
                        <th scope="col">Resolution</th>
                        <th scope="col">Refresh Rate</th>
                    </tr>
                </thead>
                <tbody>
                    {#each Object.values(gpuData) as gpu}
                        <tr>
                            <td>{gpu.Description}</td>
                            <td>{gpu.AdapterRAM / 1048576} MB</td>
                            <td>{gpu.CurrentHorizontalResolution} x {gpu.CurrentVerticalResolution}</td>
                            <td>{gpu.CurrentRefreshRate} Hz</td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        {/if}

        {#if (!(monitorData == null))}
            <h5> Monitor Info </h5>
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
                <tbody>
                    {#each Object.values(monitorData) as monitor}
                        <tr>
                            <td>{monitor.Name}</td>
                            <td>{monitor.DedicatedMemory} MB</td>
                            <td>{monitor.CurrentMode}</td>
                            <td>{monitor.MonitorModel} Hz</td>
                            <td>{monitor.ConnectionType}</td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        {/if}
    </div>
</Widget>