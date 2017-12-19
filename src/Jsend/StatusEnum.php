<?php
declare(strict_types=1);

namespace Assertis\Util\Jsend;

use Assertis\Util\Enum;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class StatusEnum extends Enum
{
    public const SUCCESS = 'success';
    public const FAIL = 'fail';
    public const ERROR = 'error';
}
