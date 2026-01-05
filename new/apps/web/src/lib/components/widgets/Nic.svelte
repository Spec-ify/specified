<!-- NOT YET IMPLEMENTED IN NEW WIDGET SYSTEM -->
<script lang="ts">
	import Widget from '../../common/ModalWidget.svelte';

	interface NicInfo {
        DefaultIPGateway: Array<string>;
        Description: string;
        DHCPEnabled: boolean;
        DHCPLeaseExpires: string;
        DHCPLeaseObtained: string;
        DHCPServer: string;
        DNSDomain: null,
        DNSDomainSuffixSearchOrder: Array<string>;
        DNSHostName: string;
        DNSServerSearchOrder: Array<string>;
        InterfaceIndex: number;
        IPAddress: Array<string>;
        IPEnabled: boolean;
        IPSubnet: Array<string>;
        MACAddress: string;
        LinkSpeed: number;
        PhysicalAdapter: boolean;
        FullDuplex: boolean;
        MediaConnectState: number;
        MediaDuplexState: number;
        MtuSize: number;
        Name: string;
        OperationalStatusDownMediaDisconnected: boolean;
        PermanentAddress: string;
        PromiscuousMode: boolean;
        State: number;
        DNSIPV6: string;
        DNSIsStatic: boolean;
	}

	interface Props {
		nics: Array<NicInfo>;
	}

	let {
		nics
	}: Props = $props();

	function findPrimaryAdapter(nics: Array<NicInfo>): string {
		let physicalAdapters: Array<string> = [];

		nics.forEach((adapter: NicInfo, _: any) => {
			// if adapter is *physical*, AND has a determined IP address
			if (adapter.PhysicalAdapter && adapter.IPAddress) {
				if (adapter.IPAddress.length > 0) {
					return adapter.Description;
				};
			}

			// identify and push physical adapters here, so we dont have to .forEach() the list later
			if (adapter.PhysicalAdapter) physicalAdapters.push(adapter.Description);
		});

		// if there are none with an assigned IP address, but there are physical adapters,
		// return the first in the array
		if (physicalAdapters.length > 0){
			return physicalAdapters[0];
		}

		return 'Disconnected';
	}
</script>

<!-- NICs -->

<Widget title="NIC">
	{#snippet widgetContents()}
		<div class="widget-value">
			<span>{findPrimaryAdapter(nics)}</span>
		</div>
	{/snippet}

	{#snippet modalContents()}
		{#each nics as adapter}
			<table class="table nic">
				<tbody>
					<tr>
						<td>#</td>
						<td>{adapter.InterfaceIndex}</td>
					</tr>
					<tr>
						<td>Name</td>
						<td>{adapter.Description}</td>
					</tr>
					<tr>
						<td>MAC</td>
						<td>{adapter.MACAddress}</td>
					</tr>
					<tr>
						<td>Gateway(s)</td>
						<td>
							{#each adapter.DefaultIPGateway as gateway}
								{gateway} <br />
							{/each}
						</td>
					</tr>
					<tr>
						<td>DHCP State</td>
						<td>{adapter.DHCPEnabled}</td>
					</tr>
					<tr>
						<td>DHCP Lease Expiry</td>
						<td>{adapter.DHCPLeaseExpires}</td>
					</tr>
					<tr>
						<td>DHCP Lease Obtained</td>
						<td>{adapter.DHCPLeaseObtained}</td>
					</tr>
					<tr>
						<td>DHCP Server</td>
						<td>{adapter.DHCPServer}</td>
					</tr>
					<tr>
						<td>DNS Domain</td>
						<td>{adapter.DNSDomain}</td>
					</tr>
					<tr>
						<td>DNS Hostname</td>
						<td>{adapter.DNSHostName}</td>
					</tr>
					<tr>
						<td>DNS IPs</td>
						<td>
							{#each adapter.DNSServerSearchOrder as dnsIP}
								{dnsIP} <br />
							{/each}
						</td>
					</tr>
					<tr>
						<td>DNS Suffixes</td>
						<td>
							{#each adapter.DNSDomainSuffixSearchOrder as dnsSuffix}
								{dnsSuffix} <br />
							{/each}
						</td>
					</tr>
					<tr>
						<td>IP Enabled?</td>
						<td>{adapter.IPEnabled}</td>
					</tr>
					<tr>
						<td>IP(s)</td>
						<td>
							{#each adapter.IPAddress as ip}
								{ip} <br />
							{/each}
						</td>
					</tr>
					<tr>
						<td>Subnet</td>
						<td>
							{#each adapter.IPSubnet as subnet}
								{subnet} <br />
							{/each}
						</td>
					</tr>
					<tr>
						<td>Physical Adapter?</td>
						<td>{adapter.PhysicalAdapter ?? 'Unknown'}</td>
					</tr>
					{#if adapter.LinkSpeed}
						<tr>
							<td>Link Speed</td>
							<td>{Math.round(adapter.LinkSpeed / 1000000)} Mbps</td>
						</tr>
					{/if}
					{#if adapter.DNSIPV6 && !(adapter.DNSIPV6 == '')}
						<tr>
							<td>IPv6 DNS?</td>
							<td>
								{#each adapter.DNSIPV6.split(',') as DNSIPV6}
									{DNSIPV6}<br />
								{/each}
							</td>
						</tr>
					{/if}
					{#if adapter.DNSIsStatic}
						<tr>
							<td>Is DNS Static?</td>
							<td>{adapter.DNSIsStatic}</td>
						</tr>
					{/if}
					{#if adapter.PhysicalAdapter}
						<tr>
							<td>Full Duplex?</td>
							<td>{adapter.FullDuplex}</td>
						</tr>
						<tr>
							<td>Media Connection State</td>
							<td>{adapter.MediaConnectState}</td>
						</tr>
						<tr>
							<td>Media Duplex State</td>
							<td>{adapter.MediaDuplexState}</td>
						</tr>
						<tr>
							<td>MTU Size</td>
							<td>{adapter.MtuSize}</td>
						</tr>
						<tr>
							<td>Name</td>
							<td>{adapter.Name}</td>
						</tr>
						<tr>
							<td>Operational Status</td>
							<td>{adapter.OperationalStatusDownMediaDisconnected}</td>
						</tr>
						<tr>
							<td>Permanent Address</td>
							<td>{adapter.PermanentAddress}</td>
						</tr>
						<tr>
							<td>Promiscuous Mode</td>
							<td>{adapter.PromiscuousMode}</td>
						</tr>
						<tr>
							<td>State</td>
							<td>{adapter.State}</td>
						</tr>
					{/if}
				</tbody>
			</table>
		{/each}
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