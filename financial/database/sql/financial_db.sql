-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2026 at 12:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `financial_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accountspayable`
--

CREATE TABLE `accountspayable` (
  `ap_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `amount_paid` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(18,2) NOT NULL,
  `status` enum('Open','Partially Paid','Paid','Voided') NOT NULL DEFAULT 'Open',
  `journal_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accountsreceivable`
--

CREATE TABLE `accountsreceivable` (
  `ar_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `amount_collected` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(18,2) NOT NULL,
  `status` enum('Open','Partially Collected','Collected','Voided') NOT NULL DEFAULT 'Open',
  `journal_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ap_paymentapplication`
--

CREATE TABLE `ap_paymentapplication` (
  `payment_id` int(11) NOT NULL,
  `ap_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount_applied` decimal(18,2) NOT NULL,
  `payment_method` enum('Cash','Check','Bank Transfer','Online') NOT NULL DEFAULT 'Bank Transfer',
  `reference_no` varchar(50) DEFAULT NULL,
  `journal_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--

CREATE TABLE `auditlog` (
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bankaccounts`
--

CREATE TABLE `bankaccounts` (
  `bank_account_id` int(11) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `bank_name` varchar(150) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'PHP',
  `current_balance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budgetmanagement`
--

CREATE TABLE `budgetmanagement` (
  `budget_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `budget_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `actual_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `variance` decimal(18,2) GENERATED ALWAYS AS (`budget_amount` - `actual_amount`) STORED,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cashmanagement`
--

CREATE TABLE `cashmanagement` (
  `cash_id` int(11) NOT NULL,
  `bank_account_id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `transaction_type` enum('Deposit','Withdrawal','Transfer In','Transfer Out') NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `journal_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chartofaccounts`
--

CREATE TABLE `chartofaccounts` (
  `account_id` int(11) NOT NULL,
  `account_code` varchar(20) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_type` enum('Asset','Liability','Equity','Revenue','Expense') NOT NULL,
  `normal_balance` enum('Debit','Credit') NOT NULL,
  `parent_account_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collectionapplication`
--

CREATE TABLE `collectionapplication` (
  `application_id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `ar_id` int(11) NOT NULL,
  `amount_applied` decimal(18,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collectionmanagement`
--

CREATE TABLE `collectionmanagement` (
  `collection_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `collection_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_method` enum('Cash','Check','Bank Transfer','Online') NOT NULL DEFAULT 'Cash',
  `reference_no` varchar(50) DEFAULT NULL,
  `journal_id` int(11) DEFAULT NULL,
  `received_by` int(11) DEFAULT NULL,
  `status` enum('Pending','Applied','Voided') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_code` varchar(20) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `credit_limit` decimal(18,2) DEFAULT 0.00,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disbursementmanagement`
--

CREATE TABLE `disbursementmanagement` (
  `disbursement_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `ap_id` int(11) DEFAULT NULL,
  `bank_account_id` int(11) NOT NULL,
  `disbursement_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_method` enum('Cash','Check','Bank Transfer','Online') NOT NULL DEFAULT 'Bank Transfer',
  `reference_no` varchar(50) DEFAULT NULL,
  `journal_id` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `status` enum('Pending','Approved','Released','Voided') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financialreports`
--

CREATE TABLE `financialreports` (
  `report_id` int(11) NOT NULL,
  `report_name` varchar(150) NOT NULL,
  `report_type` enum('Trial Balance','Income Statement','Balance Sheet','Cash Flow','AP Aging','AR Aging','Budget Variance','Tax Summary','Custom') NOT NULL,
  `period_id` int(11) DEFAULT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL,
  `parameters` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fiscalperiods`
--

CREATE TABLE `fiscalperiods` (
  `period_id` int(11) NOT NULL,
  `period_name` varchar(50) NOT NULL,
  `fiscal_year` year(4) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Open','Closed') NOT NULL DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gl_journalheader`
--

CREATE TABLE `gl_journalheader` (
  `journal_id` int(11) NOT NULL,
  `journal_no` varchar(30) NOT NULL,
  `journal_date` date NOT NULL,
  `period_id` int(11) NOT NULL,
  `source_module_id` int(11) NOT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `total_debit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_credit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` enum('Draft','Posted','Voided') NOT NULL DEFAULT 'Draft',
  `prepared_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `posted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gl_journalline`
--

CREATE TABLE `gl_journalline` (
  `line_id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `line_no` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(18,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sourcemodules`
--

CREATE TABLE `sourcemodules` (
  `module_id` int(11) NOT NULL,
  `module_code` varchar(10) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_code` varchar(20) NOT NULL,
  `supplier_name` varchar(150) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taxmanagement`
--

CREATE TABLE `taxmanagement` (
  `tax_id` int(11) NOT NULL,
  `tax_type_id` int(11) NOT NULL,
  `transaction_ref` varchar(50) DEFAULT NULL,
  `tax_period` varchar(20) NOT NULL,
  `taxable_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','Filed','Paid') NOT NULL DEFAULT 'Pending',
  `filed_by` int(11) DEFAULT NULL,
  `filed_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taxtypes`
--

CREATE TABLE `taxtypes` (
  `tax_type_id` int(11) NOT NULL,
  `tax_code` varchar(20) NOT NULL,
  `tax_name` varchar(100) NOT NULL,
  `tax_rate` decimal(6,3) NOT NULL DEFAULT 0.000,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `role` enum('ROLE_ADMIN','ROLE_FINANCE','ROLE_ACCOUNTANT','ROLE_AUDITOR') NOT NULL DEFAULT 'ROLE_ACCOUNTANT',
  `status` enum('Active','Inactive','Locked') NOT NULL DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accountspayable`
--
ALTER TABLE `accountspayable`
  ADD PRIMARY KEY (`ap_id`),
  ADD KEY `fk_ap_supplier` (`supplier_id`),
  ADD KEY `fk_ap_journal` (`journal_id`),
  ADD KEY `fk_ap_user` (`created_by`),
  ADD KEY `idx_ap_status` (`status`),
  ADD KEY `idx_ap_duedate` (`due_date`);

--
-- Indexes for table `accountsreceivable`
--
ALTER TABLE `accountsreceivable`
  ADD PRIMARY KEY (`ar_id`),
  ADD KEY `fk_ar_customer` (`customer_id`),
  ADD KEY `fk_ar_journal` (`journal_id`),
  ADD KEY `fk_ar_user` (`created_by`),
  ADD KEY `idx_ar_status` (`status`),
  ADD KEY `idx_ar_duedate` (`due_date`);

--
-- Indexes for table `ap_paymentapplication`
--
ALTER TABLE `ap_paymentapplication`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_appay_ap` (`ap_id`),
  ADD KEY `fk_appay_journal` (`journal_id`),
  ADD KEY `fk_appay_user` (`created_by`);

--
-- Indexes for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `fk_audit_user` (`user_id`),
  ADD KEY `idx_audit_table` (`table_name`,`record_id`);

--
-- Indexes for table `bankaccounts`
--
ALTER TABLE `bankaccounts`
  ADD PRIMARY KEY (`bank_account_id`);

--
-- Indexes for table `budgetmanagement`
--
ALTER TABLE `budgetmanagement`
  ADD PRIMARY KEY (`budget_id`),
  ADD KEY `fk_budget_account` (`account_id`),
  ADD KEY `fk_budget_period` (`period_id`),
  ADD KEY `fk_budget_user` (`created_by`),
  ADD KEY `idx_budget_dept` (`department`);

--
-- Indexes for table `cashmanagement`
--
ALTER TABLE `cashmanagement`
  ADD PRIMARY KEY (`cash_id`),
  ADD KEY `fk_cash_bank` (`bank_account_id`),
  ADD KEY `fk_cash_journal` (`journal_id`),
  ADD KEY `fk_cash_user` (`created_by`),
  ADD KEY `idx_cash_date` (`transaction_date`);

--
-- Indexes for table `chartofaccounts`
--
ALTER TABLE `chartofaccounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `account_code` (`account_code`),
  ADD KEY `fk_coa_parent` (`parent_account_id`);

--
-- Indexes for table `collectionapplication`
--
ALTER TABLE `collectionapplication`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `fk_collapp_collection` (`collection_id`),
  ADD KEY `fk_collapp_ar` (`ar_id`);

--
-- Indexes for table `collectionmanagement`
--
ALTER TABLE `collectionmanagement`
  ADD PRIMARY KEY (`collection_id`),
  ADD KEY `fk_coll_customer` (`customer_id`),
  ADD KEY `fk_coll_journal` (`journal_id`),
  ADD KEY `fk_coll_user` (`received_by`),
  ADD KEY `idx_coll_status` (`status`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_code` (`customer_code`);

--
-- Indexes for table `disbursementmanagement`
--
ALTER TABLE `disbursementmanagement`
  ADD PRIMARY KEY (`disbursement_id`),
  ADD KEY `fk_disb_supplier` (`supplier_id`),
  ADD KEY `fk_disb_ap` (`ap_id`),
  ADD KEY `fk_disb_bank` (`bank_account_id`),
  ADD KEY `fk_disb_journal` (`journal_id`),
  ADD KEY `fk_disb_user` (`approved_by`),
  ADD KEY `idx_disb_status` (`status`);

--
-- Indexes for table `financialreports`
--
ALTER TABLE `financialreports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_report_period` (`period_id`),
  ADD KEY `fk_report_user` (`generated_by`);

--
-- Indexes for table `fiscalperiods`
--
ALTER TABLE `fiscalperiods`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `gl_journalheader`
--
ALTER TABLE `gl_journalheader`
  ADD PRIMARY KEY (`journal_id`),
  ADD UNIQUE KEY `journal_no` (`journal_no`),
  ADD KEY `fk_jh_period` (`period_id`),
  ADD KEY `fk_jh_module` (`source_module_id`),
  ADD KEY `fk_jh_prepared` (`prepared_by`),
  ADD KEY `fk_jh_approved` (`approved_by`),
  ADD KEY `idx_gljh_date` (`journal_date`),
  ADD KEY `idx_gljh_status` (`status`);

--
-- Indexes for table `gl_journalline`
--
ALTER TABLE `gl_journalline`
  ADD PRIMARY KEY (`line_id`),
  ADD KEY `fk_jl_journal` (`journal_id`),
  ADD KEY `fk_jl_account` (`account_id`);

--
-- Indexes for table `sourcemodules`
--
ALTER TABLE `sourcemodules`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `module_code` (`module_code`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `supplier_code` (`supplier_code`);

--
-- Indexes for table `taxmanagement`
--
ALTER TABLE `taxmanagement`
  ADD PRIMARY KEY (`tax_id`),
  ADD KEY `fk_tax_type` (`tax_type_id`),
  ADD KEY `fk_tax_user` (`filed_by`),
  ADD KEY `idx_tax_status` (`status`);

--
-- Indexes for table `taxtypes`
--
ALTER TABLE `taxtypes`
  ADD PRIMARY KEY (`tax_type_id`),
  ADD UNIQUE KEY `tax_code` (`tax_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accountspayable`
--
ALTER TABLE `accountspayable`
  MODIFY `ap_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accountsreceivable`
--
ALTER TABLE `accountsreceivable`
  MODIFY `ar_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ap_paymentapplication`
--
ALTER TABLE `ap_paymentapplication`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auditlog`
--
ALTER TABLE `auditlog`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bankaccounts`
--
ALTER TABLE `bankaccounts`
  MODIFY `bank_account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budgetmanagement`
--
ALTER TABLE `budgetmanagement`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cashmanagement`
--
ALTER TABLE `cashmanagement`
  MODIFY `cash_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chartofaccounts`
--
ALTER TABLE `chartofaccounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collectionapplication`
--
ALTER TABLE `collectionapplication`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collectionmanagement`
--
ALTER TABLE `collectionmanagement`
  MODIFY `collection_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disbursementmanagement`
--
ALTER TABLE `disbursementmanagement`
  MODIFY `disbursement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financialreports`
--
ALTER TABLE `financialreports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fiscalperiods`
--
ALTER TABLE `fiscalperiods`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gl_journalheader`
--
ALTER TABLE `gl_journalheader`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gl_journalline`
--
ALTER TABLE `gl_journalline`
  MODIFY `line_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sourcemodules`
--
ALTER TABLE `sourcemodules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxmanagement`
--
ALTER TABLE `taxmanagement`
  MODIFY `tax_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxtypes`
--
ALTER TABLE `taxtypes`
  MODIFY `tax_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accountspayable`
--
ALTER TABLE `accountspayable`
  ADD CONSTRAINT `fk_ap_journal` FOREIGN KEY (`journal_id`) REFERENCES `gl_journalheader` (`journal_id`),
  ADD CONSTRAINT `fk_ap_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `fk_ap_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `accountsreceivable`
--
ALTER TABLE `accountsreceivable`
  ADD CONSTRAINT `fk_ar_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `fk_ar_journal` FOREIGN KEY (`journal_id`) REFERENCES `gl_journalheader` (`journal_id`),
  ADD CONSTRAINT `fk_ar_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ap_paymentapplication`
--
ALTER TABLE `ap_paymentapplication`
  ADD CONSTRAINT `fk_appay_ap` FOREIGN KEY (`ap_id`) REFERENCES `accountspayable` (`ap_id`),
  ADD CONSTRAINT `fk_appay_journal` FOREIGN KEY (`journal_id`) REFERENCES `gl_journalheader` (`journal_id`),
  ADD CONSTRAINT `fk_appay_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `budgetmanagement`
--
ALTER TABLE `budgetmanagement`
  ADD CONSTRAINT `fk_budget_account` FOREIGN KEY (`account_id`) REFERENCES `chartofaccounts` (`account_id`),
  ADD CONSTRAINT `fk_budget_period` FOREIGN KEY (`period_id`) REFERENCES `fiscalperiods` (`period_id`),
  ADD CONSTRAINT `fk_budget_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `cashmanagement`
--
ALTER TABLE `cashmanagement`
  ADD CONSTRAINT `fk_cash_bank` FOREIGN KEY (`bank_account_id`) REFERENCES `bankaccounts` (`bank_account_id`),
  ADD CONSTRAINT `fk_cash_journal` FOREIGN KEY (`journal_id`) REFERENCES `gl_journalheader` (`journal_id`),
  ADD CONSTRAINT `fk_cash_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `chartofaccounts`
--
ALTER TABLE `chartofaccounts`
  ADD CONSTRAINT `fk_coa_parent` FOREIGN KEY (`parent_account_id`) REFERENCES `chartofaccounts` (`account_id`) ON DELETE SET NULL;

--
-- Constraints for table `collectionapplication`
--
ALTER TABLE `collectionapplication`
  ADD CONSTRAINT `fk_collapp_ar` FOREIGN KEY (`ar_id`) REFERENCES `accountsreceivable` (`ar_id`),
  ADD CONSTRAINT `fk_collapp_collection` FOREIGN KEY (`collection_id`) REFERENCES `collectionmanagement` (`collection_id`);

--
-- Constraints for table `collectionmanagement`
--
ALTER TABLE `collectionmanagement`
  ADD CONSTRAINT `fk_coll_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `fk_coll_journal` FOREIGN KEY (`journal_id`) REFERENCES `gl_journalheader` (`journal_id`),
  ADD CONSTRAINT `fk_coll_user` FOREIGN KEY (`received_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `disbursementmanagement`
--
ALTER TABLE `disbursementmanagement`
  ADD CONSTRAINT `fk_disb_ap` FOREIGN KEY (`ap_id`) REFERENCES `accountspayable` (`ap_id`),
  ADD CONSTRAINT `fk_disb_bank` FOREIGN KEY (`bank_account_id`) REFERENCES `bankaccounts` (`bank_account_id`),
  ADD CONSTRAINT `fk_disb_journal` FOREIGN KEY (`journal_id`) REFERENCES `gl_journalheader` (`journal_id`),
  ADD CONSTRAINT `fk_disb_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `fk_disb_user` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `financialreports`
--
ALTER TABLE `financialreports`
  ADD CONSTRAINT `fk_report_period` FOREIGN KEY (`period_id`) REFERENCES `fiscalperiods` (`period_id`),
  ADD CONSTRAINT `fk_report_user` FOREIGN KEY (`generated_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `gl_journalheader`
--
ALTER TABLE `gl_journalheader`
  ADD CONSTRAINT `fk_jh_approved` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_jh_module` FOREIGN KEY (`source_module_id`) REFERENCES `sourcemodules` (`module_id`),
  ADD CONSTRAINT `fk_jh_period` FOREIGN KEY (`period_id`) REFERENCES `fiscalperiods` (`period_id`),
  ADD CONSTRAINT `fk_jh_prepared` FOREIGN KEY (`prepared_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `gl_journalline`
--
ALTER TABLE `gl_journalline`
  ADD CONSTRAINT `fk_jl_account` FOREIGN KEY (`account_id`) REFERENCES `chartofaccounts` (`account_id`),
  ADD CONSTRAINT `fk_jl_journal` FOREIGN KEY (`journal_id`) REFERENCES `gl_journalheader` (`journal_id`) ON DELETE CASCADE;

--
-- Constraints for table `taxmanagement`
--
ALTER TABLE `taxmanagement`
  ADD CONSTRAINT `fk_tax_type` FOREIGN KEY (`tax_type_id`) REFERENCES `taxtypes` (`tax_type_id`),
  ADD CONSTRAINT `fk_tax_user` FOREIGN KEY (`filed_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
