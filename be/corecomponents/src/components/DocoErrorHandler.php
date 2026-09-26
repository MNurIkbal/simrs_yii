<?php

/**
 * Error Handler Class
 * @author : Tsani Nashrullah (tsani@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\components;

use yii\web\Response;
use Yii;
use yii\web\UnauthorizedHttpException;
class DocoErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * Method log exception
     * 
     * @return Void
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function logException($exception)
    {
        // get method
        $request =  Yii::$app->request;
        $method = 'GET';
        if ($request->isPost) {
            $method = 'POST';
        } else if ($request->isPut) {
            $method = 'PUT';
        }
        Yii::error(
            'Message : ' . $exception->getMessage() . '--||--Line : ' . $exception->getLine() . '--||--File : ' . $exception->getFile() . '--||--API URL : ' . Yii::$app->request->getPathInfo() . '--||--Method : ' . $method . '--||--Payload : ' . json_encode(Yii::$app->request->post()),
            'server-error'
        );
    }

    /**
     * Override method handling error
     * 
     * @return Void
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function handleException($exception)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->statusCode = 500;
        $this->logException($exception);
        if ($this->discardExistingOutput) {
            $this->clearOutput();
        }

        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }

        if (!$exception instanceOf UnauthorizedHttpException) {
            $response->setStatusCode(500);
            $response->data = [
                'metadata' => [
                    'message' => ($exception->getMessage() !== null) ? $exception->getMessage() : 'Terjadi kesalahan pada server.' ,
                    'status' => 500          
                ],
                'response' => (object) null
            ];
        } else {
            $response->setStatusCode(401);
            $response->data = [
                'metadata' => [
                    'message' => 'Unauthorized!',
                    'status' => 401
                ],
                'response' => [
                    'message' => $exception->getMessage(),
                    'status' => 401
                ]
            ];
        }


        $response->send();
    }
}
