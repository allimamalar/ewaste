<?php
$db = new PDO('sqlite:ewaste.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create Users Table
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    role TEXT NOT NULL,
    wallet TEXT NOT NULL,
    password TEXT NOT NULL
)");

// Create E-Waste Table (Public/Private Hybrid)
$db->exec("CREATE TABLE IF NOT EXISTS submissions (
    id TEXT PRIMARY KEY,
    user_email TEXT NOT NULL,
    device_name TEXT NOT NULL,
    category TEXT NOT NULL,
    ownership TEXT,
    location TEXT,
    image TEXT,
    status TEXT DEFAULT 'Submitted',
    blockchain_hash TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Create Transactions/Blockchain Ledger Table for Transparency
$db->exec("CREATE TABLE IF NOT EXISTS ledger (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    submission_id TEXT,
    action TEXT,
    status TEXT,
    blockchain_hash TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
)");
?>
