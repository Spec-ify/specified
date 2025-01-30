<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
    import Widget from './modal-widget.svelte';
</script>

<!-- Temps -->
<Widget title="Temps" modalId="temps-modal">
    <div slot="values">
        <div class="widget-value">
            <div>🔥</div>
            <div>C</div>
        </div>
    </div>

    <div class="modal-body">
        <table id="temp-table" class="table">
            <thead>
                <tr>
                    <th>Hardware</th>
                    <th>Sensor</th>
                    <th>Temperature</th>
                </tr>
            </thead>
        </table>
    </div>
</Widget>