<?php

namespace Doco\components;

use yii\rest\Serializer;
use Yii;

class DocoSerializer extends Serializer
{
    protected static $phrase = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Expired',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required'
    );
    
    /**
     * Serializes the given data into a format that can be easily turned into other formats.
     * This method mainly converts the objects of recognized types into array representation.
     * It will not do conversion for unknown object types or non-object data.
     * The default implementation will handle [[Model]] and [[DataProviderInterface]].
     * You may override this method to support more object types.
     * @param mixed $data the data to be serialized.
     * @return mixed the converted data.
     */
    public function serialize($data) 
    {
        $is_mobile = Yii::$app->jwt->is_mobile;
        $data = parent::serialize($data);
        $statusCode = $this->response->getStatusCode();
        if (isset($data['status'])) {
            $statusCode = $data['status'];
            unset($data['status']);
        }
        if ($is_mobile) {
            $response["status"] = $statusCode;
            $response["data"] = $data;
            $response["message"] = DocoSerializer::$phrase[$statusCode];
            if ($statusCode == 200) {
                $response["error"] = null;
            } else {
                $response["data"] = null;
                $response["error"] = isset($data['message']) ? $data['message'] : $data;
            }
        } else {
            $response = [
                "metadata" => [
                    "status" => $statusCode,
                    "message" => DocoSerializer::$phrase[$statusCode]
                ],
                "response" => null
            ];
            $response["response"] = $data;
        }

        return $response;
    }
}
