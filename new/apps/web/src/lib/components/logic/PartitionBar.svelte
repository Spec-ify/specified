<script lang="ts">
    import type { Partition } from "$lib/common/report/hardware";

    interface Props {
		partitions: Array<Partition>;
	}

	let {
		partitions
	}: Props = $props();

    const partitionTotal: number = partitions.reduce((total, part) => total + part.PartitionCapacity, 0)

</script>

<div class="min-h-12 max-h-16 max-w-[99%] self-center flex flex-row even:bg-surface-950/10 outline-surface-50/20 outline-2">
    {#each partitions as partition}
        <div class="outline-surface-50/20 outline-2 overflow-clip flex items-center justify-center flex-col max-h-[inherit]" style="width: {(partition.PartitionCapacity / partitionTotal) * 100}%;">
            <span class="max-h-[inherit] text-xs">{partition.PartitionLabel} ({partition.PartitionLetter}:)</span>
            <span class="max-h-[inherit] text-xs">{partition.Filesystem}</span>
            <span class="max-h-[inherit] text-xs">{Math.floor((partition.PartitionCapacity - partition.PartitionFree) / 1048576)} MB / {Math.floor(partition.PartitionCapacity / 1048576)} MB used</span>
        </div>
    {/each}
</div>