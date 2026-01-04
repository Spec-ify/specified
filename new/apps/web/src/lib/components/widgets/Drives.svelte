<!-- NOT YET MIGRATED TO NEW WIDGET SYSTEM -->
<script lang="ts">
	import { isPartiallyEmittedExpression } from 'typescript';
	import Widget from '../../common/ModalWidget.svelte';
	import Widgets from '../Widgets.svelte';
    
    interface partitionInfo {
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

    interface smartInfo {
        Id: number;
        Name: string;
        RawValue: string;
    }
    
    interface driveInfo {
        DeviceName: string;
        SerialNumber: string;
        DiskNumber: number;
        DiskCapacity: number;
        DiskFree: number;
        BlockSize: number;
        MediaType: string;
        InterfaceType: string;
        PartitionScheme: string;
        Partitions: Array<partitionInfo>;
        SmartData: Array<smartInfo>;
    }

    interface driveProcessedInfo {
        partitionCapacityTotal: number;
        partitionFreeTotal: number;
        capacityMatch: boolean;
        data: driveInfo;
    }
    
    interface Props {
		drives: Array<driveInfo>;
	}

	let {
		drives
	}: Props = $props();

    let drivesProcessed: Array<driveProcessedInfo> = []; 

    drives.forEach((drive: driveInfo)=>{
        
        let partitionCapacityTotal: number = 0, partitionFreeTotal: number = 0;
        drive.Partitions.forEach((partition: partitionInfo)=>{
            partitionCapacityTotal += partition.PartitionCapacity;
            partitionFreeTotal += partition.PartitionFree;
        })
        
        let inpDrive: driveProcessedInfo = {
            partitionCapacityTotal: partitionCapacityTotal,
            partitionFreeTotal: partitionFreeTotal,
            capacityMatch: (partitionCapacityTotal == drive.DiskCapacity ? true : false),
            data: drive,
        }

        drivesProcessed.push(inpDrive);
    })

</script>

{#each drivesProcessed as drive}
    <Widget title={drive.data.DeviceName}>
        {#snippet widgetContents()}
            <span>{Math.round((drive.data.DiskCapacity - drive.partitionFreeTotal) / 1073741824 )} GB / {Math.floor((drive.data.DiskCapacity) / 1073741824 )} GB</span>
            <div>{Math.round(((drive.data.DiskCapacity - drive.partitionFreeTotal) / drive.data.DiskCapacity) * 100)}%</div>
        {/snippet}
        
        {#snippet modalContents()}
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>SN</td>
                            <td>#</td>
                            <td>Capacity</td>
                            <td>Free</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{drive.data.DeviceName}</td>
                            <td>{drive.data.SerialNumber}</td>
                            <td>{drive.data.DiskNumber}</td>
                            <td>{Math.floor(drive.data.DiskCapacity / 1073741824)} GB</td>
                            <td>{Math.floor(drive.data.DiskFree / 1073741824)} GB</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <h6>Partitions</h6>
                <!-- TO-DO: PARTITION BAR -->
                <table class="table">
                    <thead>
                        <tr>
                            <td>Label</td>
                            <td>Letter</td>
                            <td>Capacity</td>
                            <td>Free</td>
                            <td>FS Type</td>
                            <td>CfgMgr Error Code</td>
                            <td>Last Error Code</td>
                            <td>Dirty Bit</td>
                        </tr>
                    </thead>
                    <tbody>
                        {#each drive.data.Partitions as partition}
                            <tr>
                                <td>{partition.PartitionLabel}</td>
                                <td>{partition.PartitionLetter}</td>
                                <td>{Math.floor(partition.PartitionCapacity / 1048576)} MB</td>
                                <td>{Math.floor(partition.PartitionFree / 1048576)} MB</td>
                                <td>{partition.Filesystem}</td>
                                <td>{partition.CfgMgrErrorCode}</td>
                                <td>{partition.LastErrorCode}</td>
                                <td>{partition.DirtyBitSet}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>

            <div>
                <h6>SMART</h6>
                <table class="table">
                    <thead>
                        <tr>
                            <td>Index</td>
                            <td>Name</td>
                            <td>Value</td>
                        </tr>
                    </thead>
                    <tbody>
                        {#each drive.data.SmartData as smartVal}
                            <tr>
                                <td>{smartVal.Id}</td>
                                <td>{smartVal.Name}</td>
                                <td>{smartVal.RawValue}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        {/snippet}
    </Widget>
{/each}

<style>
	span {
		color: var(--color-secondary-50);
	}

	div {
		color: var(--color-surface-300);
		font-size: 13pt;
	}
</style>