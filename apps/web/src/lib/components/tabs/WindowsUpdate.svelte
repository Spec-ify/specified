<script lang="ts">
    export let report: any;
</script>

<div class="flex flex-col overflow-auto">
    <table>
        <thead>
            <tr>
                <th>Update</th>
                <th>Install Date</th>
            </tr>
        </thead>

        <tbody>
            {#each report.System.InstalledHotfixes as hotfix}
                <tr>
                    <td>{hotfix.HotFixID}</td>
                    <td>{hotfix.InstalledOn}</td>
                </tr>
            {/each}
        </tbody>
    </table>
</div>