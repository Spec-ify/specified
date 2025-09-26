<script lang="ts">
	export let data;

	const ramSticks = data;
	let ramStickCount = 0;
	let flexBasis = ``;
	let htmlResult = '';

	ramSticks.forEach((ramStick: any) => {
		ramStickCount++;
		flexBasis = `${100 / Object.keys(ramSticks).length / 2}%`;
		if (!(ramStick.Capacity <= 0)) {
			let stickSize = Math.floor(ramStick.Capacity / 1000);
			htmlResult += `<div class="widget-value" style="flex: 1 1 ${flexBasis};">
                                <div class="green">${stickSize}GB</div>
                                <div>DIMM ${ramStickCount}</div>
                            </div>`;
		} else {
			htmlResult += `<div class="widget-value" style="flex: 1 1 ${flexBasis};">
                                <div style="color: rgb(215,27,27);">--</div>
                                <div>DIMM ${ramStickCount}</div>
                            </div>`;
		}

		if (Object.keys(ramSticks).length > 4 && ramStickCount % 4 == 0) {
			htmlResult += `<div style="flex-basis: 100%;"></div>`;
		}
	});

	if (!htmlResult) {
		htmlResult += `<div class="widget-value">
                            <div class="red"> Error! </div>
                            <div>Error retrieving memory information.</div>
                        </div>';`;
	}
</script>

<div id="ram-modal-output" class="widget-values">
	{@html htmlResult}
</div>
