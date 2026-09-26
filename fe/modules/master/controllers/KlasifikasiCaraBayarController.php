<?php
// Author : Naufal Ziyad L

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

class KlasifikasiCaraBayarController extends DocoController
{
    protected $_title = "Master :: Klasifikasi Cara Bayar";
    protected $_module = 'master/klasifikasi-cara-bayar';
    protected $_restMaster;
    protected $_statusActive;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_statusActive = [
            1 => 'Aktif',
            0 => 'Tidak Aktif'];
    }

    public function actions()
    {
        return [
            'get-data-cara-bayar' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'klasifikasi-cara-bayar/get-cara-bayar',
                'module' => $this->_module,
                'parseMethod' => 'getDataCaraBayar'
            ],
            'get-data-penjamin' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'klasifikasi-cara-bayar/get-penjamin',
                'module' => $this->_module,
                'parseMethod' => 'getDataPenjamin'
            ],
            'get-data-penjamin-diskon' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'klasifikasi-cara-bayar/get-penjamin-diskon',
                'module' => $this->_module,
                'parseMethod' => 'getDataPenjaminDiskon'
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
        $status = $this->_statusActive;
        $statusOnline = $this->_options['confirm'];
        return $this->render('index', get_defined_vars());
    }

    public function actionPageCaraBayar() {
        $status = $this->_statusActive;
        $MetodeBayarRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=metode_bayar');
        $body = json_decode($MetodeBayarRequest->getBody(),TRUE);
        $metode_bayar = $body['response'];

        return $this->renderPartial('cara-bayar/index', get_defined_vars());
    }

    public function actionPagePenjamin() {
        $status = $this->_statusActive;
        $options = $this->_options['confirm'];
        $carabayarRequest = $this->_restMaster->get('cara-bayar/list-cara-bayar');
        $body = json_decode($carabayarRequest->getBody(),TRUE);
        $carabayar = $body['response'];

        $marginhargaRequest = $this->_restMaster->get('allow/get-list-group-margin?keyValue=groupmargin_nama');
        $bodyMargin = json_decode($marginhargaRequest->getBody(), TRUE);
        $margingroup = $bodyMargin['response'];

        return $this->renderPartial('penjamin/index', get_defined_vars());
    }

    public function actionPagePenjaminDiskon() {
        $status = $this->_statusActive;
        $carabayarRequest = $this->_restMaster->get('cara-bayar/list-cara-bayar');
        $body = json_decode($carabayarRequest->getBody(),TRUE);
        $carabayar = $body['response'];

        return $this->renderPartial('penjamin-diskon/index', get_defined_vars());
    }

    public function getDataCaraBayar($dataTable)
    {
        $data = [];
        $no = 0;
        foreach ($dataTable as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['carabayar_id']);
            $value['primary'] = $primaryKey;
            unset($value['carabayar_id']);

            $value['is_subsidiasuransi'] = ($value['is_subsidiasuransi']) ? 'Ya' : 'Tidak' ;
            $value['is_subsidipemerintah'] = ($value['is_subsidipemerintah']) ? 'Ya' : 'Tidak' ;
            $value['is_subsidirs'] = ($value['is_subsidirs']) ? 'Ya' : 'Tidak' ;
            // $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
            $value['is_active'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
            $value['is_online'] = DocoHelpers::switchStatus($value['is_online'], $primaryKey,'change-status','Ya','Tidak');
            $value['rowNum'] = $no;
            $data[$key] = $value;
        }
        return $data;
    }

    public function getDataPenjamin($dataTable)
    {
        $no = 0;
        $data = [];
        foreach ($dataTable as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['penjamin_id']);
            $value['primary'] = $primaryKey;
            unset($value['penjamin_id']);

            $value['is_active'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif';
            $value['is_online'] = DocoHelpers::switchStatus($value['is_online'], $primaryKey, 'change-tampilan','Ya','Tidak');
            $value['groupmargin_nama'] = empty($value['groupmargin_nama']) ? "-" : $value['groupmargin_nama'];
            $value['rowNum'] = $no;
            $data[$key] = $value;
        }
        return $data;
    }

    public function getDataPenjaminDiskon($dataTable)
    {
        $no = 0;
        $data = [];
        foreach ($dataTable as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['penjamindiskon_id']);
            $value['primary'] = $primaryKey;
            unset($value['penjamin_id']);
            unset($value['penjamindiskon_id']);

            $value['is_active'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif';
            $value['rowNum'] = $no;
            $data[$key] = $value;
        }
        return $data;
    }
}
