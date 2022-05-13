<?php declare(strict_types=1);

use Mailery\Web\Widget\FlashMessage;
use Yiisoft\Yii\Widgets\ContentDecorator;

/** @var Mailery\Sender\Email\Form\SenderForm $form */
/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-sender-email/views/default/_layout.php')
    ->parameters(compact('sender', 'csrf'))
    ->begin(); ?>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= FlashMessage::widget(); ?>
    </div>
</div>
<div class="mb-2"></div>

<div class="row">
    <div class="col-12">
        <?= $this->render('_form', compact('csrf', 'field', 'form')) ?>
    </div>
</div>

<?= ContentDecorator::end() ?>