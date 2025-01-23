<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
</script>

<!-- Temps -->
<div class="widget widget-temps hover" type="button" data-mdb-toggle="modal" data-mdb-target="#temps-modal">
    <h1>Temps</h1>
    <div class="widget-values">
        <div class="widget-value">
            <div>🔥</div>
            <div>C</div>
        </div>
    </div>
</div>
<div class="modal fade " id="temps-modal" tabindex="-1" aria-labelledby="temps-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label">Temps</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>