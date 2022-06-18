<?php declare(strict_types=1);

use Mailery\Sender\Email\Entity\EmailSender;
use Yiisoft\Yii\Widgets\ContentDecorator;
use Yiisoft\Yii\DataView\DetailView;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Sender\Email\Entity\EmailSender $sender */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle($sender->getName());

?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-sender-email/views/default/_layout.php')
    ->parameters(compact('sender', 'csrf'))
    ->begin(); ?>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <h6 class="font-weight-bold">General details</h6>
    </div>
</div>

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= DetailView::widget()
            ->model($sender)
            ->options([
                'class' => 'table detail-view',
            ])
            ->emptyValue('<span class="text-muted">(not set)</span>')
            ->attributes([
                [
                    'label' => 'Name',
                    'value' => function (EmailSender $data, $index) {
                        return $data->getName();
                    },
                ],
                [
                    'label' => 'Email',
                    'value' => function (EmailSender $data, $index) {
                        return $data->getEmail();
                    },
                ],
                [
                    'label' => 'Reply name',
                    'value' => function (EmailSender $data, $index) {
                        return $data->getReplyName();
                    },
                ],
                [
                    'label' => 'Reply email',
                    'value' => function (EmailSender $data, $index) {
                        return $data->getReplyEmail();
                    },
                ],
                [
                    'label' => 'Status',
                    'value' => function (EmailSender $data, $index) {
                        return '<span class="badge ' . $data->getStatus()->getCssClass() . '">' . $data->getStatus()->getLabel() . '</span>';
                    },
                ],
                [
                    'label' => 'Description',
                    'value' => function (EmailSender $data, $index) {
                        return $data->getDescription();
                    },
                ],
            ]);
        ?>
    </div>
</div>

<?= ContentDecorator::end() ?>
