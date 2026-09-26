<?php
/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-10-05 11:47:03
 * @Last Modified by: 
 * @Last Modified time: 
 */
namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use app\modules\master\components\traits\IdentitasSosialPendidikanTrait;
use app\modules\master\components\traits\IdentitasSosialPendidikanKualifikasiTrait;
use app\modules\master\components\traits\IdentitasSosialPekerjaanTrait;
use app\modules\master\components\traits\IdentitasSosialSukuTrait;

class IdentitasSosialController extends DocoController
{
    protected $_title = "Master Identitas Sosial";
    protected $_module = 'master/identitas-sosial/';
    protected $_restMaster;

    protected $allowAction = ['*'];

    use IdentitasSosialPendidikanTrait;
    use IdentitasSosialPendidikanKualifikasiTrait;
    use IdentitasSosialPekerjaanTrait;
    use IdentitasSosialSukuTrait;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions()
    {
        return [
            'get-data-pendidikan' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'identitas-sosial/get-pendidikan',
                'module' => $this->_module,
                'parseMethod' => 'getDataPendidikan'
            ],
            'get-data-pendidikan-kualifikasi' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'identitas-sosial/get-pendidikan-kualifikasi',
                'module' => $this->_module,
                'parseMethod' => 'getDataPendidikanKualifikasi'
            ],
            'get-data-pekerjaan' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'identitas-sosial/get-pekerjaan',
                'module' => $this->_module,
                'parseMethod' => 'getDataPekerjaan'
            ],
            'get-data-suku' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'identitas-sosial/get-suku',
                'module' => $this->_module,
                'parseMethod' => 'getDataSuku'
            ]
        ];
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
        $dataDropdown = [1 => 'Aktif', 0 => 'Tidak Aktif'];
        return $this->render('index', get_defined_vars());
    }
}
