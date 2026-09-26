<?php

namespace app\components;

use yii\base\UserException;

class HandlerExceptions extends UserException
{
    const PAGE_NOT_FOUND = 'Halaman Tidak ditemukan';
    const PAGE_ERROR = 'Terjadi Kesalahan Pada Server';
    /**
     * @var int HTTP status code, such as 403, 404, 500, etc.
     */
    public $statusCode;

    public $messagesError;

    /**
     * Constructor.
     * @param int $status HTTP status code, such as 404, 500, etc.
     * @param string $message error message
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($status, $message = null, \Exception $previous = null)
    {
        $this->statusCode = $status;
        $this->messagesError = self::PAGE_ERROR;
        if ($previous instanceOf \GuzzleHttp\Exception\ClientException 
                || $previous instanceOf \GuzzleHttp\Exception\ServerException) {
            if ($previous->hasResponse()) {
                $request = $previous->getRequest();
                $parse = [
                    'method' => $request->getMethod(),
                    'uri' => [
                        'path' => (string) $request->getUri(),
                        'query' => $request->getUri()->getQuery(),
                        'fragment' => $request->getUri()->getFragment(),
                    ],
                ];

                $response = $previous->getResponse();
                $body = json_decode($response->getBody(),true);
                $parse['response'] = [
                    'code' => $response->getStatusCode(),
                    'body' => $body,
                ];

                $message = json_encode($parse);
                $previous = null;
            }
        } else if ($previous instanceOf \yii\base\InvalidRouteException 
            || $previous instanceOf \yii\web\BadRequestHttpException) {
            $this->messagesError = self::PAGE_NOT_FOUND;
            $status = 404;
        } else {
            $status = !empty($previous->statusCode) ? $previous->statusCode : $status;
        }
        parent::__construct($message, $status, $previous);
    }

    public function isAjax()
    {
        $request = \Yii::$app->request;
        return $request->isAjax ?: false;
    }

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Error';
    }
}
