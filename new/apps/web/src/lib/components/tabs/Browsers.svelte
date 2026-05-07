<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	export let report;

    function browserImage(name: string) {
        const test: string = name.toLowerCase();
        let image: string = "";

        switch (test) {
            case "chrome":
                image = "assets/chrome.png";
                break;

            case "firefox":
                image = "assets/firefox.png";
                break;

            case "edge":
                image = "assets/edge.png";
                break;

            case "opera":
                image = "assets/opera.png";
                break;

            case "brave":
                image = "assets/brave.png";
                break;

            case "vivaldi":
                image = "assets/vivaldi.png";
                break;

            default:
                image = "#";
                break;

        }

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