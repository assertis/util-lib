<?php
declare(strict_types=1);

namespace Assertis\Util;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class PerRequestHandler extends StreamHandler
{

    protected $filename;
    protected $maxFiles;
    protected $mustRotate;
    protected $nextRotation;
    protected $filenameFormat;
    protected $dateFormat;

    /**
     * @param string $filename
     * @param int $level The minimum logging level at which this handler will be triggered
     * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
     * @param int|null $filePermission Optional file permissions (default (0644) are only for owner read/write)
     * @param bool $useLocking Try to lock log file before doing any writes
     * @throws Exception
     */
    public function __construct(
        string $filename,
        int $level = Logger::DEBUG,
        bool $bubble = true,
        int $filePermission = null,
        bool $useLocking = false
    ) {
        $this->filename = $filename;
        $this->filenameFormat = '{filename}_{date}_{key}';
        $this->dateFormat = 'Y-m-d_H-i-s';

        parent::__construct($this->getTimedAndKeyedFilename(), $level, $bubble, $filePermission, $useLocking);
    }

    /**
     * @return mixed|string
     */
    protected function getTimedAndKeyedFilename()
    {
        $fileInfo = pathinfo($this->filename);

        $key = sprintf("%08x", abs(crc32(
            $_SERVER['REMOTE_ADDR'] . $_SERVER['REQUEST_TIME'] . $_SERVER['REMOTE_PORT']
        )));

        $timedFilename = str_replace(
            ['{filename}', '{date}', '{key}'],
            [$fileInfo['filename'], date($this->dateFormat), $key],
            $fileInfo['dirname'] . '/' . $this->filenameFormat
        );

        if (!empty($fileInfo['extension'])) {
            $timedFilename .= '.' . $fileInfo['extension'];
        }

        return $timedFilename;
    }
}
