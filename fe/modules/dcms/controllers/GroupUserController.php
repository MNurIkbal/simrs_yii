<?php

namespace Doco\dcms\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoHelpers;
use Doco\dcms\models\KelompokMenuForm;


class GroupUserController extends DocoController
{
    
    protected $_title = "Group User";
    protected $_module = '/dcms/group-menu';
    protected $restMaster;

    public function init()
    {
        parent::init();
        $this->restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        
        $model = new KelompokMenuForm;
        $status = $this->_status;
        $title = $this->_title;
        return $this->render('index',get_defined_vars());
    }
}