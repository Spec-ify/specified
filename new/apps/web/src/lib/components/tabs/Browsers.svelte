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

<div class="flex flex-row items-center justify-center">
    {#each report.System.BrowserExtensions as browser}
        <Widget title={browser.Name+(report.System.DefaultBrowser.includes(browser.Name.toLowerCase()) ? " (Default)" : "")}>
            {#snippet widgetContents()}
                <div class="w-full flex items-center justify-center">
                    <img class="w-12" alt="{browser.Name}" src="{browserImage(browser.Name)}">
                </div>
            {/snippet}

            {#snippet modalContents()}
                {#each browser.Profiles as profile}
                <div class="flex flex-col gap-4 mb-4">
                    <h1 class="text-3xl mb-2">{browser.Name} Profile "{profile.name}"</h1>

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

                </div>
                    
                {/each}
            {/snippet}
        </Widget>
    {/each}
</div>