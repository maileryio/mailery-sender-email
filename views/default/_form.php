<?php

use Yiisoft\Form\Widget\Form;
use Mailery\Widget\Select\Select;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var \Mailery\Sender\Email\Form\SenderForm $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>
<?= Form::widget()
        ->csrf($csrf)
        ->id('sender-email-form')
        ->begin(); ?>

<?= $field->select(
        $form,
        'channel',
        [
            'class' => Select::class,
            'items()' => [$form->getChannelListOptions()],
            'searchable()' => [false],
            'clearable()' => [false],
        ]
    ); ?>

<?= $field->text($form, 'name')->autofocus(); ?>

<?= $field->email($form, 'email')
        ->attributes(['disabled' => $form->hasEntity()]); ?>

<?= $field->text($form, 'replyName'); ?>

<?= $field->email($form, 'replyEmail'); ?>

<?= $field->textArea($form, 'description', ['rows()' => [5]]); ?>

<?= $field->submitButton()
        ->class('btn btn-primary float-right mt-2')
        ->name('submit-sender-email-form')
        ->value($form->hasEntity() ? 'Save changes' : 'Add sender'); ?>

<?= Form::end(); ?>