<script lang="ts">
    import type { Report } from "$lib/common/report/report";
	import TabledData from '$lib/common/TabledData.svelte';
	import { Accordion } from '@skeletonlabs/skeleton-svelte';

    interface Props {
		report: Report;
	}

	let {
		report,
	}: Props = $props();

    const errorTables: boolean = 
            report.Events.UnexpectedShutdowns.length > 0 ||
            report.Events.MachineCheckExceptions.length > 0 ||
            report.Events.WheaErrorRecords.length > 0 ||
            report.Events.PciWheaErrors.length > 0;
</script>

<div class="flex flex-col gap-4">
    <!-- Devices, Drivers -->
    <div class="card bg-surface-900 p-4">
        <!-- Devices -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='devices'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Devices
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.Hardware.Devices} headers={Object.keys(report.Hardware.Devices[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        
        <!-- Drivers -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='drivers'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Drivers
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.Hardware.Drivers} headers={Object.keys(report.Hardware.Drivers[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
    </div>

    <!-- Running Processes, Installed Apps, Installed Windows Store Packages -->
    <div class="card bg-surface-900 p-4">
        <!-- Running Processes -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='running-proc'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Running Processes
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.System.RunningProcesses} headers={Object.keys(report.System.RunningProcesses[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        
        <!-- Installed Apps -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='installed-apps'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Installed Apps
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.System.InstalledApps} headers={Object.keys(report.System.InstalledApps[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        
        <!-- Installed Windows Store Packages -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='installed-wsp'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Installed Windows Store Packages
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.System.WindowsStorePackages} headers={Object.keys(report.System.WindowsStorePackages[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
    </div>

    <!-- Services, Tasks -->
    <div class="card bg-surface-900 p-4">
        <!-- Services -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='services'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Services
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.System.Services} headers={Object.keys(report.System.Services[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        
        <!-- Tasks -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='tasks'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Tasks
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.System.ScheduledTasks} headers={Object.keys(report.System.ScheduledTasks[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
    </div>


    <!-- Error Tables -->
    {#if errorTables}
    <div class="card bg-surface-900 p-4">
        <!-- Unexpected Shutdowns -->
        {#if report.Events.UnexpectedShutdowns.length > 0}
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='unexp-sd'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Unexpected Shutdowns
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.System.Services} headers={Object.keys(report.System.Services[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        {/if}
        
        <!-- Machine Check Exceptions -->
        {#if report.Events.MachineCheckExceptions.length > 0}
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='mce'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Machine Check Exceptions
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.Events.MachineCheckExceptions} headers={Object.keys(report.Events.MachineCheckExceptions[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        {/if}
        
        <!-- WHEA Error Records -->
        {#if report.Events.MachineCheckExceptions.length > 0}
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='whea'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            WHEA Error Records
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.Events.WheaErrorRecords} headers={Object.keys(report.Events.WheaErrorRecords[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        {/if}
        
        <!-- PCI WHEA Errors -->
        {#if report.Events.PciWheaErrors.length > 0}
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='pci-whea'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            PCI WHEA Errors
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.Events.PciWheaErrors} headers={Object.keys(report.Events.PciWheaErrors[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        {/if}
    </div>
    {/if}

    <!-- Network -->
    <div class="card bg-surface-900 p-4">
        <!-- Network Connections -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='network-connections'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Network Connections
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.Network.NetworkConnections} headers={Object.keys(report.Network.NetworkConnections[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        
        <!-- Routes Table -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='routes'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Routes
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <TabledData data={report.Network.Routes} headers={Object.keys(report.Network.Routes[0])} params="paginate search"/>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
        
        <!-- Hosts File -->
        <Accordion class="mb-2" collapsible>
            <Accordion.Item value='hosts'>
                <Accordion.ItemTrigger class="min-w-full text-start">
                    <div class="hover:bg-primary-900 rounded-sm">
                        <h3 class="p-2 px-4 text-base">
                            Hosts File
                        </h3>
                    </div>
                </Accordion.ItemTrigger>
                <Accordion.ItemContent class="card outline-2 outline-surface-50/20 mt-2 p-4">
                    <code>
                        {report.Network.HostsFile}
                    </code>
                </Accordion.ItemContent>
            </Accordion.Item>
        </Accordion>
    </div>

</div>