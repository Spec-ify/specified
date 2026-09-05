<script lang="ts">

    import { Pagination } from '@skeletonlabs/skeleton-svelte';
	
	interface Props {

        // data={array}
		data: Array<object>;
        
        // headers={Object.keys(array[0])}
        headers: Array<string>;
        
        // class-style attribute system
        // params="paginate search"
        // params="paginate"
        // params="search"
        params: string;

	}

	let {
		data,
        headers,
        params,
	}: Props = $props();

    // SETUP PARAMETERS

    let search: boolean = $state(false),
        paginate: boolean = $state(false);
    
    if (params.includes("search"))
        search = true;

    if (params.includes("paginate"))
        paginate = true;

    // Pagination and Search Variables
    let currentPage: number = $state(1),
        pageSize: number = $state(10),
        searchTerm: string = $state('');

    // Search filtering
    let finalData = $derived(searchTerm == "" ? data : data.filter(dataEntry => 
            Object.values(dataEntry).some(val => 
                String(val).toLowerCase().includes(searchTerm.toLowerCase())
        )
    ))

    // Pagination
    let start = $derived((currentPage - 1) * pageSize),
        end = $derived(start + Number(pageSize)),
        paginatedUsers = $derived(finalData.slice(start, end));

</script>

<div class="overflow-auto max-w-full flex flex-col">
    {#if params.length > 0}
        <div class="flex flex-row justify-between">
            {#if paginate}
                <label class="max-w-32 label">
                    <span class="label-text">Page size</span>
                    <select bind:value={pageSize} class="select">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </label>
            {/if}

            {#if search}
                <label class="max-w-64 label">
                    <span class="label-text">Search: </span>
                    <input bind:value={searchTerm} class="input" type="text" placeholder="Input" />
                </label>
            {/if}
        </div>
    {/if}
    
    <table class="overflow-auto table caption-bottom">
        <thead>
            <tr>
                {#each headers as header}
                    <th>{header}</th>
                {/each}
            </tr>
        </thead>

        <tbody class="[&>tr]:hover:preset-tonal-primary">
            {#each paginatedUsers as dataEntry}
                <tr>
                    {#each headers as header}
                        <td>{dataEntry[header]}</td>
                    {/each}
                </tr>
            {/each}
        </tbody>
    </table>

    {#if paginate}
    <div class="min-w-full flex items-end justify-end">
        <Pagination class="flex gap-1 flex-row" count={finalData.length} pageSize={pageSize} {currentPage} onPageChange={(event) => (currentPage = event.page)}>
            <Pagination.PrevTrigger class="btn bg-surface-800/50 text-sm">
                <a>Previous</a>
            </Pagination.PrevTrigger>
            <Pagination.Context>
                {#snippet children(pagination)}
                    <div class="flex flex-row gap-1">
                        {#each pagination().pages as page, index (page)}
                            {#if page.type === 'page'}
                                <Pagination.Item class="cursor-pointer btn bg-surface-800/50 text-sm" {...page}>
                                    <a>{page.value}</a>
                                </Pagination.Item>
                            {:else}
                                <Pagination.Ellipsis {index}>&#8230;</Pagination.Ellipsis>
                            {/if}
                        {/each}
                    </div>
                {/snippet}
            </Pagination.Context>
            <Pagination.NextTrigger class="btn bg-surface-800/50 text-sm">
                <a onclick={console.log(end)}>Next</a>
            </Pagination.NextTrigger>
        </Pagination>
    </div>
    {/if}
</div>