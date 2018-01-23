<?php
declare(strict_types=1);

namespace Assertis\Util\Jsend;

use JsonSerializable;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ResponseData implements JsonSerializable
{
    /**
     * @var StatusEnum
     */
    private $status;
    /**
     * @var array|JsonSerializable|null
     */
    private $data;
    /**
     * @var string|null
     */
    private $message;
    /**
     * @var string|null
     */
    private $code;
    /**
     * @var array|null
     */
    private $links;
    /**
     * @var array
     */
    private $debug;

    /**
     * @param StatusEnum $status
     * @param array|JsonSerializable|null $data
     * @param array|null $links
     * @param string|null $message
     * @param string|null $code
     * @param array|null $debug
     */
    public function __construct(
        StatusEnum $status,
        $data = null,
        array $links = null,
        string $message = null,
        string $code = null,
        array $debug = null
    ) {
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
        $this->code = $code;
        $this->links = $links;
        $this->debug = $debug;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'status'  => $this->status->getValue(),
            'message' => $this->message,
            'code'    => $this->code,
            'data'    => $this->data,
            'links'   => $this->links,
            'debug'   => $this->debug
        ], function ($item) {
            return $item !== null;
        });
    }
}
