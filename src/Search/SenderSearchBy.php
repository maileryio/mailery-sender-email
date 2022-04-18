<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Search;

use Mailery\Widget\Search\Model\SearchBy;

class SenderSearchBy extends SearchBy
{
    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return [
            self::getOperator(),
            [
                ['like', 'name', '%' . $this->getSearchPhrase() . '%'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getOperator(): string
    {
        return 'or';
    }
}
