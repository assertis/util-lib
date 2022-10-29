<?php
declare(strict_types=1);

namespace Assertis\Util\Jsend;

use DateInterval;
use JsonSerializable;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class Response extends JsonResponse
{
    /**
     * @param ResponseData $data
     * @param int $status
     * @param array $headers
     */
    public function __construct(ResponseData $data, int $status = self::HTTP_OK, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
    }

    /**
     * @param int $seconds
     * @return Response
     */
    public function cacheFor(int $seconds): Response
    {
        $expires = clone $this->getDate();
        $expires->add(DateInterval::createFromDateString($seconds.' seconds'));

        $this->setSharedMaxAge($seconds);
        $this->setMaxAge($seconds);
        $this->setExpires($expires);

        return $this;
    }

    /**
     * Return success response
     *
     * @param array|JsonSerializable|null $data
     * @param int $status
     * @param array $headers
     * @param array $links
     * @return Response
     */
    public static function success($data, int $status = self::HTTP_OK, array $headers = [], array $links = []): Response
    {
        return new self(
            new ResponseData(
                new StatusEnum(StatusEnum::SUCCESS),
                $data,
                $links,
                null,
                null
            ),
            $status,
            $headers
        );
    }

    /**
     * Return fail response
     *
     * @param string $message
     * @param string|null $code
     * @param array|JsonSerializable|null $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function fail(
        string $message = null,
        string $code = null,
        $data = null,
        int $status = self::HTTP_BAD_REQUEST,
        array $headers = []
    ): Response {
        return new self(
            new ResponseData(
                new StatusEnum(StatusEnum::FAIL),
                $data,
                null,
                $message,
                $code
            ),
            $status,
            $headers
        );
    }

    /**
     * Return error response
     *
     * @param string $message
     * @param null|string $code
     * @param array|JsonSerializable|null $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function error(
        string $message,
        string $code = null,
        $data = null,
        int $status = self::HTTP_INTERNAL_SERVER_ERROR,
        array $headers = []
    ): Response {
        return new self(
            new ResponseData(
                new StatusEnum(StatusEnum::ERROR),
                $data,
                null,
                $message,
                $code
            ),
            $status,
            $headers
        );
    }

    /**
     * @param string $uri
     * @param array|JsonSerializable|null $data
     * @param array $headers
     * @return Response
     */
    public static function created(string $uri, $data = null, array $headers = []): Response
    {
        if ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        } elseif ($data === null) {
            $data = [];
        }

        $data = ['uri' => $uri] + $data;
        $headers['Location'] = $uri;

        return self::success($data, self::HTTP_CREATED, $headers);
    }

    /**
     * @param string $message
     * @param array $headers
     * @return Response
     */
    public static function notFound(string $message = null, array $headers = []): Response
    {
        return self::fail($message, null, null, self::HTTP_NOT_FOUND, $headers);
    }
}
