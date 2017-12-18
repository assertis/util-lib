<?php
declare(strict_types=1);

namespace Assertis\Util\Monolog;

use Monolog\Formatter\FormatterInterface;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class MultilineFormatter implements FormatterInterface
{

    /**
     * Formats a log record.
     *
     * @param  array $record A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        return trim($record['message']) . "\n\n";
    }

    /**
     * Formats a set of log records.
     *
     * @param  array $records A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        return array_map([$this, 'format'], $records);
    }
}
