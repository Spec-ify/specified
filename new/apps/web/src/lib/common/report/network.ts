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
    Adapters: Array<Record<string, string | Array<string> | number | boolean | null>>;
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
    Route, NetworkConnection, UDPEndpoint,
    AutoTuningLevel,

    Network,
}