<?php

use Yiisoft\Html\Html;
use Yiisoft\Form\Widget\Form;

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\View\WebView $this */
/** @var Mailery\Sender\Email\Form\SenderForm $form */
/** @var string $csrf */

?>
<div class="row">
    <div class="col-12 col-xl-4">
        <?= Form::widget()
            ->options(
                [
                    'id' => 'form-sender',
                    'csrf' => $csrf,
                    'enctype' => 'multipart/form-data',
                ]
            )
            ->begin(); ?>

        <?= $field->config($form, 'name'); ?>
        <?= $field->config($form, 'email')
            ->textInput(['disabled' => !empty($sender)]); ?>
        <?= $field->config($form, 'replyName'); ?>
        <?= $field->config($form, 'replyEmail'); ?>

        <?= Html::submitButton(
            'Save',
            [
                'class' => 'btn btn-primary float-right mt-2'
            ]
        ); ?>

        <?= Form::end(); ?>
    </div>
</div>