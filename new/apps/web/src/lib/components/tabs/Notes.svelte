<script lang="ts">
    import type { Report } from '$lib/common/report/report';
    import type { EolListEntry } from '$lib/common/interfaces';

    import { lists } from '$lib/common/lists';
	import { bytesToGigabytes } from '$lib/common/constants';

	interface Props {
		report: Report;
		eolList: Array<EolListEntry>;
	}

	let {
		report,
		eolList
	}: Props = $props();

    // EOL Check

    let valid: boolean = false, 
        latest: boolean = false, 
        insider: boolean = false;

    function eolCheck() {

        let windows11: boolean = false;
        let latestVersions: Array<string> = [];
        let validVersions: Array<string> = [];

        eolList.forEach((version: EolListEntry) => {
            // Windows 11
            if (
                !(version.lts)
                && !(version.cycle.includes("-e"))
                && (version.cycle.includes("11"))
                && !version.latest.includes("10.0.28000")
                && !windows11
            ){
                latestVersions.push(version.latest);
                windows11 = true;
            }

            if (
                !(version.lts)
                && new Date(version.support) > new Date()
            ) validVersions.push(version.latest)
        });

        if (latestVersions.includes(report.BasicInfo.Version)) latest = true;
        if (validVersions.includes(report.BasicInfo.Version)) valid = true;
        if (validVersions[0].split(".")[2] < report.BasicInfo.Version.split(".")[2]) insider = true;

    }

    eolCheck();

    // Uptime

    const days: number = Math.floor(report.BasicInfo.Uptime / 60 / 60 / 24),
        hours: number = Math.floor(report.BasicInfo.Uptime / 60 / 60 % 24), 
        minutes: number = Math.floor(report.BasicInfo.Uptime / 60 % 60), 
        seconds: number = report.BasicInfo.Uptime % 60;

    // Host file

    const hostsFileHash: string = "2D6BDFB341BE3A6234B24742377F93AA7C7CFB0D9FD64EFA9282C87852E57085";

    // Disk shenanigans

    let diskCapacityIds: Record<number, number> = [];

    report.Hardware.Storage.forEach((drive, driveId) => {
        let partitionTotal: number = 0;

        drive.Partitions.forEach((partition) => {
            partitionTotal += partition.PartitionCapacity;
        });

        if (Math.abs(Math.floor(partitionTotal / bytesToGigabytes) - Math.floor(drive.DiskCapacity / bytesToGigabytes)) > 5)
            diskCapacityIds[driveId] = partitionTotal;
    });
</script>

