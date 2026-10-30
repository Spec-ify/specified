interface EolListEntry {
    cycle: string;
    releaseLabel: string;
    releaseDate: string;
    eol: string;
    latest: string;
    link: string;
    lts: boolean;
    support: string;
    extendedSupport: boolean;
}

export type {
    EolListEntry
}