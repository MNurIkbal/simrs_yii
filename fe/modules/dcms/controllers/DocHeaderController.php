<?php
namespace Doco\dcms\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoController;

class DocHeaderController extends DocoController
{
    protected $_title = "Kepala Surat";
    protected $_module = 'doc-header/';
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->dcms;
    }

    public function actionIndex()
    {
        
    }

}