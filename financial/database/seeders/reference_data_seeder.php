<?php
/**
 * Reference Data Seeder
 * Seeds minimal rows needed to actually test the GL engine:
 * source modules, one open fiscal period, and a couple of GL accounts.
 * Run once: http://localhost/financial/database/seeders/reference_data_seeder.php
 */

require_once __DIR__ . '/../../config/database.php';

echo "<h2>Seeding reference data...</h2><ul>";

// ---- Source Modules ----
$modules = [
    ['GL', 'General Ledger', 'Manual journal entries'],
    ['AP', 'Accounts Payable', 'Supplier invoices and bills'],
    ['AR', 'Accounts Receivable', 'Customer invoices'],
    ['CASH', 'Cash Management', 'Bank deposits, withdrawals, transfers'],
    ['DISB', 'Disbursement', 'Payments to suppliers'],
    ['COLL', 'Collection', 'Receipts from customers'],
];

$stmt = $pdo->prepare("SELECT module_id FROM sourcemodules WHERE module_code = ?");
$insertModule = $pdo->prepare("INSERT INTO sourcemodules (module_code, module_name, description) VALUES (?, ?, ?)");

foreach ($modules as [$code, $name, $desc]) {
    $stmt->execute([$code]);
    if ($stmt->fetchColumn()) {
        echo "<li>⚠️ Module '{$code}' already exists — skipped.</li>";
        continue;
    }
    $insertModule->execute([$code, $name, $desc]);
    echo "<li>✅ Source module created: {$code} - {$name}</li>";
}

// ---- Fiscal Period (current month, Open) ----
$periodName = date('F Y'); // e.g. "July 2026"
$fiscalYear = date('Y');
$startDate  = date('Y-m-01');
$endDate    = date('Y-m-t');

$stmt = $pdo->prepare("SELECT period_id FROM fiscalperiods WHERE period_name = ?");
$stmt->execute([$periodName]);

if ($stmt->fetchColumn()) {
    echo "<li>⚠️ Fiscal period '{$periodName}' already exists — skipped.</li>";
} else {
    $insertPeriod = $pdo->prepare(
        "INSERT INTO fiscalperiods (period_name, fiscal_year, start_date, end_date, status) VALUES (?, ?, ?, ?, 'Open')"
    );
    $insertPeriod->execute([$periodName, $fiscalYear, $startDate, $endDate]);
    echo "<li>✅ Fiscal period created: {$periodName} ({$startDate} to {$endDate})</li>";
}

// ---- Minimal Chart of Accounts (enough to test a journal entry) ----
$accounts = [
    ['1000', 'Cash on Hand', 'Asset', 'Debit'],
    ['1010', 'Bank - Operating Account', 'Asset', 'Debit'],
    ['1200', 'Accounts Receivable', 'Asset', 'Debit'],
    ['2000', 'Accounts Payable', 'Liability', 'Credit'],
    ['3000', 'Owner\'s Equity', 'Equity', 'Credit'],
    ['4000', 'Tour Revenue', 'Revenue', 'Credit'],
    ['5000', 'Operating Expenses', 'Expense', 'Debit'],
];

$stmt = $pdo->prepare("SELECT account_id FROM chartofaccounts WHERE account_code = ?");
$insertAccount = $pdo->prepare(
    "INSERT INTO chartofaccounts (account_code, account_name, account_type, normal_balance) VALUES (?, ?, ?, ?)"
);

foreach ($accounts as [$code, $name, $type, $normal]) {
    $stmt->execute([$code]);
    if ($stmt->fetchColumn()) {
        echo "<li>⚠️ Account '{$code} - {$name}' already exists — skipped.</li>";
        continue;
    }
    $insertAccount->execute([$code, $name, $type, $normal]);
    echo "<li>✅ Account created: {$code} - {$name} ({$type})</li>";
}

echo "</ul><p>Done. You now have enough reference data to create a test journal entry.</p>";

// ---- Sample Suppliers ----
echo "<h3>Suppliers</h3><ul>";

