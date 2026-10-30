interface UnexpectedShutdown {
    Timestamp: string;
    BugcheckCode: number;
    BugcheckParameter1: string;
    BugcheckParameter2: string;
    BugcheckParameter3: string;
    BugcheckParameter4: string;
    PowerButtonTimestamp: number;
}

interface MachineCheckException {
    Timestamp: number | null;
    MciStatusRegisterValid: boolean;
    ErrorOverflow: boolean;
    UncorrectedError: boolean;
    ErrorReportingEnabled: boolean;
    ProcessorContextCorrupted: boolean;
    PoisonedData: boolean;
    ExtendedErrorCode: number;
    McaErrorCode: string;
    ErrorMessage: string;
    TransactionType: string;
    MemoryHierarchyLevel: string;
    RequestType: string;
    Participation: string;
    Timeout: string;
    MemoryOrIo: string;
    MemoryTransactionType: string;
    ChannelNumber: string;
}

interface ErrorHeader {
    Signature: string;
    Revision: string;
    SignatureEnd: string;
    SectionCount: string;
    Severity: string;
    ValidBits: string;
    Length: string;
    Timestamp: string;
    PlatformId: string;
    PartitionId: string;
    CreatorId: string;
    NotifyType: string;
    RecordId: string;
    Flags: string;
    PersistenceInfo: string;
}

interface ErrorDescriptor {
    SectionOffset: string;
    SectionLength: string;
    Revision: string;
    ValidBits: string;
    Flags: string;
    SectionType: string;
    FRUId: string;
    SectionSeverity: string;
    FRUText: string;
}

interface WheaErrorRecord {
    ErrorHeader: ErrorHeader;
    ErrorDescriptors: Array<ErrorDescriptor>;
    ErrorPackets: Array<string>;
}

interface PciCommandRegister {
    InterruptDisable: boolean;
    FastBackToBackEnable: boolean;
    SErrEnable: boolean;
    ParityErrorResponse: boolean;
    VgaPaletteSnoop: boolean;
    MemoryWriteAndInvalidateEnable: boolean;
    SpecialCycles: boolean;
    BusMaster: boolean;
    MemorySpace: boolean;
    IoSpace: boolean;
}

interface PciStatusRegister {
    DetectedParityError: boolean;
    SignaledSystemError: boolean;
    ReceivedMasterAbort: boolean;
    ReceivedTargetAbort: boolean;
    SignaledTargetAbort: boolean;
    DevselTiming: number;
    MasterDataParityError: boolean;
    FastBackToBackCapable: boolean;
    SixtySixMhzCapable: boolean;
    CapabilitiesList: boolean;
    InterruptStatus: boolean;
}

interface PciWheaError {
    Timestamp: number | null;
    VendorId: string;
    DeviceId: string;
    FaultingDevice: string;
    Command: number;
    Status: number;
    Bus: number;
    Device: number;
    Function: number;
    pciCommandRegister: PciCommandRegister;
    pciStatusRegister: PciStatusRegister;
}

interface Events {
    UnexpectedShutdowns: Array<UnexpectedShutdown>;
    MachineCheckExceptions: Array<MachineCheckException>;
    WheaErrorRecords: Array<WheaErrorRecord>;
    PciWheaErrors: Array<PciWheaError>;
    UnexpectedShutdownCount: number;
    MachineCheckExceptionCount: number;
    WheaErrorRecordCount: number;
    PciWheaErrorCount: number;
}

export type {
    UnexpectedShutdown, MachineCheckException, ErrorHeader,
    ErrorDescriptor, WheaErrorRecord, PciCommandRegister, 
    PciStatusRegister, PciWheaError,

    Events,
}