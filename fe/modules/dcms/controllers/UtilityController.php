<?php

namespace Doco\dcms\controllers;

use app\components\DocoController;

use Yii;

class UtilityController extends DocoController
{
    protected $_module = '/dcms/utility';
    protected $_restDcms;

    /**
     * This function will handle init of class
     * 
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function init()
    {
        parent::init();
    }

    public function actionFlushCache()
    {
        if (!in_array('sysadmin', Yii::$app->docoVars->user('roles'))) {
            return $this->responseJson(401, 'You\'re not authorized to do this action!');
        } else {
            $flushStatus = \Yii::$app->cache->flush();
            return $this->responseJson(200, 'Cache berhasil dibersihkan', compact('flushStatus'));
        }
    }
}