<div>
    <h1>General Notes</h1>

    <!--

        NON-SPECIFIC NOTES
    
    -->

    <li> <!-- OS -->
        The OS is 
        {#if insider}
            <p>Insider</p>
        {:else}
            <span>{ valid ? "Not EOL" : "EOL" }</span>{ (valid && !latest) ? ", but " : " and " }<span>{latest ? "Up-to-date" : "not Up-to-date"}</span>.
        {/if}
        <span>(version {report.BasicInfo.FriendlyVersion}, build {report.BasicInfo.Version})</span>
    </li>
    
    <li>
        <p>
            The current uptime is {days} days, {hours} hours, {minutes} minutes, and {seconds} seconds.
        </p>
    </li>

    <li>
        <p>
            {#if report.Security.AvList.length > 0}
                Currently Installed AVs are: 
                <span>
                    {#each report.Security.AvList as antivirus}
                        {antivirus}
                    {/each}
                </span>
            {:else}
                There are no installed AVs!
            {/if}
        </p>
    </li>

    {#if report.System.UsernameSpecialCharacters}
        <li>
            <p>
                Username found with <span>Special Characters!</span>
            </p>
        </li>
    {/if}

    {#if report.System.OneDriveCommercialPathLength != null}
        <li>
            <p>
                OneDrive Path Length: <span>{report.System.OneDriveCommercialPathLength}</span>
                OneDrive Name Length: <span>{report.System.OneDriveCommercialNameLength}</span>
            </p>
        </li>
    {/if}

    {#if report.System.RecentMinidumps != 0}
        <li>
            <p>
                There have been <span>{report.System.RecentMinidumps}</span> Minidumps found
            </p>
        </li>
    {/if}

    {#if hostsFileHash != report.Network.HostsFileHash}
        <li>
            <p>
                Hosts file has been modified from original.
            </p>
        </li>
    {/if}

    {#if report.Hardware.Batteries != null && report.Hardware.Batteries.length > 0}
        {#each report.Hardware.Batteries as battery}
            {#if battery.Remaining_Life_Percentage < 70}
                <li>
                    <p>
                        Hosts file has been modified from original.
                    </p>
                </li>
            {/if}
        {/each}
    {/if}

    {#if report.Events.UnexpectedShutdownCount > 0}
        <li>
            <p>
               <span>{report.Events.UnexpectedShutdownCount}</span> Unexpected Shutdowns found.
            </p>
        </li>
    {/if}

    {#if report.Events.MachineCheckExceptionCount > 0}
        <li>
            <p>
                <span>{report.Events.MachineCheckExceptionCount}</span> Machine Check Exceptions found.
            </p>
        </li>
    {/if}

    {#if report.Events.WheaErrorRecordCount > 0}
        <li>
            <p>
                <span>{report.Events.WheaErrorRecordCount}</span> WHEA errors found.
            </p>
        </li>
    {/if}

    {#if report.Events.PciWheaErrorCount > 0}
        <li>
            <p>
                <span>{report.Events.PciWheaErrorCount}</span> PCI WHEA errors found.
            </p>
        </li>
    {/if}

    {#if report.Meta.ElapsedTime > 200000}
        <li>
            <p>
                Specify runtime is over 20s
            </p>
        </li>
    {/if}

    {#if report.System.LimitedMemory}
        <li>
            <p>
                Device configured to use a limited amount of memory.
            </p>
        </li>
    {/if}

    {#if report.System.WindowsOld}
        <li>
            <p>
                <span>Windows.OLD</span> folder found.
            </p>
        </li>
    {/if}

    <!--
    
        DRIVE / PARTITION NOTES
    
    -->

    {#each report.Hardware.Storage as disk, diskId}
        {#each disk.SmartData as smartEntry}
            {#if smartEntry.Name.includes('!') && /[1-9]+/gm.test(smartEntry.RawValue)}
                <li>
                    <p>
                        {disk.DeviceName} (
                            {#each disk.Partitions as partition, partitionId} 
                                {#if partition.PartitionLetter != null} 
                                    {partition.PartitionLetter}
                                    {#if partitionId+1 < disk.Partitions.length}
                                    , 
                                    {/if}
                                {/if} 
                            {/each}
                            ) has {smartEntry.RawValue} on {smartEntry.Name}.
                    </p>
                </li>
            {/if}
        {/each}

        {#each disk.Partitions as partition, partitionId}
            {#if partition.DirtyBitSet}
                <li>
                    <p>
                        Dirty bit set on <span>{partition.PartitionLetter ? partitionId : partition.PartitionLetter} ({disk.DeviceName})</span>.
                    </p>
                </li>
            {/if}
        {/each}

        {#if diskId in diskCapacityIds}
            <li>
                <p>
                    {disk.DeviceName} (
                            {#each disk.Partitions as partition, partitionId} 
                                {#if partition.PartitionLetter != null} 
                                    {partition.PartitionLetter}
                                    {#if partitionId + 1 < disk.Partitions.length}
                                    , 
                                    {/if}
                                {/if} 
                            {/each}
                            ) has differing capacities. ({bytesToGigabytes(disk.DiskCapacity)} on disk vs. {bytesToGigabytes(diskCapacityIds[diskId])} on partitions.)
                </p>
            </li>
        {/if}
    {/each}

    <!--
    
        REGISTRY NOTES
    
    -->

    {#each report.System.ChoiceRegistryValues as regEntry}
        {#if regEntry.Value && !lists.defaultRegEntries[regEntry.Name].includes(regEntry.Value)}
            <li>
                <p>
                    Registry Value <span>{regEntry.Name}</span> found set, value of <span>{regEntry.Value}</span>.
                </p>
            </li>
        {/if}
    {/each}
</div>