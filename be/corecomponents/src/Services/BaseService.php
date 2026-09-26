<?php

namespace Doco\Services;

use Yii;

class BaseService
{
    protected $service;

    /**
     * This function to exec service
     * 
     * @param Array $optionGuzzle => two keys => url, method
     * @param Array $payloadData payload of request, two keys => form_params, query
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function execService($optionGuzzle, $payloadData = [])
    {
        $result = [];
        $method = !isset($optionGuzzle['method']) ? 'get' : strtolower($optionGuzzle['method']);
        if (isset($optionGuzzle['payload'])) {
            $payload = $optionGuzzle['payload'];
        } else {
            if ($method == 'get' || $method == 'delete') {
                $payload = [
                    'query' => isset($payloadData['query']) ? $payloadData['query'] : []
                ];
            } else {
                $payload = [
                    'query' => isset($payloadData['query']) ? $payloadData['query'] : [],
                    'form_params' => isset($payloadData['form_params']) ? json_encode($payloadData['form_params']) : [],
                ];
            }
        }
        $successCallback = isset($optionGuzzle['success']) ? $optionGuzzle['success'] : null;
        $failedCallback = isset($optionGuzzle['failed']) ? $optionGuzzle['failed'] : null;
        if (isset($optionGuzzle['save_to']) && !empty($optionGuzzle['save_to'])) {
            $payload['save_to'] = $optionGuzzle['save_to'];
        }
        $isReturnResponse = isset($optionGuzzle['returnResponse']) && $optionGuzzle['returnResponse'];
        if ($isReturnResponse) {
            $result = [];
        }
        try {
            if ($method == 'post') {
                $content_json = ['Content-Type' => 'application/json'];
                $payload['headers'] = (isset($payload['headers'])) ? array_merge($payload['headers'], $content_json) : $content_json;
                if (!empty($payload['query'])) {
                    $payload['query'] = json_encode($payload['query']);
                } else {
                    unset($payload['query']);
                }
                $payload['body'] = json_encode($payload['form_params']);
                unset($payload['form_params']);
            }
            $guzzleRequest = $this->service->{$method}($optionGuzzle['url'], $payload);
            $response = json_decode($guzzleRequest->getBody(), true);
            $result = isset($response['response']) ? $response['response'] : [];
            if (!empty($successCallback)) {
                call_user_func($successCallback, $result, $guzzleRequest->getStatusCode());
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $httpStatusCode = $e->getResponse()->getStatusCode();
            $response = json_decode($e->getResponse()->getBody(), true);
            $result = isset($response['response']) ? $response['response'] : [];
            if (!empty($failedCallback)) {
                $resultCallback = call_user_func($failedCallback, $result, $httpStatusCode);
                if (!empty($resultCallback)) {
                    $result = $resultCallback;
                }
            }
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            Yii::error([
                "Message-Error" => $e->getMessage()
            ]);
            if (!empty($failedCallback)) {
                $resultCallback = call_user_func($failedCallback, [], 500);
                if (!empty($resultCallback)) {
                    $result = $resultCallback;
                }
            }
        } catch (RequestException $e) {
            Yii::error([
                "Message-Error" => $e->getMessage()
            ]);
            if (!empty($failedCallback)) {
                $resultCallback = call_user_func($failedCallback, [], 500);
                if (!empty($resultCallback)) {
                    $result = $resultCallback;
                }
            }
        } catch (Exception $e) {
            Yii::error([
                "Message-Error" => $e->getMessage()
            ]);
            if (!empty($failedCallback)) {
                $resultCallback = call_user_func($failedCallback, [], 500);
                if (!empty($resultCallback)) {
                    $result = $resultCallback;
                }
            }
        }
        return $result;
    }

    /**
     * This function to get data
     * 
     * @param String URL
     * @param Array option
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function get($url, $option)
    {
        if (!empty($this->service)) {
            return $this->execService(array_merge([
                'url' => $url,
                'payload' => [
                    'query' => isset($option['query']) ? $option['query'] : []
                ]
            ], $option));
        } else {
            return [];
        }
    }

    /**
     * This function to get data
     * 
     * @param String URL
     * @param Array option
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function post($url, $option)
    {
        if (!empty($this->service)) {
            return $this->execService(array_merge([
                'url' => $url,
                'method' => 'post',
                'payload' => [
                    'query' => isset($option['query']) ? $option['query'] : [],
                    'form_params' => isset($option['form_params']) ? $option['form_params'] : [],
                    'headers' => isset($option['headers']) ? $option['headers'] : [],
                ]
            ], $option));
        } else {
            return [];
        }
    }
}
