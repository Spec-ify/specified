<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	export let report;

    function browserImage(name: string) {
        const browsers: Array<string> = ["chrome", "firefox", "edge", "opera", "brave", "vivaldi"],
                test: string = name.toLowerCase(),
                image: string = browsers.includes(test) ? `assets/${test}.png` : "#";;

        return image;
    }

</script>

<div>
    {#each report.System.BrowserExtensions as browser}
        <Widget title={browser.Name+(report.System.DefaultBrowser.includes(browser.Name.toLowerCase()) ? "(Default)" : "")}>
            {#snippet widgetContents()}
                <div class="widget-contents">
                    <img class="center" height="48px" width="48px" src="{browserImage(browser.Name)}">
                </div>
            {/snippet}

            {#snippet modalContents()}
                {#each browser.Profiles as profile}
                    <h1>{browser.Name} Profile "{profile.name}"</h1>

                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Version</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            {#each profile.Extensions as extension}
                                <tr>
                                    <td>{extension.name}</td>
                                    <td>{extension.version}</td>
                                    <td>{extension.description}</td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                {/each}
            {/snippet}
        </Widget>
    {/each}
</div>