<script lang="ts">
    export let report: any;
</script>

<div class="flex flex-col overflow-auto">
    <table>
        <thead>
            <tr>
                <th>App Name</th>
                <th>App Path</th>
                <th>Timestamp</th>
            </tr>
        </thead>

        <tbody>
            {#each report.System.StartupTasks as task}
                <tr>
                    <td>{task.AppName}</td>
                    <td>{task.ImagePath}</td>
                    <td>{task.Timestamp}</td>
                </tr>
            {/each}
        </tbody>
    </table>
</div>