$suppliers = [
    ['SUP-001', 'ABC Office Supplies Inc.', 'Juan Dela Cruz', 'abc@supplier.local', '0917-000-0001'],
    ['SUP-002', 'Metro Fuel Depot', 'Maria Santos', 'metro@supplier.local', '0917-000-0002'],
];

$stmt = $pdo->prepare("SELECT supplier_id FROM suppliers WHERE supplier_code = ?");
$insertSupplier = $pdo->prepare(
    "INSERT INTO suppliers (supplier_code, supplier_name, contact_person, email, phone, status) VALUES (?, ?, ?, ?, ?, 'Active')"
);

foreach ($suppliers as [$code, $name, $contact, $email, $phone]) {
    $stmt->execute([$code]);
    if ($stmt->fetchColumn()) {
        echo "<li>⚠️ Supplier '{$code}' already exists — skipped.</li>";
        continue;
    }
    $insertSupplier->execute([$code, $name, $contact, $email, $phone]);
    echo "<li>✅ Supplier created: {$code} - {$name}</li>";
}
echo "</ul>";

// ---- Sample Tax Types ----
echo "<h3>Tax Types</h3><ul>";

$taxTypes = [
    ['VAT12', 'Value Added Tax', 12.000, 'Standard 12% VAT on sales/purchases'],
    ['WHT2', 'Withholding Tax - Goods', 2.000, '2% expanded withholding tax on goods'],
    ['WHT5', 'Withholding Tax - Services', 5.000, '5% expanded withholding tax on services'],
];

$stmt = $pdo->prepare("SELECT tax_type_id FROM taxtypes WHERE tax_code = ?");
$insertTaxType = $pdo->prepare(
    "INSERT INTO taxtypes (tax_code, tax_name, tax_rate, description) VALUES (?, ?, ?, ?)"
);

foreach ($taxTypes as [$code, $name, $rate, $desc]) {
    $stmt->execute([$code]);
    if ($stmt->fetchColumn()) {
        echo "<li>⚠️ Tax type '{$code}' already exists — skipped.</li>";
        continue;
    }
    $insertTaxType->execute([$code, $name, $rate, $desc]);
    echo "<li>✅ Tax type created: {$code} - {$name} ({$rate}%)</li>";
}
echo "</ul>";
echo "<h3>Customers</h3><ul>";

$customers = [
    ['CUST-001', 'Sunrise Travel Group', 'Ana Reyes', 'sunrise@customer.local', '0917-100-0001'],
    ['CUST-002', 'Golden Horizon Tours', 'Mark Villanueva', 'golden@customer.local', '0917-100-0002'],
];

$stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_code = ?");
$insertCustomer = $pdo->prepare(
    "INSERT INTO customers (customer_code, customer_name, contact_person, email, phone, status) VALUES (?, ?, ?, ?, ?, 'Active')"
);

foreach ($customers as [$code, $name, $contact, $email, $phone]) {
    $stmt->execute([$code]);
    if ($stmt->fetchColumn()) {
        echo "<li>⚠️ Customer '{$code}' already exists — skipped.</li>";
        continue;
    }
    $insertCustomer->execute([$code, $name, $contact, $email, $phone]);
    echo "<li>✅ Customer created: {$code} - {$name}</li>";
}
echo "</ul>";
echo "<h3>Bank Accounts</h3><ul>";

$stmt = $pdo->prepare("SELECT bank_account_id FROM bankaccounts WHERE account_number = ?");
$stmt->execute(['0001-0001-01']);

if ($stmt->fetchColumn()) {
    echo "<li>⚠️ Bank account already exists — skipped.</li>";
} else {
    $insertBank = $pdo->prepare(
        "INSERT INTO bankaccounts (account_name, bank_name, account_number, currency, current_balance, status) 
         VALUES (?, ?, ?, 'PHP', ?, 'Active')"
    );
    $insertBank->execute(['Operating Account', 'BDO Unibank', '0001-0001-01', 500000.00]);
    echo "<li>✅ Bank account created: Operating Account - BDO Unibank (starting balance: 500,000.00)</li>";
}
echo "</ul>";
echo "<p><strong>Remember to delete this seeder file when done.</strong></p>";