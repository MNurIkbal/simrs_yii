<?php

namespace app\modules\integrator\components;

use Yii;
use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;
use Doco\models\PublicUser;

class LisAuth extends AuthMethod
{
	public function init()
    {
        parent::init();
    }

    public function beforeAction($action)
    {
        $response = $this->response ?: Yii::$app->getResponse();
        try {
            $identity = $this->authenticate(
                $this->user ?: Yii::$app->getUser(),
                $this->request ?: Yii::$app->getRequest(),
                $response
            );
        } catch (UnauthorizedHttpException $e) {
            if ($this->isOptional($action)) {
                return true;
            }

            throw $e;
        }

        if ($identity !== null || $this->isOptional($action)) {
            return true;
        }

        $this->challenge($response);
        $this->handleFailure($response);

        return false;
    }

    public function authenticate($user, $request, $response)
    {
        $uid = $request->getHeaders()->get('x-uid');
        $signate_key = $request->getHeaders()->get('x-signature');
        $identity = null;

        if(isset($uid) && isset($signate_key)){
        	$identity = PublicUser::findIdentityWithKey($uid,$signate_key, get_class($this));
        	if(!is_null($identity) && $this->validSign($uid,$identity->getAttribute('api_key'),$signate_key)){
        		return $identity;
	        }
        }

        return null;
    }

    public function handleFailure($response)
    {    	
        throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
    }

    protected function validSign($uid,$key,$signate_key)
    {
        $signature = hash_hmac('sha256', $uid.'+'.$key,true);
        $encodeSignature = base64_encode($signature);

        if($signate_key == $encodeSignature){
        	return true;
        }
        return null;
    }
}