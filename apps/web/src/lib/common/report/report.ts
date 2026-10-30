import type { Meta } from "./meta";
import type { BasicInfo } from "./basicinfo";
import type { System } from "./system";
import type { Hardware } from "./hardware";
import type { Security } from "./security";
import type { Network } from "./network";
import type { Events } from "./events";

interface Report {
    Version: string;
    Meta: Meta;
    BasicInfo: BasicInfo;
    System: System;
    Hardware: Hardware;
    Security: Security;
    Network: Network;
    Events: Events;
    DebugLogText: string;
}

export type {
    Report,
}