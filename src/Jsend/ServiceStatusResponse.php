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
    ) {
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

    public static function checkServices(
        array $servicesClients,
        &$status,
        ?array $whoAsks = []
    ): array {
        $servicesStatus = [];
        foreach ($servicesClients as $serviceName => $serviceClient) {
            if (!empty($servicesStatus[$serviceName]) ||
                is_null($serviceClient) ||
                in_array($serviceName, $whoAsks)) {
                $servicesStatus[$serviceName] = StatusEnum::SUCCESS;
                continue;
            }
            $serviceStatusResult = ServiceStatusResponse::getServiceStatus($serviceClient, 'status', $whoAsks);
            $data = $serviceStatusResult->getResponseBody();
            if ($data['status'] !== StatusEnum::SUCCESS) {
                $status = StatusEnum::ERROR;
            }
            $servicesStatus[$serviceName] = $data['status'];
            foreach ($data['data']['services'] as $key => $status) {
                $servicesStatus[$key] = $status;
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
        string $url = 'status',
        ?array $serviceName = []
    ): ServiceStatusResponse {
        $query = [];
        if(!empty($serviceName[0])) {
            $query['questioning'] = $serviceName[0];
        }
        $response = $client->send(new Request($url, '', $query, Request::GET));
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
