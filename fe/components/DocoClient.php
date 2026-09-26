<?php

namespace app\components;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;

/**
 * 
 */
class DocoClient extends Client
{
	public function __call($method, $args)
	{
		try {
			return parent::__call($method, $args);
		} catch (ClientException $e) {
    		throw new ClientException(
    			$this->parseMessage($e),
    			$e->getRequest(),
    			$e->getResponse(),
    			$e->getPrevious(),
    			$e->getHandlerContext()
    		);
		} catch (ServerException $e) {
    		throw new ServerException(
    			$this->parseMessage($e),
    			$e->getRequest(),
    			$e->getResponse(),
    			$e->getPrevious(),
    			$e->getHandlerContext()
    		);
		} catch (RequestException $e) {
			throw new DocoRestException($this->parseMessage($e), 0, $e);
		}
	}

	private function parseMessage(RequestException $e)
	{
		$message = $e->getMessage();
		if ($e->hasResponse()) {
			$response = $e->getResponse();
            $body = $response->getBody();
            $parsed = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
            	if (isset($parsed['response']['message'])) {
            		$message = $parsed['response']['message'];
            	} elseif (isset($parsed['meta']['message'])) {
            		$message = $parsed['meta']['message'];
            	} elseif (isset($parsed['metadata']['message'])) {
            		$message = $parsed['metadata']['message'];
            	} elseif (isset($parsed['message'])) {
            		$message = $parsed['message'];
            	}
            }
		}

		return DocoHelpers::hideMessage($message);
	}
}