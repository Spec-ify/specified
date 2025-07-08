<script lang="ts">
    import Widget from '../../common/modal-widget.svelte';

    export let data;

    const adapterData = data.Network.Adapters;
    
    function findNic(){
        let nicText = "";
        adapterData.forEach((adapter: any, _: any) => {
            if (adapter.PhysicalAdapter && adapter.IPAddress){
                if (Object.values(adapter.IPAddress).length > 0) 
                    nicText = adapter.Description;
                }
        });

        adapterData.forEach((_: string, adapter: Record<string, any>) => {
            if (adapter.PhysicalAdapter) 
                nicText = adapter.Description;
        });

        if (nicText == ""){
            return "Disconnected";
        } else {
            return nicText;
        }
    };
</script>

<!-- NIC -->

<Widget title="NIC" modalId="nic-modal">
    <div slot="values">
        <div class="widget-value">
            <div class="green">
                {findNic()}
            </div>
        </div>
    </div>

    <div slot="modal-body">
        {#each adapterData as adapter}
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
                                {gateway} <br/>
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
                                {dnsIP} <br/>
                            {/each}
                        </td>
                    </tr>
                    <tr>
                        <td>DNS Suffixes</td>
                        <td>
                            {#each adapter.DNSDomainSuffixSearchOrder as dnsSuffix}
                                {dnsSuffix} <br/>
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
                                {ip} <br/>
                            {/each}
                        </td>
                    </tr>
                    <tr>
                        <td>Subnet</td>
                        <td>
                            {#each adapter.IPSubnet as subnet}
                                {subnet} <br/>
                            {/each}
                        </td>
                    </tr>
                    <tr>
                        <td>Physical Adapter?</td>
                        <td>{adapter.PhysicalAdapter ?? 'unknown'}</td>
                    </tr>
                    {#if (adapter.LinkSpeed)}
                        <tr>
                            <td>Link Speed</td>
                            <td>{Math.round(adapter.LinkSpeed / 1000000)} Mbps</td>
                        </tr>
                    {/if}
                    {#if (adapter.DNSIPV6 && !(adapter.DNSIPV6 == ""))}
                        <tr>
                            <td>IPv6 DNS?</td>
                            <td>
                                {#each adapter.DNSIPV6.split(",") as DNSIPV6}
                                    {DNSIPV6}<br/> 
                                {/each}
                            </td>
                        </tr>
                    {/if}
                    {#if (adapter.DNSIsStatic)}
                        <tr>
                            <td>Is DNS Static?</td>
                            <td>{adapter.DNSIsStatic}</td>
                        </tr>
                    {/if}
                    {#if (adapter.DNSIsStatic)}
                        <tr>
                            <td>Is DNS Static?</td>
                            <td>{adapter.DNSIsStatic}</td>
                        </tr>
                    {/if}
                    {#if (adapter.PhysicalAdapter)}
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
    </div>
</Widget>