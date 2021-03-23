<?php

namespace CircleCloud\DockerAPI;

class Client
{
    /**
     * @var string 节点
     */
    private $endpoint;
    /**
     * @var GuzzleHttp\Client
     */
    private $httpClient;

    private $containers;

    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
        $this->initialize();
    }

    public function __sleep()
    {
        return ['endpoint'];
    }

    public function __wakeup()
    {
        $this->initialize();
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function initialize()
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => $this->endpoint,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
        $this->containers = new Containers($this);
    }

    public function containers()
    {
        return $this->containers;
    }

    public function get($method)
    {
        return $this->request('GET', $method);
    }

    public function getRaw($method)
    {
        return $this->httpClient->get($method)->getBody()->getContents();
    }

    public function post($method, $options = [])
    {
        return $this->request('POST', $method, [
            'body' => json_encode($options),
        ]);
    }

    public function delete($method)
    {
        return $this->request('DELETE', $method);
    }

    public function request($method, $path, $options = [])
    {
        try {
            $result = $this->httpClient
                ->request(
                    $method,
                    $path,
                    \array_merge([
                        'http_errors' => false,
                    ], $options)
                )
            ;
            $status = $result->getStatusCode();
            if ($status > 399) {
                return \forbidden($result->getBody()->getContents());
            }
            if (204 == $status) {
                return $status;
            }

            return json_decode($result->getBody()->getContents());
        } catch (\Throwable $th) {
            return \forbidden($th->getMessage());
        }
    }
}
