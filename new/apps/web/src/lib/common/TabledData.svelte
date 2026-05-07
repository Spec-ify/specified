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

<div class="table-wrap">
    {#if params.length > 0}
        <div class="table-options">
            {#if paginate}
                <label class="label">
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
        </div>

        {#if search}
            <label class="label">
                <span class="label-text">Search: </span>
                <input bind:value={searchTerm} class="input" type="text" placeholder="Input" />
            </label>
        {/if}
    {/if}
    
    <table class="table caption-bottom">
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
        <Pagination count={finalData.length} pageSize={pageSize} {currentPage} onPageChange={(event) => (currentPage = event.page)}>
		<Pagination.PrevTrigger>
			<a>Previous</a>
		</Pagination.PrevTrigger>
		<Pagination.Context>
			{#snippet children(pagination)}
				{#each pagination().pages as page, index (page)}
					{#if page.type === 'page'}
						<Pagination.Item {...page}>
							{page.value}
						</Pagination.Item>
					{:else}
						<Pagination.Ellipsis {index}>&#8230;</Pagination.Ellipsis>
					{/if}
				{/each}
			{/snippet}
		</Pagination.Context>
		<Pagination.NextTrigger>
			<a onclick={console.log(end)}>Next</a>
		</Pagination.NextTrigger>
	</Pagination>
    {/if}
</div>