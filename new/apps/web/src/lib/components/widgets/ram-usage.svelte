<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
    import Widget from './modal-widget.svelte';
</script>

<!-- RAM Usage -->
<Widget title="Memory Usage" type="" modalId="memory-usage-modal">
    <div slot="values">
        <div class="widget-value">
            <div class="widget-single-value">
                <span class="green">
                    <!-- <?= $ram_used ?>GB -->
                </span>
                <span>/</span>
                <span>
                    <!-- <?= $total_ram ?>GB -->
                </span>
            </div>
            <div>
                <!-- <?= $ram_used_percent ?>% -->
            </div>
        </div>
    </div>
</Widget>