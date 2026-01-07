<script lang="ts">
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

	export let report;
	export let cpuMoreInfo;
</script>

<div class="widgets">
	<Cpu cpu={report.Hardware.Cpu} cpuMoreInfo={cpuMoreInfo}/>
	<Motherboard
		tpm={report.Security.Tpm}
		motherboard={report.Hardware.Motherboard}
		bios={report.Hardware.BiosInfo}
	/>
	<Ram ram={report.Hardware.Ram} pagefile={report.System.PageFile}/>
	<Gpu gpus={report.Hardware.Gpu} monitors={report.Hardware.Monitors} />
	<Nic nics={report.Network.Adapters} />
	<!--
	<OS securityData={rawJSON.Security} basicinfoData={rawJSON.BasicInfo} />
-->
</div>

<div class="widgets">
	<Drives drives={report.Hardware.Storage}/>
</div>

<div class="widgets">
	<!-- <CpuUsage cpuloadData={rawJSON.Hardware.Cpu.LoadPercentage} />
	<RamUsage runprocData={rawJSON.System.RunningProcesses} ramData={rawJSON.Hardware.Ram} />
	<Temps /> -->
	<AudioDevices audioDevices={report.Hardware.AudioDevices}/>
	<!-- <PowerProfiles /> -->
</div>

<style>
	.widgets {
		display: flex;
		justify-content: space-evenly;
		flex-wrap: wrap;
		gap: 10px;
		margin-bottom: 10px;
	}
</style>