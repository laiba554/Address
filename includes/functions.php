<?php
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
?>
