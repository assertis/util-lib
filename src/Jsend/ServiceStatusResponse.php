<?php

namespace Assertis\Util\Jsend;

/*
 * @author Lukasz Nowak <lukasz.nowak@assertis.co.uk>
 */
use Assertis\Http\Client\ClientInterface;
use Assertis\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response as Status;

class ServiceStatusResponse
{

    /**
     * @var string
     */
    private $status;
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $apiVersion;
    /**
     * @var array
     */
    private $mysql;
    /**
     * @var array
     */
    private $service;
    /**
     * @var array
     */
    private $settings;
    /**
     * @var array
     */
    private $config;
    /**
     * @var string
     */
    private $environment;

    /**
     * ServiceStatusResponse constructor.
     * @param string $status
     * @param string $environment
     * @param string $name
     * @param int $apiVersion
     * @param array $mysql
     * @param array $service
     * @param array $settings
     * @param array $config
     */
    public function __construct(
        string $status,
        string $environment,
        string $name,
        int $apiVersion,
        array $mysql = [],
        array $service = [],
        array $settings = [],
        array $config = []
    ) {
        $this->status = $status;
        $this->environment = $environment;
        $this->name = $name;
        $this->apiVersion = $apiVersion;
        $this->mysql = $mysql;
        $this->service = $service;
        $this->settings = $settings;
        $this->config = $config;
    }

    public function getResponseStatus(): int
    {
        if (strcmp($this->status, StatusEnum::SUCCESS) === 0) {
            return 200;
        }
        return 503;
    }

    public function getResponseBody(): array
    {
        return [
            'status' => $this->status,
            'data' => [
                'name' => $this->name,
                'environment' => $this->environment,
                'apiVersion' => $this->apiVersion,
                'mysql' => $this->mysql,
                'settings' => $this->settings,
                'service' => $this->service,
                'config' => $this->config
            ],
        ];
    }

    /**
     * @param ClientInterface $client
     * @param string $url
     * @return bool
     */
    public static function isServiceOk(ClientInterface $client, string $url = 'status'): bool
    {
        $response = $client->send(new Request($url, '', [], Request::GET));

        if ($response->getStatusCode() !== Status::HTTP_OK) {
            return false;
        }

        $data = $response->json();

        return is_array($data) && in_array($data['status'], ['ok', 'success']);
    }

}
