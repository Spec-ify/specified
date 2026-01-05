<script lang="ts">

    interface PartitionInfo {
        PartitionCapacity: number;
        PartitionFree: number;
        PartitionLabel: string;
        PartitionLetter: null;
        Filesystem: string;
        CfgMgrErrorCode: number;
        LastErrorCode: number;
        DirtyBitSet: boolean;
        BitlockerEncryptionStatus: boolean;
    }

    interface Props {
		partitions: Array<PartitionInfo>;
	}

	let {
		partitions
	}: Props = $props();

    const partitionTotal: number = partitions.reduce((total, part) => total + part.PartitionCapacity, 0)

</script>

<div class="partition-bar">
    {#each partitions as partition}
        <div class="partition" style="width: {(partition.PartitionCapacity / partitionTotal) * 100}%;">
            <span>{partition.PartitionLabel} ({partition.PartitionLetter}:)</span>
            <span>{partition.Filesystem}</span>
            <span>{Math.floor((partition.PartitionCapacity - partition.PartitionFree) / 1048576)} MB / {Math.floor(partition.PartitionCapacity / 1048576)} MB used</span>
        </div>
    {/each}
</div>

<style>
    
    .partition-bar {
        min-height: 3rem;
        max-height: 4rem;       
        display: flex;
        flex-flow: row;
    }

    .partition-bar > div {
        min-height: inherit;
        border: 1px solid #FFFFFF22;
    }

    .partition-bar > div:nth-child(even){
        background-color: #00000022;
    }

    .partition {
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: middle;
        flex-flow: column;
        max-height: inherit;
    }

    .partition > span {
        max-height: inherit;
        font-size: 0.75rem;
    }
</style>