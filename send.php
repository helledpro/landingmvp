<?php
header('Content-Type: application/json; charset=utf-8');
$config = require __DIR__ . '/config.php';
function respond($ok, $message, $code = 200) { http_response_code($code); echo json_encode(['ok'=>$ok,'message'=>$message], JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(false, 'Метод не поддерживается.', 405);
if (!empty($_POST['website'])) respond(false, 'Заявка отклонена.', 400);
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$clinic = trim($_POST['clinic'] ?? '');
$comment = trim($_POST['comment'] ?? '');
$privacy = isset($_POST['privacy']);
if ($name === '') respond(false, 'Укажите имя.', 422);
if ($phone === '' && $email === '') respond(false, 'Укажите телефон или email.', 422);
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) respond(false, 'Укажите корректный email.', 422);
if (!$privacy) respond(false, 'Необходимо согласие с политикой конфиденциальности.', 422);
$joined = mb_strtolower($name.' '.$phone.' '.$email.' '.$clinic.' '.$comment, 'UTF-8');
$spamWords = ['viagra','casino','crypto','http://','https://','<a href'];
foreach ($spamWords as $word) { if (strpos($joined, $word) !== false) respond(false, 'Заявка похожа на спам.', 400); }
foreach ([$name,$phone,$email,$clinic] as $field) { if (mb_strlen($field, 'UTF-8') > 160) respond(false, 'Слишком длинное значение поля.', 422); }
if (mb_strlen($comment, 'UTF-8') > 2000) respond(false, 'Комментарий слишком длинный.', 422);
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$page = $_SERVER['HTTP_REFERER'] ?? 'unknown';
$date = date('Y-m-d H:i:s');
$subject = 'Новая заявка с сайта '.$config['site_name'];
$body = "Новая заявка с сайта {$config['site_name']}\n\n".
"Имя: {$name}\nТелефон: {$phone}\nEmail: {$email}\nКлиника: {$clinic}\nКомментарий: {$comment}\n\n".
"Дата: {$date}\nСтраница: {$page}\nIP: {$ip}\nUser-Agent: {$ua}\n";
$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'From: '.$config['site_name'].' <'.$config['from_email'].'>';
if ($email !== '') $headers[] = 'Reply-To: '.$email;
$ok = mail($config['recipient_email'], '=?UTF-8?B?'.base64_encode($subject).'?=', $body, implode("\r\n", $headers));
if (!$ok) respond(false, 'Сервер не смог отправить письмо. Проверьте настройки mail() на хостинге.', 500);
respond(true, 'Заявка отправлена. Мы свяжемся с вами в течение 15 минут.');
