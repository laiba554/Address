<footer class="admin-footer mt-auto">
    <div class="container-fluid text-center">
        <p class="mb-1 fw-semibold">
            Address Jewelers – Admin Panel
        </p>
        <p class="mb-0 small">
            &copy; <?= date('Y'); ?> All Rights Reserved
        </p>
    </div>
</footer>

<style>
    .admin-footer {
        background-color: #111;
        color: #ccc;
        padding: 15px 0;
        width: 100%;
    }

    .admin-footer p {
        margin: 0;
    }

    /* Prevent footer from creating scrollbars */
    html, body {
        height: 100%;
    }

    body {
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
    }
</style>
