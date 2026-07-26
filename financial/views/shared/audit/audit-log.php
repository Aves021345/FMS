<?php
require_once __DIR__ . '/../../../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../../helpers/AuditLogger.php';

requireRole(['ROLE_ADMIN', 'ROLE_AUDITOR']);

$pageTitle = 'Audit Log';

$tableFilter = $_GET['table_name'] ?? null;
$logs = getAuditLog($pdo, $tableFilter, 300);

// Distinct table names for the filter dropdown
$tables = $pdo->query("SELECT DISTINCT table_name FROM auditlog ORDER BY table_name")->fetchAll(PDO::FETCH_COLUMN);

$actionBadge = [
    'CREATE' => 'bg-emerald-100 text-emerald-600',
    'UPDATE' => 'bg-blue-100 text-blue-600',
    'DELETE' => 'bg-rose-100 text-rose-600',
];

ob_start();
?>

<div class="p-6 space-y-6">

    <div class="bg-card rounded-2xl border border-line shadow-brandMd p-5">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-navy900 flex items-center justify-center text-orange text-xl shrink-0">
                    <i class='bx bx-history'></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-textDark">Audit Log</h1>
                    <p class="text-xs text-textSoft mt-0.5">Showing the most recent <?= count($logs) ?> entries<?= $tableFilter ? ' for ' . htmlspecialchars($tableFilter) : '' ?></p>
                </div>
            </div>

            <form method="get" class="flex items-center gap-2">
                <label for="table_name" class="text-xs font-semibold text-textMid">Filter by table</label>
                <select name="table_name" id="table_name" onchange="this.form.submit()"
                        class="px-3 py-2 rounded-lg border border-line text-sm text-textMid bg-white focus:outline-none focus:ring-2 focus:ring-navy900/20">
                    <option value="">All</option>
                    <?php foreach ($tables as $t): ?>
                        <option value="<?= htmlspecialchars($t) ?>" <?= $tableFilter === $t ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-line shadow-brandSm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-0">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-textSoft border-b border-line">
                        <th class="px-5 py-3 font-semibold whitespace-nowrap">Date/Time</th>
                        <th class="px-2 py-3 font-semibold">User</th>
                        <th class="px-2 py-3 font-semibold">Action</th>
                        <th class="px-2 py-3 font-semibold">Table</th>
                        <th class="px-2 py-3 font-semibold">Record ID</th>
                        <th class="px-2 py-3 font-semibold">Old Value</th>
                        <th class="px-2 py-3 font-semibold">New Value</th>
                        <th class="px-5 py-3 font-semibold">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="8" class="px-5 py-8 text-center text-textSoft">No audit log entries found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="border-b border-line hover:bg-[#f8fafc] align-top">
                            <td class="px-5 py-3 text-textMid whitespace-nowrap"><?= htmlspecialchars($log['created_at']) ?></td>
                            <td class="px-2 py-3 text-textDark font-medium whitespace-nowrap"><?= htmlspecialchars($log['full_name'] ?? 'System') ?></td>
                            <td class="px-2 py-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold <?= $actionBadge[$log['action']] ?? 'bg-[#eef1f6] text-textMid' ?>">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td class="px-2 py-3 text-textMid whitespace-nowrap"><?= htmlspecialchars($log['table_name']) ?></td>
                            <td class="px-2 py-3 text-textMid whitespace-nowrap"><?= htmlspecialchars($log['record_id'] ?? '') ?></td>
                            <td class="px-2 py-3 max-w-[220px]">
                                <pre class="m-0 whitespace-pre-wrap break-words font-sans text-xs text-textMid bg-rose-50/60 rounded-lg px-2 py-1.5"><?= htmlspecialchars($log['old_value'] ?? '') ?></pre>
                            </td>
                            <td class="px-2 py-3 max-w-[220px]">
                                <pre class="m-0 whitespace-pre-wrap break-words font-sans text-xs text-textMid bg-emerald-50/60 rounded-lg px-2 py-1.5"><?= htmlspecialchars($log['new_value'] ?? '') ?></pre>
                            </td>
                            <td class="px-5 py-3 text-textSoft whitespace-nowrap font-mono text-xs"><?= htmlspecialchars($log['ip_address'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';