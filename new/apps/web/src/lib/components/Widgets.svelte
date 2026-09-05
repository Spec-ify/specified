<script lang="ts">
	import type { Report } from '$lib/common/report/report';

	import Cpu from './widgets/Cpu.svelte';
	import Ram from './widgets/Ram.svelte';
	import Motherboard from './widgets/Motherboard.svelte';
	import Gpu from './widgets/Gpu.svelte';
	import Os from './widgets/Os.svelte';
	import Nic from './widgets/Nic.svelte';

	import Drives from './widgets/Drives.svelte';

	import CpuUsage from './widgets/CpuUsage.svelte';
	import RamUsage from './widgets/RamUsage.svelte';
	import Temps from './widgets/Temps.svelte';
	import AudioDevices from './widgets/AudioDevices.svelte';
	import PowerProfiles from './widgets/PowerProfiles.svelte';

	interface Props {
		report: Report;
		cpuMoreInfo: Response;
	}

	let {
		report,
		cpuMoreInfo
	}: Props = $props();
</script>

<div class="flex flex-col gap-4 min-w-full">
	<div class="flex items-stretch justify-evenly flex-wrap gap-4 w-full">
		<Cpu cpu={report.Hardware.Cpu} cpuMoreInfo={cpuMoreInfo}/>
		<Ram ram={report.Hardware.Ram} pagefile={report.System.PageFile}/>
		<Motherboard
			tpm={report.Security.Tpm}
			motherboard={report.Hardware.Motherboard}
			bios={report.Hardware.BiosInfo}
		/>
		<Gpu gpus={report.Hardware.Gpu} monitors={report.Hardware.Monitors} />
		<Os security={report.Security} basic={report.BasicInfo} />
		<Nic nics={report.Network.Adapters} />
	</div>

	<div class="flex items-stretch justify-evenly flex-wrap gap-4 w-full">
		<Drives drives={report.Hardware.Storage}/>
	</div>

	<div class="flex items-stretch justify-evenly flex-wrap gap-4 w-full">
		<CpuUsage cpuLoad={report.Hardware.Cpu.LoadPercentage}/>
		<RamUsage runningProcesses={report.System.RunningProcesses} ram={report.Hardware.Ram}></RamUsage>
		<AudioDevices audioDevices={report.Hardware.AudioDevices}/>
		<!--
		<Temps temps={report.Hardware.Temperatures}/>
		-->
		<PowerProfiles powerProfiles={report.System.PowerProfiles} batteries={report.Hardware.Batteries}/>
	</div>

</div>