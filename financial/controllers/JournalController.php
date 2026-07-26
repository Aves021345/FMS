<?php
/**
 * Journal Controller
 * JSON API endpoint. Accountant creates drafts, Finance/Admin posts them.
 *
 * Expects POST with:
 *   action = 'create_draft' | 'post_journal' | 'void_journal' | 'list'
 *
 * create_draft expects:
 *   period_id, source_module_id, reference_no, description,
 *   lines[] = array of { account_id, debit, credit, description }
 *
 * post_journal / void_journal expect:
 *   journal_id
 *
 * list expects (optional):
 *   status = 'Draft' | 'Posted' | 'Voided'
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../services/JournalService.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'create_draft':
            requireRole(['ROLE_ACCOUNTANT', 'ROLE_ADMIN']);

            $lines = $_POST['lines'] ?? [];

            if (!is_array($lines) || empty($lines)) {
                throw new Exception('At least one journal line is required.');
            }

            $header = [
                'period_id'        => $_POST['period_id'] ?? null,
                'source_module_id' => $_POST['source_module_id'] ?? null,
                'reference_no'     => $_POST['reference_no'] ?? null,
                'description'      => $_POST['description'] ?? null,
                'prepared_by'      => $_SESSION['user_id'],
            ];

            if (!$header['period_id'] || !$header['source_module_id']) {
                throw new Exception('period_id and source_module_id are required.');
            }

            $journalId = createJournalDraft($pdo, $header, $lines);

            echo json_encode([
                'success'    => true,
                'message'    => 'Draft journal created.',
                'journal_id' => $journalId,
            ]);
            break;

        case 'post_journal':
            requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

            $journalId = (int) ($_POST['journal_id'] ?? 0);
            if (!$journalId) {
                throw new Exception('journal_id is required.');
            }

            postJournal($pdo, $journalId, $_SESSION['user_id']);

            echo json_encode([
                'success' => true,
                'message' => "Journal #{$journalId} posted successfully.",
            ]);
            break;

        case 'void_journal':
            requireRole(['ROLE_FINANCE', 'ROLE_ADMIN']);

            $journalId = (int) ($_POST['journal_id'] ?? 0);
            if (!$journalId) {
                throw new Exception('journal_id is required.');
            }

            voidJournal($pdo, $journalId, $_SESSION['user_id']);

            echo json_encode([
                'success' => true,
                'message' => "Journal #{$journalId} voided.",
            ]);
            break;

        case 'list':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

            $status = $_GET['status'] ?? $_POST['status'] ?? null;
            $journals = getAllJournals($pdo, $status);

            echo json_encode([
                'success'  => true,
                'journals' => $journals,
            ]);
            break;

        case 'view':
            requireRole(['ROLE_ADMIN', 'ROLE_FINANCE', 'ROLE_ACCOUNTANT', 'ROLE_AUDITOR']);

            $journalId = (int) ($_GET['journal_id'] ?? $_POST['journal_id'] ?? 0);
            if (!$journalId) {
                throw new Exception('journal_id is required.');
            }

            $header = getJournalById($pdo, $journalId);
            if (!$header) {
                throw new Exception('Journal not found.');
            }
            $lines = getJournalLines($pdo, $journalId);

            echo json_encode([
                'success' => true,
                'header'  => $header,
                'lines'   => $lines,
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown or missing action.']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}