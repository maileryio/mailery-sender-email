<?php declare(strict_types=1);

use Mailery\Icon\Icon;
use Mailery\Web\Widget\FlashMessage;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\Router\UrlGeneratorInterface $urlGenerator */
/** @var Yiisoft\View\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Sender\Email\Entity\EmailSender $sender */
/** @var Mailery\Sender\Email\Form\SenderForm $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('Edit Sender #' . $sender->getId());

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h3">Edit sender #<?= $sender->getId(); ?></h1>
            <div class="btn-toolbar float-right">
                <a class="btn btn-sm btn-info mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/sender/email/view', ['id' => $sender->getId()]); ?>">
                    <?= Icon::widget()->name('eye')->options(['class' => 'mr-1']); ?>
                    View
                </a>
                <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/sender/email/index'); ?>">
                    Back
                </a>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12 col-xl-4">
        <?= FlashMessage::widget(); ?>
    </div>
</div>
<div class="mb-2"></div>

<?= $this->render('_form', compact('csrf', 'field', 'form', 'sender')) ?>