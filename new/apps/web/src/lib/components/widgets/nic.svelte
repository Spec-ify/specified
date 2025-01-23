<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
</script>

<!-- NIC -->
<div class="widget widget-board hover" type="button" data-mdb-toggle="modal" data-mdb-target="#nic-modal">
    <h1>NIC</h1>
    <div class="widget-values">
        <div class="widget-value">
            <div class="green">

                <!-- <?php
                $adapterText = "Disconnected";

                foreach ($json_data['Network']['Adapters'] as $adapter) {
                    if ((bool) $adapter['PhysicalAdapter'] && is_array($adapter['IPAddress']) && count($adapter['IPAddress']) > 0) {
                        $adapterText = $adapter['Description'];
                        break;
                    }
                };

                if ($adapter == "") {
                    foreach ($json_data['Network']['Adapters'] as $adapter) {
                        if ((bool) $adapter['PhysicalAdapter']) {
                            $adapterText = $adapter['Description'];
                            break;
                        }
                    };
                }
                echo $adapterText;

                ?> -->

            </div>
        </div>
    </div>
</div>
<div class="modal fade " id="nic-modal" tabindex="-1" aria-labelledby="nic-modal" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label">NIC Information</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- <?php

                foreach ($json_data["Network"]["Adapters"] as $nic) {
                    $table = '';
                    $gateways = '';
                    $dnsIPs = '';
                    $dnsSuffixes = '';
                    $ips = '';
                    $subnets = '';

                    if (is_array($nic["DefaultIPGateway"])) {
                        foreach ($nic["DefaultIPGateway"] as $gateway) {
                            $gateways .= $gateway . ' ';
                        }
                    }

                    if (is_array($nic["DNSServerSearchOrder"])) {
                        foreach ($nic["DNSServerSearchOrder"] as $dnsIP) {
                            $dnsIPs .= $dnsIP . ' ';
                        }
                    }

                    if (is_array($nic["DNSDomainSuffixSearchOrder"])) {
                        foreach ($nic["DNSDomainSuffixSearchOrder"] as $dnsSuffix) {
                            $dnsSuffixes .= $dnsSuffix . ' ';
                        }
                    }

                    if (is_array($nic["IPAddress"])) {
                        foreach ($nic["IPAddress"] as $ip) {
                            $ips .= $ip . ' ';
                        }
                    }

                    if (is_array($nic["IPSubnet"])) {
                        foreach ($nic["IPSubnet"] as $subnet) {
                            $subnets .= $subnet . ' ';
                        }
                    }

                    $table = '<table class="table nic">
                                                    <tr>
                                                        <td>#</td>
                                                        <td>' . $nic["InterfaceIndex"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Name</td>
                                                        <td>' . $nic["Description"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>MAC</td>
                                                        <td>' . $nic["MACAddress"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Gateway(s)</td>
                                                        <td>' . $gateways . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>DHCP State</td>
                                                        <td>' . $nic["DHCPEnabled"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>DHCP Lease Expiry</td>
                                                        <td>' . $nic["DHCPLeaseExpires"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>DHCP Lease Obtained</td>
                                                        <td>' . $nic["DHCPLeaseObtained"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>DHCP Server</td>
                                                        <td>' . $nic["DHCPServer"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>DNS Domain</td>
                                                        <td>' . $nic["DNSDomain"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>DNS Hostname</td>
                                                        <td>' . $nic["DNSHostName"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>DNS IPs</td>
                                                        <td>' . $dnsIPs . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>DNS Suffixes</td>
                                                        <td>' . $dnsSuffixes . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>IP Enabled?</td>
                                                        <td>' . $nic["IPEnabled"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>IP(s)</td>
                                                        <td>' . $ips . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Subnet</td>
                                                        <td>' . $subnets . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Physical Adapter?</td>
                                                        <td>' . ($nic['PhysicalAdapter'] ?? 'unknown') . '</td>
                                                    </tr>
                                                ';

                    if (isset($nic["LinkSpeed"])) {
                        $table .= '
                                                    <tr>
                                                        <td>Link Speed</td>
                                                        <td>' . round($nic["LinkSpeed"] / 1_000_000) . 'Mbps </td>
                                                    </tr>
                                            ';
                    }

                    if (isset($nic["DNSIPV6"])) {
                        // Stolen from doom-scroll.php.
                        $ipv6_dns = isset($nic['DNSIPV6']) ? (is_array($nic['DNSIPV6']) ? $nic['DNSIPV6'] : explode(',', $nic['DNSIPV6'])) : [];
                        $table .= '
                                                    <tr>
                                                        <td>IPv6 DNS?</td>
                                                        <td>' . $ipv6_dns . '</td>
                                                    </tr>
                                            ';
                    }

                    if (isset($nic["DNSIsStatic"])) {
                        $table .= '
                                                    <tr>
                                                        <td>Is DNS Static?</td>
                                                        <td>' . $nic["DNSIsStatic"] . '</td>
                                                    </tr>
                                            ';
                    }

                    if (isset($nic["PhysicalAdapter"]) && (bool) $nic["PhysicalAdapter"]) {
                        $table .= '
                                                    <tr>
                                                        <td>Full Duplex?</td>
                                                        <td>' . $nic["FullDuplex"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Media Connection State</td>
                                                        <td>' . $nic["MediaConnectState"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Media Duplex State</td>
                                                        <td>' . $nic["MediaDuplexState"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>MTU Size</td>
                                                        <td>' . $nic["MtuSize"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Name</td>
                                                        <td>' . $nic["Name"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Operational Status</td>
                                                        <td>' . $nic["OperationalStatusDownMediaDisconnected"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Permanent Address</td>
                                                        <td>' . $nic["PermanentAddress"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>Promiscuous Mode</td>
                                                        <td>' . $nic["PromiscuousMode"] . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>State</td>
                                                        <td>' . $nic["State"] . '</td>
                                                    </tr>
                                            ';
                    }

                    $table .= '</table>';
                    echo $table;
                }

                ?> -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>