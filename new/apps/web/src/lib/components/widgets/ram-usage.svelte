<script lang="ts">
    import Widget from './modal-widget.svelte';

    export let data;

    let workingSet: number = 0;

    data.System.RunningProcesses.forEach((process: any) => {
        workingSet += process.WorkingSet;
    });

    let ramUsed = Math.round((workingSet / 1073741824) * 100) / 100;
    let totalRam: number = 0;
    let ramUsedPercent: number = 0; 

    if (data.Hardware.Ram){
        data.Hardware.Ram.forEach((stick: any) => {
            let capacity = stick.Capacity;
            if (capacity > 0){
                totalRam += Math.floor(capacity / 1024);
            }
        });
        ramUsedPercent = Math.round((ramUsed / totalRam) * 100);
    }
</script>

<!-- RAM Usage -->
<Widget title="Memory Usage" type="" modalId="memory-usage-modal">
    <div slot="values">
        <div class="widget-value">
            <div class="widget-single-value">
                <span class="green">
                    {ramUsed} GB
                </span>
                <span>/</span>
                <span>
                    {totalRam} GB
                </span>
            </div>
            <div>
                {ramUsedPercent}%
            </div>
        </div>
    </div>
</Widget>