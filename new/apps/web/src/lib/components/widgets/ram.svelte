<script lang="ts">
    import Widget from '../../common/modal-widget.svelte';
    import Rambuilder from '../logic/rambuilder.svelte';
    
    export let data;
</script>

<!-- RAM -->

<Widget title="Memory" modalId="ram-modal">
    <div slot="values">
        <Rambuilder data={data}/>
    </div>

    <div slot="modal-body">
        <h5>Physical Memory</h5>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">DIMM</th>
                    <th scope="col">Manufacturer</th>
                    <th scope="col">Model</th>
                    <th scope="col">Speed</th>
                    <th scope="col">Capacity</th>
                </tr>
            </thead>
            <tbody>
                {#each data.Hardware.Ram as ramStick}
                    {#if (ramStick['Capacity'] <= 0)}
                        <tr>
                            <td>{ramStick['DeviceLocation']}</td>
                            <td colspan="4" class="td-center">Not Detected</td>
                        </tr>
                    {:else}
                        <tr>
                            <td>{ramStick['DeviceLocation']}</td>
                            <td>{ramStick['Manufacturer']}</td>
                            <td>{ramStick['PartNumber']}</td>
                            <td>{ramStick['ConfiguredSpeed']} MHz</td>
                            <td>{ramStick['Capacity']} MB</td>
                        </tr>
                    {/if}
                {/each}
            </tbody>
        </table>
        <h5>Pagefile</h5>
        <table class="table">
            <tbody>
                <tr>
                    <td>File Path</td>
                    <td>{data.System.PageFile.Caption}</td>
                </tr>
                <tr>
                    <td>Allocated Base Size</td>
                    <td>{data.System.PageFile.AllocatedBaseSize} MB</td>
                </tr>
                <tr>
                    <td>Current Usage</td>
                    <td>{data.System.PageFile.CurrentUsage} MB</td>
                </tr>
                <tr>
                    <td>Peak Usage</td>
                    <td>{data.System.PageFile.PeakUsage} MB</td>
                </tr>
            </tbody>
        </table>
    </div>
</Widget>