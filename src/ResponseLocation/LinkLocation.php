<?php
/**
 * @author  nediam
 */

namespace nediam\PhraseApp\ResponseLocation;

use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Parameter;
use GuzzleHttp\Command\Guzzle\ResponseLocation\AbstractLocation;
use GuzzleHttp\Message\ResponseInterface;

class LinkLocation extends AbstractLocation
{
    public function visit(CommandInterface $command, ResponseInterface $response, Parameter $param, &$result, array $context = [])
    {
        // Retrieving a single header by name
        $name   = $param->getName();
        $header = $response->getHeader($param->getWireName());
        if ($header) {
            $result[$name] = $param->filter($this->parseLink($header));
        }

    }

    /**
     * @param string $header
     *
     * @return array
     */
    protected function parseLink($header)
    {
        $trimmed = "\"'  \n\t\r";
        $result  = [];
        foreach (preg_split('/,(?=([^"]*"[^"]*")*[^"]*$)/', $header) as $links) {
            $links = preg_split('/;(?=([^"]*"[^"]*")*[^"]*$)/', trim($links));

            $part = [];
            foreach ($links as $link) {
                if (preg_match_all('/<[^>]+>|[^=]+/', $link, $matches)) {
                    $matches = $matches[0];
                    if (array_key_exists(1, $matches)) {
                        $part[trim($matches[0], $trimmed)] = trim($matches[1], $trimmed);
                    } else {
                        $part['url'] = trim(trim($matches[0], $trimmed), '<>');
                    }
                }
            }
            $result[$part['rel']] = $part['url'];
        }

        return $result;
    }
}