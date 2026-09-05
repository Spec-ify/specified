<script lang="ts">
    import type { Report } from "$lib/common/report/report";
    import type { EolListEntry } from "$lib/common/interfaces";

    import { Tabs } from "@skeletonlabs/skeleton-svelte";
    import Notes from "./tabs/Notes.svelte";
    import NotableSoftware from "./tabs/NotableSoftware.svelte";
    import WindowsUpdate from "./tabs/WindowsUpdate.svelte";
    import StartupTasks from "./tabs/StartupTasks.svelte";
    import Browsers from "./tabs/Browsers.svelte";
    import Variables from "./tabs/Variables.svelte";


	interface Props {
		report: Report;
		eolList: Array<EolListEntry>;
	}

	let {
		report,
		eolList
	}: Props = $props();
</script>

<div class="card bg-surface-900 p-4" >
    <Tabs class="p-4" defaultValue="notes">
        <Tabs.List class="mb-4">
            <Tabs.Trigger class="btn bg-surface-800/50 text-sm" value="notes">Notes</Tabs.Trigger>
            <Tabs.Trigger class="btn bg-surface-800/50 text-sm" value="notable-software">Notable Software</Tabs.Trigger>
            <Tabs.Trigger class="btn bg-surface-800/50 text-sm" value="variables">Variables</Tabs.Trigger>
            <Tabs.Trigger class="btn bg-surface-800/50 text-sm" value="browsers">Browsers</Tabs.Trigger>
            <Tabs.Trigger class="btn bg-surface-800/50 text-sm" value="startup-tasks">Startup Tasks</Tabs.Trigger>
            <Tabs.Trigger class="btn bg-surface-800/50 text-sm" value="windows-updates">Windows Updates</Tabs.Trigger>
            <Tabs.Indicator />
        </Tabs.List>

        <Tabs.Content value="notes">
            <Notes report={report} eolList={eolList}/>
        </Tabs.Content>
        
        <Tabs.Content value="notable-software">
            <NotableSoftware report={report}/>
        </Tabs.Content>

        <Tabs.Content value="variables">
            <Variables report={report}/>
        </Tabs.Content>
        
        <Tabs.Content value="browsers">
            <Browsers report={report}/>
        </Tabs.Content>
        
        <Tabs.Content value="startup-tasks">
            <StartupTasks report={report}/>
        </Tabs.Content>
        
        <Tabs.Content value="windows-updates">
            <WindowsUpdate report={report}/>
        </Tabs.Content>

    </Tabs> 
</div>