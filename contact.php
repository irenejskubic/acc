<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function respond(int $status, bool $success, string $message): void {
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(405, false, 'Method not allowed.');
}

if (!empty($_POST['company'] ?? '')) {
    respond(200, true, 'Message accepted.');
}

$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$service = trim((string)($_POST['service'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$allowedServices = ['starter', 'standard', 'premium', 'custom', 'social', 'influencer', 'ai', 'other'];

function text_length(string $value): int {
    return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
}

if (text_length($name) < 2 || text_length($name) > 100) {
    respond(422, false, 'Invalid name.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || text_length($email) > 160) {
    respond(422, false, 'Invalid email address.');
}
if (!in_array($service, $allowedServices, true)) {
    respond(422, false, 'Invalid service.');
}
if (text_length($message) < 10 || text_length($message) > 3000) {
    respond(422, false, 'Invalid message.');
}

$ip = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$rateFile = sys_get_temp_dir() . '/ycc_contact_' . hash('sha256', $ip);
$now = time();
if (is_file($rateFile)) {
    $lastRequest = (int)file_get_contents($rateFile);
    if ($now - $lastRequest < 30) {
        respond(429, false, 'Please wait before sending another inquiry.');
    }
}
@file_put_contents($rateFile, (string)$now, LOCK_EX);

$serviceLabels = [
    'starter' => 'Začetni paket', 'standard' => 'Standard paket',
    'premium' => 'Premium paket', 'custom' => 'Custom paket',
    'social' => 'Vodenje družbenih omrežij',
    'influencer' => 'Influencer marketing',
    'ai' => 'AI-podprta produkcija', 'other' => 'Drugo'
];

$safeName = preg_replace('/[\r\n]+/', ' ', $name);
$safeEmail = preg_replace('/[\r\n]+/', '', $email);
$subject = 'Novo povprasevanje z ycc.si - ' . $serviceLabels[$service];
$body = "Ime: {$safeName}\nE-pošta: {$safeEmail}\nStoritev: {$serviceLabels[$service]}\n\nSporočilo:\n{$message}\n";
$headers = [
    'From: Your Content Co. website <website@ycc.si>',
    'Reply-To: ' . $safeEmail,
    'Content-Type: text/plain; charset=UTF-8'
];

if (!mail('info@ycc.si', $subject, $body, implode("\r\n", $headers))) {
    error_log('YCC contact form: mail() failed');
    respond(500, false, 'Message could not be sent.');
}

respond(200, true, 'Message sent.');
