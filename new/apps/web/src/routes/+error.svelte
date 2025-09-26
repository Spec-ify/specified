<script lang="ts">
    import { page } from '$app/state';
</script>

<div>
    <h1>
        {page.status}
    </h1>

    <h2>{page.error?.message ?? "no message given"}</h2>
</div>

<style>
    div {
        width: 100vw;
        height: 100vh;
        background-color: gray;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    h1 {
        font-size: 4rem;
    }

    h2 {
        font-size: 3rem;
        text-align: center;
    }
</style>

