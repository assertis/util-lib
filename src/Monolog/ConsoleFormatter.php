<?php
declare(strict_types = 1);

namespace Assertis\Util\Monolog;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ConsoleFormatter extends LineFormatter
{
    /**
     * @param array $record
     * @return array
     */
    private function applyEmphasis(array $record): array
    {
        $record['message'] = str_replace(['[', ']'], [$record['end_tag'], $record['start_tag']], $record['message']);

        return $record;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {

        if ($record['level'] >= Logger::ERROR) {
            $record['start_tag'] = '<error>';
            $record['end_tag'] = '</error>';
        } elseif ($record['level'] >= Logger::NOTICE) {
            $record['start_tag'] = '<comment>';
            $record['end_tag'] = '</comment>';
        } elseif ($record['level'] >= Logger::INFO) {
            $record['start_tag'] = '<info>';
            $record['end_tag'] = '</info>';
        } else {
            $record['start_tag'] = '';
            $record['end_tag'] = '';
        }

        $record = $this->applyEmphasis($record);

        return parent::format($record);
    }
}
