<?php declare(strict_types=1);

use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\Icon\Icon;
use Mailery\Sender\Email\Entity\EmailSender;
use Mailery\Widget\Link\Link;
use Mailery\Widget\Search\Widget\SearchWidget;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\GridView;

/** @var Yiisoft\Yii\WebView $this */
/** @var Mailery\Sender\Email\Field\VerificationType $verificationType */
/** @var Mailery\Widget\Search\Form\SearchForm $searchForm */
/** @var Yiisoft\Aliases\Aliases $aliases */
/** @var Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Yiisoft\Data\Paginator\PaginatorInterface $paginator */
/** @var Yiisoft\Yii\View\Csrf $csrf */

$this->setTitle('Email addresses');

?><div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md">
                        <h4 class="mb-0">Email addresses</h4>
                    </div>
                    <div class="col-auto">
                        <div class="btn-toolbar float-right">
                            <?= SearchWidget::widget()->form($searchForm); ?>
                            <b-dropdown right size="sm" variant="secondary" class="mb-2">
                                <template v-slot:button-content>
                                    <?= Icon::widget()->name('settings'); ?>
                                </template>
                                <?= ActivityLogLink::widget()
                                    ->tag('b-dropdown-item')
                                    ->label('Activity log')
                                    ->group('sender'); ?>
                            </b-dropdown>
                            <a class="btn btn-sm btn-primary mx-sm-1 mb-2" href="<?= $url->generate('/sender/email/create'); ?>">
                                <?= Icon::widget()->name('plus')->options(['class' => 'mr-1']); ?>
                                Add new address
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
                <?= GridView::widget()
                    ->layout("{items}\n<div class=\"mb-4\"></div>\n{summary}\n<div class=\"float-right\">{pager}</div>")
                    ->options([
                        'class' => 'table-responsive',
                    ])
                    ->tableOptions([
                        'class' => 'table table-hover',
                    ])
                    ->emptyText('No data')
                    ->emptyTextOptions([
                        'class' => 'text-center text-muted mt-4 mb-4',
                    ])
                    ->paginator($paginator)
                    ->currentPage($paginator->getCurrentPage())
                    ->columns([
                        [
                            'label()' => ['Name'],
                            'value()' => [fn (EmailSender $model) => Html::a($model->getName(), $url->generate($model->getViewRouteName(), $model->getViewRouteParams()))],
                        ],
                        [
                            'label()' => ['Email'],
                            'value()' => [fn (EmailSender $model) => $model->getEmail()],
                        ],
                        [
                            'label()' => ['Reply name'],
                            'value()' => [fn (EmailSender $model) => $model->getReplyName()],
                        ],
                        [
                            'label()' => ['Reply email'],
                            'value()' => [fn (EmailSender $model) => $model->getReplyEmail()],
                        ],
                        [
                            'label()' => ['Verification'],
                            'value()' => [fn (EmailSender $model) => $model->getVerification()->getType()->getLabel()],
                        ],
                        [
                            'label()' => ['Status'],
                            'value()' => [fn (EmailSender $model) => '<span class="badge ' . $model->getStatus()->getCssClass() . '">' . $model->getStatus()->getLabel() . '</span>'],
                        ],
                        [
                            'label()' => ['Edit'],
                            'value()' => [static function (EmailSender $model) use($url) {
                                return Html::a(
                                    Icon::widget()->name('pencil')->render(),
                                    $url->generate($model->getEditRouteName(), $model->getEditRouteParams()),
                                    [
                                        'class' => 'text-decoration-none mr-3',
                                    ]
                                )
                                ->encode(false);
                            }],
                        ],
                        [
                            'label()' => ['Delete'],
                            'value()' => [static function (EmailSender $model) use($url, $csrf) {
                                return Link::widget()
                                    ->csrf($csrf)
                                    ->label(Icon::widget()->name('delete')->options(['class' => 'mr-1'])->render())
                                    ->method('delete')
                                    ->href($url->generate($model->getDeleteRouteName(), $model->getDeleteRouteParams()))
                                    ->confirm('Are you sure?')
                                    ->options([
                                        'class' => 'text-decoration-none text-danger',
                                    ])
                                    ->encode(false);
                            }],
                        ],
                    ]);
                ?>
            </div>
        </div>
    </div>
</div>
