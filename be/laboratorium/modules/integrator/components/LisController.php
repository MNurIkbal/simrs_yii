<?php

namespace app\modules\integrator\components;

use Yii;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\web\Response;

// use Doco\components\DocoPublicJwtAuth;
use Doco\components\DocoPublicAccessRule;
use Doco\components\DocoPublicRateLimiter;

use app\modules\integrator\components\LisAuth;
use Doco\Traits\ControllerHelperTrait;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstansId;
use Doco\components\DocoLookupType;

class LisController extends ActiveController
{

    use ControllerHelperTrait;

    public $helper;
    public $constans;
    public $lookup_type;
    // public $serializer = [
    //     'class' => '\Doco\components\DocoSerializer',
    //     'collectionEnvelope' => 'data',
    // ];

    public function init()
    {
        parent::init();
        $this->helper = new DocoHelpers;
        $this->constans = new DocoConstansId;
        $this->lookup_type = new DocoLookupType;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => LisAuth::className()
        ];

        $behaviors['access'] = [
            'class' => DocoPublicAccessRule::className()
        ];
        $behaviors['rateLimiter'] = [
        	'class' => DocoPublicRateLimiter::className()
        ];
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; // Force XML Header to Json Response
        $behaviors['contentNegotiator']['formats']['application/xml'] = Response::FORMAT_JSON; // Force XML Header to Json Response
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }
}