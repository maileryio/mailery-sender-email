<?php declare(strict_types=1);

/** @var Yiisoft\Form\Widget\Field $field */
/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\View\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Yiisoft\Form\FormModelInterface $form */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('New sender');

?><div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <h4 class="mb-0">New sender</h4>
                    </div>
                    <div class="col-auto">
                        <div class="btn-toolbar float-right">
                            <a class="btn btn-sm btn-outline-secondary mx-sm-1" href="<?= $url->generate('/sender/default/index'); ?>">
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <?= $this->render('_form', compact('csrf', 'field', 'form')) ?>
            </div>
        </div>
    </div>
</div>
