interface RunningProcess {
    ProcessName: string;
    Count: number;
    ExePath: string;
    Id: number;
    WorkingSet: number;
    CpuPercent: number;
}

interface Service {
    Caption: string;
    Name: string;
    PathName: string;
    StartMode: string;
    State: string;
}

interface InstalledApp {
    Name: string;
    Version: string;
    InstallDate: string | null;
}

interface Hotfix {
    Description: string;
    HotFixID: string;
    InstalledOn: string;
}

interface ScheduledTask {
    Path: string;
    Name: string;
    State: string;
    IsActive: boolean;
    Author: string | null;
    TriggerTypes: Array<string>;
}

interface StartupTask {
    AppName: string;
    AppDescription: string;
    ImagePath: string;
    Timestamp: string;
}

interface PowerProfile {
    Caption: null;
    Description: string;
    ElementName: string;
    InstanceID: string;
    IsActive: boolean;
}

interface WindowsStorePackage {
    Architecture: string;
    Language: string;
    Name: string;
    ProgramId: string;
    Vendor: string;
    Version: string;
}

interface ChoiceRegistryValue {
    HKey: string;
    Path: string;
    Name: string;
    Value: number | null;
}

interface BrowserExtension {
    name: string;
    version: string;
    description: string;
}

interface BrowserProfile {
    Name: string;
    Extensions: Array<BrowserExtension>;
}

interface Browser {
    Name: string;
    Profiles: Array<BrowserProfile>;
}

interface PageFile {
    AllocatedBaseSize: number;
    Caption: string;
    CurrentUsage: number;
    PeakUsage: number;
}

interface System {
    UserVariables: Record<string, string>;
    SystemVariables: Record<string, string>;
    RunningProcesses: Array<RunningProcess>;
    Services: Array<Service>;
    InstalledApps: Array<InstalledApp>;
    InstalledHotfixes: Array<Hotfix>;
    ScheduledTasks: Array<ScheduledTask>;
    WinScheduledTasks: Array<ScheduledTask>;
    StartupTasks: Array<StartupTask>;
    PowerProfiles: Array<PowerProfile>;
    WindowsStorePackages: Array<WindowsStorePackage>;
    MicroCodes: Array<string>;
    RecentMinidumps: number;
    DumpZip: string | null;
    StaticCoreCount: boolean;
    LimitedMemory: boolean;
    ChoiceRegistryValues: Array<ChoiceRegistryValue>;
    UsernameSpecialCharacters: boolean;
    OneDriveCommercialPathLength: number | null;
    OneDriveCommercialNameLength: number | null;
    BrowserExtensions: Array<Browser>;
    DefaultBrowser: string;
    PageFile: PageFile;
    WriteSuccess: boolean;
    ErrorCount: number;
    LastBiosTime: number;
    WindowsOld: boolean;
    InstalledLanguagePacks: Array<string>;
    SystemLanguage: string;
}

export type {
    RunningProcess, Service, InstalledApp, 
    Hotfix, ScheduledTask, StartupTask, 
    PowerProfile, WindowsStorePackage, ChoiceRegistryValue, 
    BrowserExtension, BrowserProfile, Browser, PageFile,

    System, 
};