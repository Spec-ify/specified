<script lang="ts">
    import { Tabs } from "@skeletonlabs/skeleton-svelte";
	import Notes from "./tabs/Notes.svelte";

	export let report;
	export let eolList;
</script>

<div class="tabbed-info">
    <Tabs defaultValue="notes">
        <Tabs.List>
            <Tabs.Trigger value="notes">Notes</Tabs.Trigger>
            <Tabs.Trigger value="notable-software">Notable Software</Tabs.Trigger>
            <Tabs.Trigger value="variables">Variables</Tabs.Trigger>
            <Tabs.Trigger value="browsers">Browsers</Tabs.Trigger>
            <Tabs.Trigger value="startup-tasks">Startup Tasks</Tabs.Trigger>
            <Tabs.Trigger value="windows-updates">Windows Updates</Tabs.Trigger>
            <Tabs.Indicator />
        </Tabs.List>

        <Tabs.Content value="notes">
            <Notes report={report} eolList={eolList}/>
        </Tabs.Content>
        
        <Tabs.Content value="notable-software">
            a
        </Tabs.Content>

        <Tabs.Content value="variables">
            b
        </Tabs.Content>
        
        <Tabs.Content value="browsers">
            c
        </Tabs.Content>
        
        <Tabs.Content value="startup-tasks">
            d
        </Tabs.Content>
        
        <Tabs.Content value="windows-updates">
            e
        </Tabs.Content>

    </Tabs> 
</div>