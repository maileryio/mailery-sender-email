<?php declare(strict_types=1);

use Mailery\Activity\Log\Widget\ActivityLogLink;
use Mailery\Icon\Icon;
use Mailery\Sender\Email\Entity\Sender;
use Mailery\Widget\Dataview\DetailView;
use Mailery\Widget\Link\Link;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Sender\Email\Entity\Sender $sender */
/** @var string $csrf */
/** @var bool $submitted */

$this->setTitle($sender->getName());

?><div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
            <h1 class="h3">Sender #<?= $sender->getId(); ?></h1>
            <div class="btn-toolbar float-right">
                <?= Link::widget()
                    ->label(Icon::widget()->name('delete')->options(['class' => 'mr-1'])->render() . ' Delete')
                    ->method('delete')
                    ->href($urlGenerator->generate('/sender/sender/delete', ['id' => $sender->getId()]))
                    ->confirm('Are you sure?')
                    ->options([
                        'class' => 'btn btn-sm btn-danger mx-sm-1 mb-2',
                    ])
                    ->encode(false);
                ?>
                <a class="btn btn-sm btn-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/sender/sender/edit', ['id' => $sender->getId()]); ?>">
                    <?= Icon::widget()->name('pencil')->options(['class' => 'mr-1']); ?>
                    Update
                </a>
                <b-dropdown right size="sm" variant="secondary" class="mb-2">
                    <template v-slot:button-content>
                        <?= Icon::widget()->name('settings'); ?>
                    </template>
                    <?= ActivityLogLink::widget()
                        ->tag('b-dropdown-item')
                        ->label('Activity log')
                        ->entity($sender); ?>
                </b-dropdown>
                <div class="btn-toolbar float-right">
                    <a class="btn btn-sm btn-outline-secondary mx-sm-1 mb-2" href="<?= $urlGenerator->generate('/sender/sender/index'); ?>">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mb-2"></div>
<div class="row">
    <div class="col-12 grid-margin">
        <?= DetailView::widget()
            ->data($sender)
            ->options([
                'class' => 'table detail-view',
            ])
            ->emptyText('(not set)')
            ->emptyTextOptions([
                'class' => 'text-muted',
            ])
            ->attributes([
                [
                    'label' => 'Name',
                    'value' => function (Sender $data, $index) {
                        return $data->getName();
                    },
                ],
            ]);
        ?>
    </div>
</div>
