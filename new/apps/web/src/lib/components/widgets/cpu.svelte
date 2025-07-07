<script lang="ts">
    import Widget from './modal-widget.svelte';

    export let data;
</script>

<!-- CPU -->
<Widget title="CPU" modalId="cpu-modal">
    <div slot="values">
        <div class="green">
            {data.Hardware.Cpu.Name}
        </div>
        <div>Callsign</div>
    </div>

    <table slot="modal-body" class="table">
        <tbody>
            <tr>
                <td>Name</td>
                <td>{data.Hardware.Cpu.Name}</td>
            </tr>
            <tr>
                <td>Manufacturer</td>
                <td>{data.Hardware.Cpu.Manufacturer}</td>
            </tr>
            <tr>
                <td>Socket Designation</td>
                <td>{data.Hardware.Cpu.SocketDesignation}</td>
            </tr>
            <tr>
                <td>Current Clock Speed</td>
                <td>{data.Hardware.Cpu.CurrentClockSpeed}</td>
            </tr>
            <tr>
                <td># of Enabled Cores</td>
                <td>{data.Hardware.Cpu.NumberOfEnabledCore}</td>
            </tr>
            <tr>
                <td>Thread Count</td>
                <td>{data.Hardware.Cpu.ThreadCount}</td>
            </tr>
        </tbody>
    </table>

    <div slot="extras" class="modal-body" id="cpu-info-table" style="display:none;">
        <!-- This content is populated javascript side -->
        <h6 class="modal-title" id="cpu-info-title">Database results for: ...</h6>
        <table class="table">
            <tbody id="fetched-cpu-info">

            </tbody>
        </table>
    </div>
</Widget>