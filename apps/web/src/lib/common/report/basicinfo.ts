interface BasicInfo {
    Edition: string;
    Version: string;
    Sku: string;
    FriendlyVersion: string;
    InstallDate: string;
    Uptime: number;
    Hostname: string;
    Username: string;
    Domain: string;
    BootMode: string;
    BootState: string;
    SMBiosRamInformation: boolean;
    WriteSuccess: boolean;
    ErrorCount: number;
}

export type {
    BasicInfo,
}