<?php

namespace CircleCloud\DockerAPI;

class Containers
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create($name, $options)
    {
        return $this->client->post("/containers/create?name={$name}", $options);
    }

    public function start($idOrName)
    {
        return $this->client->post("/containers/{$idOrName}/start");
    }

    public function inspect($idOrName)
    {
        return $this->client->get("/containers/{$idOrName}/json");
    }

    public function stop($idOrName, $wait = 10)
    {
        return $this->client->post("/containers/{$idOrName}/stop?t={$wait}");
    }

    public function remove($idOrName, $force = false)
    {
        return $this->client->delete("/containers/{$idOrName}?force=".$force);
    }

    public function update($idOrName, $options)
    {
        return $this->client->post("/containers/{$idOrName}/update", $options);
    }

    public function logs($idOrName, $options = [])
    {
        return $this->client->getRaw("/containers/{$idOrName}/logs?".\http_build_query(array_merge(
            [
                'stdout' => true,
                'stderr' => true,
                'timestamps' => false,
                'tail' => 10,
            ],
            $options
        )));
    }
}
