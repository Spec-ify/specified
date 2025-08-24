<script lang="ts">
    import CPU from './widgets/cpu.svelte';
    import RAM from './widgets/ram.svelte';
    import Motherboard from './widgets/motherboard.svelte';
    import GPU from './widgets/gpu.svelte';
    import OS from './widgets/os.svelte';
    import NIC from './widgets/nic.svelte';

	import Drives from './widgets/drives.svelte';

    import CpuUsage from './widgets/cpu-usage.svelte';
    import RamUsage from './widgets/ram-usage.svelte';
    import Temps from './widgets/temps.svelte';
    import AudioDevices from './widgets/audio-devices.svelte';
    import PowerProfiles from './widgets/power-profiles.svelte';

    export let rawJSON;
</script>

<div class="widgets-widgets widgets" id="hardware-widgets" data-hide="false">
    <CPU cpuData={rawJSON.Hardware.Cpu}/>
    <RAM ramData={rawJSON.Hardware.Ram} pagefileData={rawJSON.System.PageFile}/>
    <Motherboard tpmData={rawJSON.Security.Tpm} motherboardData={rawJSON.Hardware.Motherboard} biosData={rawJSON.Hardware.BiosInfo}/>
    <GPU rawGPUData={rawJSON.Hardware.Gpu} rawMonitorData={rawJSON.Hardware.Monitors}/>
    <OS securityData={rawJSON.Security} basicinfoData={rawJSON.BasicInfo}/>
    <NIC nicData={rawJSON.Network.Adapters}/>
</div>

<div class="widgets-widgets widgets" data-hide="false">
    <Drives/>
</div>

<div class="widgets-widgets widgets" data-hide="false">
    <CpuUsage cpuloadData={rawJSON.Hardware.Cpu.LoadPercentage}/>
    <RamUsage runprocData={rawJSON.System.RunningProcesses} ramData={rawJSON.Hardware.Ram}/>
    <Temps/>
    <AudioDevices/>
    <PowerProfiles/>
</div>