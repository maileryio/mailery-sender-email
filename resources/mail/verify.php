<?php

use Yiisoft\Html\Html;

/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\View\WebView $this */
/** @var Mailery\Sender\Email\Entity\EmailSender $sender */

$verifyUrl = $url->generateAbsolute(
    '/sender/email/verify',
    [
        'id' => $sender->getId(),
        'token' => $sender->getVerification()->getToken(),
    ]
);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <main>
        <h1>Please confirm your email</h1>

        <p>This email has been sent to verify that <b><?= $sender->getEmail(); ?></b> is a valid email address at domain that belongs to you.</p>
        <p>To start sending from your email address you must click the link below to verify that you approve this action.</p>

        <?= Html::a($verifyUrl, $verifyUrl) ?>
    </main>

    <footer style="margin-top: 5em">
    -- <br>
    Mailed by Yii
    </footer>
</body>
</html>
