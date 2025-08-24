const biosCharacteristicsList: Record<number, string> = {
	3: 'BIOS Characteristics are not supported.',
	4: 'ISA is supported.',
	5: 'MCA is supported.',
	6: 'EISA is supported.',
	7: 'PCI is supported.',
	8: 'PC card (PCMCIA) is supported.',
	9: 'Plug and Play is supported.',
	10: 'APM is supported.',
	11: 'BIOS is upgradeable (Flash).',
	12: 'BIOS shadowing is allowed.',
	13: 'VL-VESA is supported.',
	14: 'ESCD support is available.',
	15: 'Boot from CD is supported.',
	16: 'Selectable boot is supported.',
	17: 'BIOS ROM is socketed.',
	18: 'Boot from PC card (PCMCIA) is supported.',
	19: 'EDD specification is supported.',
	20: 'Japanese floppy for NEC 9800 1.2 MB (3.5”, 1K bytes/sector, 360 RPM) is supported.',
	21: 'Japanese floppy for Toshiba 1.2 MB (3.5”, 360 RPM) is supported.',
	22: '5.25” / 360 KB floppy services are supported.',
	23: '5.25” /1.2 MB floppy services are supported.',
	24: '3.5” / 720 KB floppy services are supported.',
	25: '3.5” / 2.88 MB floppy services are supported.',
	26: 'Print screen Service is supported.',
	27: '8042 keyboard services are supported.',
	28: 'Serial services are supported.',
	29: 'Printer services are supported.',
	30: 'CGA/Mono Video Services are supported.',
	31: 'NEC PC-98.'
};

export const lists = {
	biosCharacteristicsList: biosCharacteristicsList
};
