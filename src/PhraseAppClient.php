<?php
namespace nediam\PhraseApp;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\ResultInterface;

/**
 * @author  nediam
 */
class PhraseAppClient
{
    /**
     * @var GuzzleClient
     */
    private $guzzleClient;
    /**
     * @var string
     */
    private $token;

    /**
     * PhraseAppApiClient constructor.
     *
     * @param string      $token
     * @param string|null $serviceDescriptionPath
     *
     * @throws \Exception
     */
    public function __construct($token, $serviceDescriptionPath = null)
    {
        $serviceDescriptionPath = $serviceDescriptionPath ?: __DIR__ . '/../PhraseAppDescription.json';

        if (false === file_exists($serviceDescriptionPath)) {
            throw new \Exception('PhraseApp service descriptio file does not exists.');
        }

        $serviceDescription = json_decode(file_get_contents($serviceDescriptionPath), true);

        $client             = new Client();
        $description        = new Description($serviceDescription);
        $this->guzzleClient = new GuzzleClient($client, $description);

        $this->token = $token;
    }

    /**
     * @param string $name
     * @param array  $args
     *
     * @return ResultInterface|mixed|null
     */
    public function request($name, array $args = [])
    {
        $args = array_merge($this->getDefaultArgs(), $args);

        $command = $this->guzzleClient->getCommand($name, $args);

        $response = $this->guzzleClient->execute($command);

        return $response;
    }

    public function getDefaultArgs()
    {
        return [
            'token' => sprintf('token %s', $this->token),
        ];
    }
}
