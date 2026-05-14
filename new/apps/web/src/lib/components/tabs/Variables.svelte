<script lang="ts">
    export let report: any;

    console.log(typeof(report.System.UserVariables));
    
    const pathArray: Array<string> = report.System.UserVariables.Path.split(';'),
        regexMatch: RegExp = /^[C-Z]:/;
</script>

<div>
    <div>
        <h1>User Variables</h1>

        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                {#each Object.entries(report.System.UserVariables) as [userVariable, value]}
                    {#if userVariable != "Path"}
                        <tr>
                            <td>{userVariable}</td>
                            <td>{value}</td>
                        </tr>
                    {/if}
                {/each}
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Path Variables</th>
                </tr>
            </thead>
            <tbody>
                {#each pathArray as path}
                    {#if regexMatch.test(path)}
                        <tr>
                            <td>{path}</td>
                        </tr>
                    {/if}
                {/each}
            </tbody>
        </table>
    </div>

    <div>
        <h1>System Variables</h1>

        <table>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                {#each Object.entries(report.System.SystemVariables) as [systemVariable, value]}
                    {#if systemVariable != "Path"}
                        <tr>
                            <td>{systemVariable}</td>
                            <td>{value}</td>
                        </tr>
                    {/if}
                {/each}
            </tbody>
        </table>
    </div>
</div>