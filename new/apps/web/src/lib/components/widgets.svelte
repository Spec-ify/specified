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
    <CPU data={rawJSON}/>
    <RAM data={rawJSON}/>
    <Motherboard data={rawJSON}/>
    <GPU data={rawJSON}/>
    <OS data={rawJSON}/>
    <NIC data={rawJSON}/>
</div>

<div class="widgets-widgets widgets" data-hide="false">
    <Drives/>
</div>

<div class="widgets-widgets widgets" data-hide="false">
    <CpuUsage data={rawJSON}/>
    <RamUsage data={rawJSON}/>
    <Temps/>
    <AudioDevices/>
    <PowerProfiles/>
</div>