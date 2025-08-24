<script lang="ts">
	import CPU from './widgets/Cpu.svelte';
	import RAM from './widgets/Ram.svelte';
	import Motherboard from './widgets/Motherboard.svelte';
	import GPU from './widgets/Gpu.svelte';
	import OS from './widgets/Os.svelte';
	import NIC from './widgets/Nic.svelte';

	import Drives from './widgets/Drives.svelte';

	import CpuUsage from './widgets/CpuUsage.svelte';
	import RamUsage from './widgets/RamUsage.svelte';
	import Temps from './widgets/Temps.svelte';
	import AudioDevices from './widgets/AudioDevices.svelte';
	import PowerProfiles from './widgets/PowerProfiles.svelte';

	export let rawJSON;
</script>

<div class="widgets-widgets widgets" id="hardware-widgets" data-hide="false">
	<CPU cpuData={rawJSON.Hardware.Cpu} />
	<RAM ramData={rawJSON.Hardware.Ram} pagefileData={rawJSON.System.PageFile} />
	<Motherboard
		tpmData={rawJSON.Security.Tpm}
		motherboardData={rawJSON.Hardware.Motherboard}
		biosData={rawJSON.Hardware.BiosInfo}
	/>
	<GPU rawGPUData={rawJSON.Hardware.Gpu} rawMonitorData={rawJSON.Hardware.Monitors} />
	<OS securityData={rawJSON.Security} basicinfoData={rawJSON.BasicInfo} />
	<NIC nicData={rawJSON.Network.Adapters} />
</div>

<div class="widgets-widgets widgets" data-hide="false">
	<Drives />
</div>

<div class="widgets-widgets widgets" data-hide="false">
	<CpuUsage cpuloadData={rawJSON.Hardware.Cpu.LoadPercentage} />
	<RamUsage runprocData={rawJSON.System.RunningProcesses} ramData={rawJSON.Hardware.Ram} />
	<Temps />
	<AudioDevices />
	<PowerProfiles />
</div>
