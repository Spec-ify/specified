<script lang="ts">
    import { Tabs } from "@skeletonlabs/skeleton-svelte";
</script>

<div class="tabbed-info">
    <Tabs>
        <Tabs.List>
            <Tabs.Trigger value="notes"></Tabs.Trigger>
            <Tabs.Trigger value="notable-software"></Tabs.Trigger>
            <Tabs.Trigger value="variables"></Tabs.Trigger>
            <Tabs.Trigger value="browsers"></Tabs.Trigger>
            <Tabs.Trigger value="startup-tasks"></Tabs.Trigger>
            <Tabs.Trigger value="windows-updates"></Tabs.Trigger>
        </Tabs.List>

        <Tabs.Content value="notes">
            
        </Tabs.Content>
        
        <Tabs.Content value="notable-software">

        </Tabs.Content>

        <Tabs.Content value="variables">

        </Tabs.Content>
        
        <Tabs.Content value="browsers">

        </Tabs.Content>
        
        <Tabs.Content value="startup-tasks">

        </Tabs.Content>
        
        <Tabs.Content value="windows-updates">

        </Tabs.Content>

    </Tabs> 
</div>