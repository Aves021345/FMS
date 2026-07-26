<?php
/**
 * Footer Partial
 */
?>
<div class="px-8 py-4 border-t border-line text-textSoft text-[.8rem]">
    &copy; <?= date('Y') ?> Travel & Tours - Financial Management System.
    Logged in as <?= htmlspecialchars($_SESSION['username'] ?? '') ?>.
</div>