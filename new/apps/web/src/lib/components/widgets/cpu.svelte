<script lang="ts">
    import { jsonData } from '../../common/access-file.js';
</script>

<!-- CPU -->
<div class="widget widget-cpu hover" type="button" data-mdb-toggle="modal" data-mdb-target="#cpu-modal">
    <h1>CPU</h1>
    <div class="widget-values">
        <div class="widget-value">
            <div class="green">
                <!-- <?= $json_data['Hardware']['Cpu']['Name'] ?> -->
            </div>
            <div>Callsign</div>
        </div>
    </div>
</div>
<div class="modal fade " id="cpu-modal" tabindex="-1" aria-labelledby="cpu-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label">CPU info</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <!-- <td><?= $json_data['Hardware']['Cpu']['Name'] ?></td> -->
                        </tr>
                        <tr>
                            <td>Manufacturer</td>
                            <!-- <td><?= $json_data['Hardware']['Cpu']['Manufacturer'] ?></td> -->
                        </tr>
                        <tr>
                            <td>Socket Designation</td>
                            <!-- <td><?= $json_data['Hardware']['Cpu']['SocketDesignation'] ?></td> -->
                        </tr>
                        <tr>
                            <td>Current Clock Speed</td>
                            <!-- <td><?= $json_data['Hardware']['Cpu']['CurrentClockSpeed'] ?></td> -->
                        </tr>
                        <tr>
                            <td># of Enabled Cores</td>
                            <!-- <td><?= $json_data['Hardware']['Cpu']['NumberOfEnabledCore'] ?></td> -->
                        </tr>
                        <tr>
                            <td>Thread Count</td>
                            <!-- <td><?= $json_data['Hardware']['Cpu']['ThreadCount'] ?></td> -->
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-body" id="cpu-info-table" style="display:none;">
                <!-- This content is populated javascript side -->
                <h6 class="modal-title" id="cpu-info-title">Database results for: ...</h6>
                <table class="table">
                    <tbody id="fetched-cpu-info">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cpu-more-info-button">More Info</button>
                <button type="button" class="btn btn-secondary" id="cpu-close-button" data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>