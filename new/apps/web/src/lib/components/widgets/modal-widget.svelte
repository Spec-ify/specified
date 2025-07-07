<script lang="ts">
    export let title = "Modal";
    export let modalId = "widget-modal";
    export let modalSpecial = "";
    export let type = "button";

    function infoClick(id: String){
        let element = document.getElementById(id + "-info-table");
        if (element){
            element.style.display = "block";
        }

        let button = document.getElementById(id + "-more-info-button")
        if (button){
            button.style.display = "none";
        }
    };
</script>
  
<div class={"widget hover widget-"+ modalId} type={type} data-mdb-toggle="modal" data-mdb-target={"#" + modalId}>
    <h1>{title}</h1>
    <div class="widget-values">
        <div class="widget-value">
        <slot name="values"></slot>
        </div>
    </div>
</div>

<div class="modal fade" id={modalId} tabindex="-1" aria-labelledby={modalId} aria-hidden="true">
    <div class={"modal-dialog " + modalSpecial}>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id=modal-label>{title}</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <slot name="modal-body"></slot>
            </div>
            <div class="modal-body">
                <slot name="extras"></slot>
            </div>
            <div class="modal-footer">
                {#if $$slots.extras}
                    <button type="button" class="btn btn-secondary" id={modalId + "-more-info-button"} on:click={() => infoClick(modalId)}>More Info</button>
                {/if}
                <button type="button" class="btn btn-secondary" id={modalId + "-close-button"} data-mdb-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>