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
</script>

<div class="widgets-widgets widgets" id="hardware-widgets" data-hide="false">
    <CPU/>
    <RAM/>
    <Motherboard/>
    <GPU/>
    <OS/>
    <NIC/>
</div>

<div class="widgets-widgets widgets" data-hide="false">
    <Drives/>
</div>

<div class="widgets-widgets widgets" data-hide="false">
    <CpuUsage/>
    <RamUsage/>
    <Temps/>
    <AudioDevices/>
    <PowerProfiles/>
</div>