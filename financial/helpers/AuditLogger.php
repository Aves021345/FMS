<?php
/**
 * Audit Logger
 * Reusable helper to record changes into the auditlog table.
 * Call this from any controller action that creates, updates, approves,
 * or voids a financial record.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Record an audit log entry.
 *
 * @param PDO         $pdo
 * @param int|null    $userId     Who performed the action (null for system actions)
 * @param string      $action     e.g. 'CREATE', 'UPDATE', 'POST', 'VOID', 'APPROVE', 'DELETE'
 * @param string      $tableName  The table affected, e.g. 'gl_journalheader'
 * @param int|null    $recordId   The primary key of the affected row
 * @param mixed       $oldValue   Previous state (array/object will be JSON-encoded), or null
 * @param mixed       $newValue   New state (array/object will be JSON-encoded), or null
 */
function logAudit(PDO $pdo, ?int $userId, string $action, string $tableName, ?int $recordId, mixed $oldValue = null, mixed $newValue = null): void
{
    $oldJson = $oldValue !== null ? (is_string($oldValue) ? $oldValue : json_encode($oldValue)) : null;
    $newJson = $newValue !== null ? (is_string($newValue) ? $newValue : json_encode($newValue)) : null;

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt = $pdo->prepare(
        "INSERT INTO auditlog (user_id, action, table_name, record_id, old_value, new_value, ip_address)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$userId, $action, $tableName, $recordId, $oldJson, $newJson, $ip]);
}

/**
 * List audit log entries, optionally filtered by table name, with the
 * acting user's name joined in. Newest first.
 */
function getAuditLog(PDO $pdo, ?string $tableName = null, int $limit = 200): array
{
    $sql = "SELECT al.*, u.full_name, u.username
            FROM auditlog al
            LEFT JOIN users u ON u.user_id = al.user_id";

    if ($tableName) {
        $sql .= " WHERE al.table_name = ? ORDER BY al.created_at DESC LIMIT " . (int) $limit;
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tableName]);
    } else {
        $sql .= " ORDER BY al.created_at DESC LIMIT " . (int) $limit;
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll();
}