interface TpmDevice {
    IsActivated_InitialValue: boolean;
    IsEnabled_InitialValue: boolean;
    IsOwned_InitialValue: boolean;
    ManufacturerId: number;
    ManufacturerIdTxt: string;
    ManufacturerVersion: string;
    ManufacturerVersionFull20: string;
    ManufacturerVersionInfo: string;
    PhysicalPresenceVersionInfo: string;
    SpecVersion: string;
    IsPresent: boolean;
}

interface Security {
    AvList: Array<string>;
    ExclusionPath: Array<string>;
    ExclusionExtension: Array<string>;
    ExclusionProcess: Array<string>;
    ExclusionIpAddresses: Array<string>;
    FwList: Array<string>;
    UacEnabled: boolean;
    SecureBootEnabled: boolean;
    UacLevel: number;
    Tpm: TpmDevice | null;
    WriteSuccess: boolean;
    ErrorCount: number;
}

export type {
    TpmDevice, 
    
    Security,
}