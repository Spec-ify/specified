interface RamModule {
    DeviceLocation: string;
    BankLocator: string;
    Mnufacturer: string | null;
    SerialNumber: string | null;
    PartNumber: string | null;
    ConfiguredSpeed: number;
    Capacity: number;
}

interface Cpu {
    CurrentClockSpeed: number;
    LoadPercentage: number;
    Manufacturer: string;
    Name: string;
    NumberOfEnabledCore: number;
    SocketDesignation: string;
    ThreadCount: number;
}

interface Gpu {
    AdapterRAM: number | null;
    CurrentBitsPerPixel: number | null;
    CurrentHorizontalResolution: number | null;
    CurrentRefreshRate: number | null;
    CurrentVerticalResolution: number | null;
    Description: string;
    DriverDate: string;
    DriverVersion: string;
}

interface Motherboard {
    Manufacturer: string;
    Product: string;
    SerialNumber: string;
}

interface AudioDevice {
    DeviceID: string;
    Manufacturer: string;
    Name: string;
    Status: string;
}

interface Monitor {
    Source: string | null;
    Name: string;
    ChipType: string | null;
    DedicatedMemory: string;
    MonitorModel: string;
    CurrentMode: string;
    ConnectionType: string;
}

interface EdidEntry {
    FixedHeaderPattern: string;
    ManufacturerId: string;
    ProductCode: string;
    SerialNumber: string;
    ManufacturedDate: string;
    EdidVersion: string;
    EdidRevision: string;
    VideoInputParametersBitmap: string;
    HorizontalScreenSize: string;
    VerticalScreenSize: string;
    DisplayGamma: string;
    SupportedFeaturesBitmap: string;
    ChromacityCoordinates: string;
    EstablishedTimingBitmap: string;
    TimingInformation: string;
    TimingDescriptors: string;
    NumberOfExtensions: string;
    Checksum: string;
}

interface Driver {
    DeviceID: string;
    DeviceName: string;
    DriverVersion: string;
    FriendlyName: string;
    Manufacturer: string;
}

interface Device {
    ConfigManagerErrorCode: number;
    Description: string;
    DeviceID: string;
    Name: string;
    Status: string;
}

interface BiosInfo {
    BiosCharacteristics: Array<number>;
    BIOSVersion: Array<string>;
    BuildNumber: string | null;
    Caption: string | null;
    CodeSet: string | null;
    CurrentLanguage: string;
    Description: string;
    EmbeddedControllerMajorVersion: number;
    EmbeddedControllerMinorVersion: number;
    IdentificationCode: number | null;
    InstallableLanguages: number;
    InstallDate: string | null;
    LanguageEdition: string | null;
    ListOfLanguages: Array<string>;
    Manufacturer: string;
    Name: string;
    OtherTargetOS: string | null;
    PrimaryBIOS: boolean;
    ReleaseDate: string;
    SerialNumber: string;
    SMBIOSBIOSVersion: string;
    SMBIOSMajorVersion: number;
    SMBIOSMinorVersion: number;
    SMBIOSPresent: boolean;
    SoftwareElementID: string;
    SoftwareElementState: number;
    Status: string;
    SystemBiosMajorVersion: number;
    SystemBiosMinorVersion: number;
    TargetOperatingSystem: number;
    Version: string;
}

interface Partition {
    PartitionCapacity: number;
    PartitionFree: number;
    PartitionLabel: string;
    PartitionLetter: string;
    Filesystem: string;
    CfgMgrErrorCode: number;
    LastErrorCode: number;
    DirtyBitSet: boolean;
    BitlockerEncryptionStatus: boolean;
}

interface SmartEntry {
    Id: number;
    Name: string;
    RawValue: string;
}

interface StorageDevice {
    DeviceName: string;
    SerialNumber: string;
    DiskNumber: number;
    DiskCapacity: number;
    DiskFree: number;
    BlockSize: number | null;
    MediaType: string;
    InterfaceType: string;
    PartitionScheme: string;
    Partitions: Array<Partition>;
    SmartData: Array<SmartEntry>;
}

interface TemperatureSensor {
    Hardware: string;
    SensorName: string;
    SensorValue: number;
}

interface Battery {
    Name: string;
    Manufacturer: string;
    Chemistry: string;
    Design_Capacity: string;
    Full_Charge_Capacity: string;
    Remaining_Life_Percentage: string;
}

interface Hardware {
    Ram: Array<RamModule>;
    Cpu: Cpu;
    Gpu: Array<Gpu>;
    Motherboard: Motherboard;
    AudioDevices: Array<AudioDevice>;
    Monitors: Array<Monitor>;
    EdidData: Array<EdidEntry>;
    Drivers: Array<Driver>;
    Devices: Array<Device>;
    BiosInfo: Array<BiosInfo>;
    Storage: Array<StorageDevice>;
    Temperatures: Array<TemperatureSensor> | null;
    Batteries: Array<Battery>;
    WriteSuccess: boolean;
    ErrorCount: number;
}

export type {
    RamModule, Cpu, Gpu,
    Motherboard, AudioDevice, Monitor,
    EdidEntry, Driver, Device,
    BiosInfo, Partition, SmartEntry,
    StorageDevice, TemperatureSensor, Battery,

    Hardware,
};