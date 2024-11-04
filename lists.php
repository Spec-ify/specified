<?php
    /*
     * Various lists that are used in the viewers
     */

    /**
     * this is "notable software" (I was too lazy to change the name)
     *
     * It should include things that are actual PUPs (like IOBit) as well as things that may be legitimate but people
     * who are giving support should know about, like VPNs. It should NOT include things that do not directly impact
     * support like Roblox. It should also not include AVs because those are already detected elsewhere.
     */
    $notableSoftwareList = [
        "smartbyte", // messes with network
        "netlimiter", // messes with network
        "cfosspeed", // messes with network
        "Gigabyte Speed", // messes with network
        "XFast LAN", // messes with network
        "Driver Easy", // driver updater
        "CCleaner", // debloater
        "Wondershare", // "Adware with popups and shills, even after uninstalling it will still give you ads and popups constantly." - KayZ
        "Vanguard", // Kernel-level Anti-cheat, causes system instability
        "Battleye", // Kernel-level Anti-cheat, causes system instability
        "WinAero", // tweaker
        "GeekFreaksTuning", // tweaker
        "citrix", // vpn
        "tailscale", // vpn
        "hamachi", // vpn
        "vpn", // duh
        "Process Lasso", // tweaker
        "throttlestop", // overclock
        "Hone", // tweaker
        "MacType", // Can mess with text clarity
        "Wave Browser", // "It's a chromium-based web browser that pushes ad notifications, reported by many AVs to be a PUP as well." - Adam
        "LogiLDA", // adware
        "TLauncher", // "Known token stealer, potential malware." - TheRublixCube
        "Lunar Client", // Shady, privacy policy allows for stealing "search history", stole code, etc.
        "BoosterX", // tweaker
        "iTop", // vpn
        "Wallpaper Engine", // can mess up ui
        "voice changer", // messes with audio
        "Voicemod", // messes with audio
        "System Mechanic", // debloater
        "MyCleanPC", // debloater
        "DriverFix", // driver updater
        "Reimage Repair", // debloater
        "Browser Assistant", // malware
        "KMS", // piracy tools that can have malware
        "HWID", // cheat tools that can have malware
        "Advanced SystemCare", // debloater
        "salad", // crypto miner
        "cleaner", // blanket for debloater
        "Speedify", // messes with network
        "UltraUXThemePatcher", // can mess up ui
        "Lockdown Browser" // has messed up windows power plans and other things https://discord.com/channels/749314018837135390/749316166878625843/1298020309881847849
        "KMSpico" // Known windows activation tool likely malware
    ];

    $biosCharacteristics = [
        3 => "BIOS Characteristics are not supported.",
        4 => "ISA is supported.",
        5 => "MCA is supported.",
        6 => "EISA is supported.",
        7 => "PCI is supported.",
        8 => "PC card (PCMCIA) is supported.",
        9 => "Plug and Play is supported.",
        10 => "APM is supported.",
        11 => "BIOS is upgradeable (Flash).",
        12 => "BIOS shadowing is allowed.",
        13 => "VL-VESA is supported.",
        14 => "ESCD support is available.",
        15 => "Boot from CD is supported.",
        16 => "Selectable boot is supported.",
        17 => "BIOS ROM is socketed.",
        18 => "Boot from PC card (PCMCIA) is supported.",
        19 => "EDD specification is supported.",
        20 => "Japanese floppy for NEC 9800 1.2 MB (3.5”, 1K bytes/sector, 360 RPM) is supported.",
        21 => "Japanese floppy for Toshiba 1.2 MB (3.5”, 360 RPM) is supported.",
        22 => "5.25” / 360 KB floppy services are supported.",
        23 => "5.25” /1.2 MB floppy services are supported.",
        24 => "3.5” / 720 KB floppy services are supported.",
        25 => "3.5” / 2.88 MB floppy services are supported.",
        26 => "Print screen Service is supported.",
        27 => "8042 keyboard services are supported.",
        28 => "Serial services are supported.",
        29 => "Printer services are supported.",
        30 => "CGA/Mono Video Services are supported.",
        31 => "NEC PC-98."
    ];

    $defaultRegKeys = [
        "TdrLevel" => [3],
        "NonBestEffortLimit" => [20],
        "NetworkThrottlingIndex" => [10],
        "EnableSuperfetch" => [null],
        "DisableAntiVirus" => [0],
        "DisableAntiSpyware" => [0],
        "PUAProtection" => [0],
        "PassiveMode" => [0],
        "DontReportInfectionInformation" => [1],
        "Disabled" => [0],
        "AllowUpgradesWithUnsupportedTPMOrCPU" => [null],
        "HwSchMode" => [1],
        "UseWUServer" => [1],
        "NoAutoUpdate" => [0],
        "HiberbootEnabled" => [1],    // side note, probably should be disabled cause bugs but eh its default
        "AuditBoot" => [1],
        "AllowBuildPreview" => [2],
        "BypassCPUCheck" => [null],
        "BypassStorageCheck" => [null],
        "BypassRAMCheck" => [null],
        "BypassTPMCheck" => [null],
        "BypassSecureBootCheck" => [null],
        "SV2" => [null],
        "Win32PrioritySeparation" => [2],
        "Windows Error Reporting\\Disabled" => [null],
        "Windows Defender\\Passive Mode" => [null],
        "UnsupportedHardwareNotificationCache\\SV2" => [null],
        "HypervisorEnforcedCodeIntegrity" => [null],
    ];
