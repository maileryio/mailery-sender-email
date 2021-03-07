<?php

declare(strict_types=1);

namespace Mailery\Sender\Email\Mapper;

use Mailery\Activity\Log\Mapper\LoggableMapper;
use Mailery\Sender\Email\Module;

/**
 * @Cycle\Annotated\Annotation\Table(
 *      columns = {
 *          "created_at": @Cycle\Annotated\Annotation\Column(type = "datetime"),
 *          "updated_at": @Cycle\Annotated\Annotation\Column(type = "datetime")
 *      }
 * )
 */
final class DefaultMapper extends LoggableMapper
{
    /**
     * {@inheritdoc}
     */
    protected function getModule(): string
    {
        return Module::NAME;
    }
}
