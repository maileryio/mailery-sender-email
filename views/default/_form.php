<?php

use Yiisoft\Html\Tag\Form;
use Mailery\Widget\Select\Select;
use Yiisoft\Form\Field;

/** @var Yiisoft\View\WebView $this */
/** @var Mailery\Sender\Email\Form\SenderForm $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

?>
<?= Form::tag()
        ->csrf($csrf)
        ->id('sender-email-form')
        ->post()
        ->open(); ?>

<?= Field::input(
        Select::class,
        $form,
        'channel',
        [
            'optionsData()' => [$form->getChannelListOptions()],
            'searchable()' => [false],
            'clearable()' => [false],
        ]
    ); ?>

<?= Field::text($form, 'name')->autofocus(); ?>

<?= Field::email($form, 'email')
        ->disabled($form->hasEntity()); ?>

<?= Field::text($form, 'replyName'); ?>

<?= Field::email($form, 'replyEmail'); ?>

<?= Field::textarea($form, 'description', ['rows()' => [5]]); ?>

<?= Field::submitButton()
        ->name('submit-sender-email-form')
        ->content($form->hasEntity() ? 'Save changes' : 'Add sender'); ?>

<?= Form::tag()->close(); ?>