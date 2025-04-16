<?php
declare(strict_types = 1);

namespace Assertis\Util\Monolog;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;
use Monolog\Level;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ConsoleFormatter extends LineFormatter
{
    /**
     * Applies console-style emphasis using tags.
     *
     * @param string $message
     * @param string $startTag
     * @param string $endTag
     * @return string
     */
    private function applyEmphasis(string $message, string $startTag, string $endTag): string
    {
        return str_replace(['[', ']'], [$endTag, $startTag], $message);
    }

    /**
     * {@inheritdoc}
     */
    public function format(LogRecord $record): string
    {
        $startTag = '';
        $endTag = '';

        if ($record->level->value >= Level::Error->value) {
            $startTag = '<error>';
            $endTag = '</error>';
        } elseif ($record->level->value >= Level::Notice->value) {
            $startTag = '<comment>';
            $endTag = '</comment>';
        } elseif ($record->level->value >= Level::Info->value) {
            $startTag = '<info>';
            $endTag = '</info>';
        }

        // Clone with modified message
        $modifiedRecord = $record->with(message: $this->applyEmphasis($record->message, $startTag, $endTag));

        return parent::format($modifiedRecord);
    }
}
