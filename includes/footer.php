<footer class="admin-footer mt-auto py-3">
    <div class="container-fluid text-center">
        <p class="mb-1 fw-semibold">
            Address Jewelers – Admin Panel
        </p>
        <p class="mb-0 small text-muted">
            &copy; <?= date('Y'); ?> All Rights Reserved
        </p>
    </div>
</footer>

<style>
/* Footer Base */
.admin-footer {
    background: linear-gradient(135deg, #0b6b4f, #158f6b);
    color: #f0f0f0;
    padding: 15px 0;
    width: 100%;
    box-shadow: 0 -4px 15px rgba(0,0,0,0.2);
    border-top: 1px solid rgba(255,255,255,0.1);
    font-family: 'Poppins', sans-serif;
}

/* Footer Text */
.admin-footer p {
    margin: 0;
}

/* Footer Link Hover (Optional if you add links) */
.admin-footer a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.admin-footer a:hover {
    color: #ffd700;
}

/* Prevent horizontal scroll */
html, body {
    height: 100%;
}

body {
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
}

/* Push footer to bottom if content is short */
body > .container-fluid,
body > .page-wrapper {
    flex: 1 0 auto;
}
</style>
