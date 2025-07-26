<?php
session_start();
header('Content-Type: application/json');

$host = "localhost";
$dbname = "intern";
$username = "root";     // change to your DB user
$password = "root";         // change to your DB pass

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $passwordInput = $data['password'] ?? '';

    if (!$email || !$passwordInput) {
        echo json_encode(['error' => 'Email and password required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($passwordInput, $user['password'])) {
        $_SESSION['uname'] = $user['uname'];
        echo json_encode(['success' => true, 'uname' => $user['uname']]);
    } else {
        echo json_encode(['error' => 'Invalid credentials']);
    }
    exit;
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// Check login for below actions


if ($action === 'getCompanies') {
    $stmt = $pdo->query("SELECT * FROM companies");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($companies);
    exit;
}

if ($action === 'addCompany') {
    $data = json_decode(file_get_contents('php://input'), true);
    $cname = $data['cname'] ?? '';
    $clogo = $data['clogo'] ?? '';
    $cdesc = $data['cdesc'] ?? '';
    $dept = $data['dept'] ?? '';
    $loc = $data['loc'] ?? '';
    $size = $data['size'] ?? '';
    $website = $data['website'] ?? '';
    $cmail = $data['cmail'] ?? '';
    $ph = $data['ph'] ?? '';

    if (!$cname) {
        echo json_encode(['error' => 'Company name required']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO companies 
        (clogo, cname, cdesc, dept, loc, size, website, cmail, ph)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([$clogo, $cname, $cdesc, $dept, $loc, $size, $website, $cmail, $ph]);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'deleteCompany') {
    $cid = intval($_GET['cid'] ?? 0);
    if ($cid <= 0) {
        echo json_encode(['error' => 'Invalid company id']);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM companies WHERE cid = ?");
    $stmt->execute([$cid]);
    echo json_encode(['success' => true]);
    exit;
}

// You can add updateCompany similarly
if ($action === 'updateCompany') {
    $cid = intval($_GET['cid'] ?? 0);
    if ($cid <= 0) {
        echo json_encode(['error' => 'Invalid company id']);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['error' => 'No data received']);
        exit;
    }

    // Build query dynamically for provided fields
    $allowed = ['cname','clogo','cdesc','dept','loc','size','website','cmail','ph'];
    $fields = [];
    $values = [];
    foreach ($allowed as $field) {
        if (isset($data[$field])) {
            $fields[] = "$field = ?";
            $values[] = $data[$field];
        }
    }
    if (count($fields) === 0) {
        echo json_encode(['error' => 'No valid fields to update']);
        exit;
    }

    $values[] = $cid;
    $sql = "UPDATE companies SET " . implode(", ", $fields) . " WHERE cid = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    echo json_encode(['success' => true]);
    exit;
}


echo json_encode(['error' => 'Invalid action']);
