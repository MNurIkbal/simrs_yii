<?php
//Author: Ardi Pratama

// Namespace
namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;


class SyncTestController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        // Parent
        $verbs = parent::verbs();

        // Return
        return $verbs;
    }

    // Acions
    public function actions()
    {
        // Parent
        $actions = parent::actions();
        // Return
        return $actions;
    }

	public function actionConnection($services = 'sinkronisasi')
	{
        try{
            $restSync = Yii::$app->docoRest->{$services};
            $posts = [];
            $request = $restSync->post('test-sinkron/alpha', [
                'form_params'=>$posts
            ]);
            $response = json_decode($request->getBody(),true);
    		return $response['response'];
        }catch(\Exception $e){
            return 'Connection Failed';
        }
	}
}