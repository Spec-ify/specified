<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
</script>

<!-- RAM Usage -->
<div class="widget widget-memory hover">
    <h1>Memory
        <span>(Used)</span>
    </h1>
    <div class="widget-values">
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
</div>