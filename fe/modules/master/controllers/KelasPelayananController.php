<?php
// Author : Arief Saputra

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KelasPelayananForm;
use app\modules\master\models\KelasRuanganForm;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class KelasPelayananRuanganController extends DocoController
{
    protected $_title = 'Master Kelas Pelayanan Ruangan';
    protected $_module = 'master/kelas-pelayanan/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $status = $this->_status;
        $title = Yii::t('fe', $this->_title);
        return $this->render('index', get_defined_vars());
    }
}