interface Adapter {
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

interface Route {
    Description: string;
    Destination: string;
    InterfaceIndex: number;
    Mask: string;
    Metric1: string;
    NextHop: string;
}

interface NetworkConnection {
    LocalIPAddress: string;
    LocalPort: number;
    RemoteIPAddress: string;
    RemotePort: number;
    OwningPID: number;
}

interface UDPEndpoint {
    LocalAddress: string;
    LocalPort: number;
    OwningProcess: number;
}

interface AutoTuningLevel {
    Automatic: string;
    InternetCustom: string;
    DatacenterCustom: string;
    Compat: string;
    Datacenter: string;
    Internet: string;
}

interface Network {
    Adapters: Adapter;
    Routes: Array<Route>;
    NetworkConnections: Array<NetworkConnection>;
    UDPEndpoints: Array<UDPEndpoint>;
    ReceiveSideScaling: boolean;
    AutoTuningLevelLocal: AutoTuningLevel;
    HostsFile: string;
    HostsFileHash: string;
    WriteSuccess: boolean;
    ErrorCount: boolean;
}

export type {
    Adapter, Route, NetworkConnection, 
    UDPEndpoint, AutoTuningLevel,

    Network,
}