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

    const STATUS_ENDPOINT = 'status';
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
    private $services;
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
     * @param array $services
     * @param array $settings
     * @param array $config
     */
    public function __construct(
        string $status,
        string $environment,
        string $name,
        int $apiVersion,
        array $mysql = [],
        array $services = [],
        array $settings = [],
        array $config = []
    )
    {
        $this->status = $status;
        $this->environment = $environment;
        $this->name = $name;
        $this->apiVersion = $apiVersion;
        $this->mysql = $mysql;
        $this->services = $services;
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
                'services' => $this->services,
                'config' => $this->config
            ],
        ];
    }

    /**
     * @param array of Client objects $servicesClients
     * @param $status
     * @param array|null $whoAsks
     * @param array|null $headers
     * @return array
     */
    public static function checkServices(
        array $servicesClients,
        &$status,
        ?array $whoAsks = [],
        ?array $headers = [],
        ?int $recursive = 1
    ): array
    {
        $servicesStatus = [];
        foreach ($servicesClients as $serviceName => $serviceClient) {
            $serviceName = $serviceClient->getName();
            if (!empty($servicesStatus[$serviceName]) ||
                in_array($serviceName, $whoAsks)) {
                $servicesStatus[$serviceName] = !empty($serviceStatus[$serviceName]) ? $serviceStatus[$serviceName] : StatusEnum::SUCCESS;
                continue;
            }
            $serviceStatusResult = ServiceStatusResponse::getServiceStatus($serviceClient, $whoAsks, $headers, $recursive);
            $data = $serviceStatusResult->getResponseBody();
            if ($data['status'] !== StatusEnum::SUCCESS) {
                $status = StatusEnum::ERROR;
            }
            $servicesStatus[$serviceName] = $data['status'];
            foreach ($data['data']['services'] as $key => $serviceStatus) {
                $servicesStatus[$key] = $serviceStatus;
                if ($serviceStatus !== StatusEnum::SUCCESS) {
                    $status = StatusEnum::ERROR;
                }
            }
        }
        return $servicesStatus;
    }

    /**
     * @param ClientInterface $client
     * @param string $url
     * @param string|null $serviceName
     * @return ServiceStatusResponse
     * @throws \Exception
     */
    public static function getServiceStatus(
        ClientInterface $client,
        ?array $serviceName = [],
        ?array $headers = [],
        ?int $recursive = 1
    ): ServiceStatusResponse
    {
        $query = [
            'recursive' => $recursive
        ];
        if (!empty($serviceName[0])) {
            $query['questioning'] = $serviceName[0];
        }
        $response = $client->send(new Request(self::STATUS_ENDPOINT, '', $query, Request::GET, $headers));
        $data = json_decode($response->getBody()->getContents(), true);
        if (!is_array($data) || !isset($data['data'])) {
            return new ServiceStatusResponse(
                StatusEnum::ERROR,
                '',
                '',
                1
            );
        }
        $dataDetails = $data['data'];
        return new ServiceStatusResponse(
            $data['status'],
            $dataDetails['environment'],
            $dataDetails['name'],
            $dataDetails['apiVersion'] ?? 1,
            $dataDetails['mysql'] ?? [],
            $dataDetails['services'] ?? [],
            $dataDetails['settings'] ?? [],
            $dataDetails['config'] ?? []
        );

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

        $data = json_decode($response->getBody()->getContents(), true);

        return is_array($data) && in_array($data['status'], ['ok', 'success']);
    }

}
