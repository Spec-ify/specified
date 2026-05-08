<script lang="ts">
    import { lists } from '$lib/common/lists';

    interface InstalledApp {
        Name: string,
        Version: string,
        InstallDate: string | null
    }

    interface RunningProcess {
        ProcessName: string,
        Count: number,
        ExePath: string,
        Id: number,
        WorkingSet: number,
        CpuPercent: number
    }

    export let report: any;

    let pupsInstalled: Array<string> = [],
        pupsRunning: Array<string> = [];

    report.System.InstalledApps.forEach((installedApp: InstalledApp) => {
        lists.notableSoftwareList.forEach((pupEntry) => {
            const stringMatch: string = pupEntry.toLowerCase(),
                    pattern: RegExp = new RegExp(`(${stringMatch})`, "i");

            if (pattern.test(installedApp.Name))
                pupsInstalled.push(installedApp.Name);
        });
    }); 

    report.System.RunningProcesses.forEach((runningProcess: RunningProcess) => {
        lists.notableSoftwareList.forEach((pupEntry) => {
            const stringMatch: string = pupEntry.toLowerCase(),
                    pattern: RegExp = new RegExp(`(${stringMatch})`, "i");

            if (pattern.test(runningProcess.ProcessName))
                pupsRunning.push(runningProcess.ProcessName);
        });
    });

    pupsInstalled = Array.from(new Set(pupsInstalled));
    pupsRunning = Array.from(new Set(pupsRunning));

</script>

<div>
    <h1>Notable Software</h1>

    {#if pupsInstalled.length > 0}
        {#each pupsInstalled as app}
            <li>
                <p>
                    {app} Found Installed.
                </p>
            </li>
        {/each}
    {:else}
        <li>
            <p>
                No notable software found installed.
            </p>
        </li>
    {/if}

    {#if pupsRunning.length > 0}
        {#each pupsRunning as app}
            <li>
                <p>
                    {app} Found running.
                </p>
            </li>
        {/each}
    {:else}
        <li>
            <p>
                No notable software found running.
            </p>
        </li>
    {/if}
</div>