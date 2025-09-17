<?php
require __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$timeToCall = $_POST['time_to_call'] ?? '';

if (!$phone || !$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Phone and email are required']);
    exit;
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $config['smtp']['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp']['username'];
    $mail->Password = $config['smtp']['password'];
    $mail->SMTPSecure = $config['smtp']['encryption'];
    $mail->Port = $config['smtp']['port'];

    $mail->setFrom($config['smtp']['from_email'], $config['smtp']['from_name']);
    $mail->addAddress($config['smtp']['from_email'], 'Admin');

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = basename($_FILES['file']['name']);
        $destPath = $uploadDir . $fileName;
        move_uploaded_file($fileTmpPath, $destPath);
        $mail->addAttachment($destPath);
    }

    $mail->isHTML(true);
    $mail->Subject = 'New Contact Request';
    $mail->Body = "<b>Phone:</b> {$phone}<br><b>Email:</b> {$email}<br><b>Time to call:</b> {$timeToCall}";

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
