<!-- NOT YET IMPLEMENTED IN NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	interface PowerProfileInfo {
		Caption: string;
        Description: string;
        ElementName: string;
        InstanceID: string;
        IsActive: boolean;	
	}

	interface BatteryInfo {
		Name: string;
        Manufacturer: string;
        Chemistry: string;
        Design_Capacity: string;
        Full_Charge_Capacity: string;
        Remaining_Life_Percentage: string;
	}

	interface Props {
		powerprofiles: Array<PowerProfileInfo>;
		batteries: Array<BatteryInfo>;
	}

	let {
		powerprofiles,
		batteries
	}: Props = $props();

	let activeProfile: PowerProfileInfo;

	powerprofiles.forEach((profile: PowerProfileInfo)=>{
		if (profile.IsActive){
			activeProfile = profile
			return
		}
	})
</script>

<!-- Power Profiles -->
<Widget title="Power Profiles">
	{#snippet widgetContents()}
		<div class="widget-value">
			<span>{activeProfile.ElementName}</span>
			<div style="font-size: 10pt;">Current Profile</div>
		</div>
	{/snippet}

	{#snippet modalContents()}
		<h4>Power Profiles</h4>
		<table id="power-table" class="table">
			<thead>
				<tr>
					<th>Description</th>
					<th>Element</th>
					<th>Instance Path</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				{#each powerprofiles as profile}
					<tr>
						<td>{profile.Description}</td>
						<td>{profile.ElementName}</td>
						<td>{profile.InstanceID}</td>
						<td>{profile.IsActive}</td>
					</tr>
				{/each}
			</tbody>
		</table>
		<br />
		<h4>Battery</h4>
		<table id="battery-table" class="table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Manufacturer</th>
					<th>Chemistry</th>
					<th>Design Capacity</th>
					<th>Current Full Charge Capacity</th>
				</tr>
			</thead>
			<tbody>
				{#each batteries as battery}
					<tr>
						<td>{battery.Name}</td>
						<td>{battery.Manufacturer}</td>
						<td>{battery.Chemistry}</td>
						<td>{battery.Design_Capacity}</td>
						<td>{battery.Full_Charge_Capacity}</td>
					</tr>
				{/each}
			</tbody>
		</table>
	{/snippet}
</Widget>

<style>
	span {
		color: var(--color-secondary-50);
	}

	div {
		color: var(--color-surface-300);
		font-size: 13pt;
	}
</style>