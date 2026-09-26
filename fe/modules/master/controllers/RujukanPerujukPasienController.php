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

class RujukanPerujukPasienController extends DocoController
{
    protected $_title = "Master :: Rujukan Perujuk Pasien";
    protected $_module = 'master/rujukan-perujuk-pasien';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions()
    {
        return [
            'get-data-asal-rujukan' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'rujukan-perujuk-pasien/get-asal-rujuk',
                'module' => $this->_module,
                'parseMethod' => 'getDataAsalRujukan'
            ],
            'get-data-perujuk' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'rujukan-perujuk-pasien/get-perujuk',
                'module' => $this->_module,
                'parseMethod' => 'getDataPerujuk'
            ],
            'get-data-rujukan-keluar' => [
                'class' => 'app\modules\master\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'rujukan-perujuk-pasien/get-rujukan-keluar',
                'module' => $this->_module,
                'parseMethod' => 'getDataRujukanKeluar'
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
        return $this->render('index', get_defined_vars());
    }

    public function actionPageAsalRujukan() {
        $status = $this->_status;
        return $this->renderPartial('_asal-rujukan', get_defined_vars());
    }

    public function actionPagePerujuk() {
        $status = $this->_status;

        $asalRujukanRequest = $this->_restMaster->get('rujukan-perujuk-pasien/list-asal-rujukan');
        $body = json_decode($asalRujukanRequest->getBody(),TRUE);
        $asal = $body['response'];

        return $this->renderPartial('_perujuk', get_defined_vars());
    }

    public function actionPageRujukanKeluar() {
        $status = $this->_status;

        $asalRujukanRequest = $this->_restMaster->get('rujukan-perujuk-pasien/list-asal-rujukan');
        $body = json_decode($asalRujukanRequest->getBody(),TRUE);
        $asal = $body['response'];
        
        return $this->renderPartial('_rujukan-keluar', get_defined_vars());
    }

    public function getDataAsalRujukan($dataTable)
    {
        $data = [];
        $no = 1;
        foreach ($dataTable as $key => $value) {
            $primaryKey = DocoHelpers::encrypt($value['asalrujukan_id']);
            $value['primary'] = $primaryKey;
            unset($value['asalrujukan_id']);
            $value['aksi']  = Html::button(
                "<i class='fa fa-eye'></i>", [
                    'style' => 'margin-right:5px',
                    'class' => 'btn btn-info btn-xs data-view',
                    'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_asalrujukan',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Lihat')
                ]
            );
            $value['aksi'] .= Html::button( 
                "<i class='fa fa-pencil'></i>", [
                    'style' => 'margin-right:5px ',
                    'class' => 'btn btn-primary btn-xs data-update-asalrujukan',
                    'action' => Url::home().$this->_module.'update?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_asalrujukan',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Ubah'),
                ]
            );
            $value['aksi'] .= Html::a(
                "<i class='fa fa-trash'></i>",'#', [
                    'id' => 'hapus_asalrujukan',
                    'style' => 'margin-right:5px',
                    'class' => 'btn btn-danger btn-xs delete data-delete-asalrujukan',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Hapus'),
                    'action' => Url::home().$this->_module.'delete?id='.$primaryKey
                ]
            );
            // $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey);
            $value['is_active'] = DocoHelpers::isActive($value['is_active']);
            $value['rowNum'] = $no;
            $data[$key] = $value;
            $no++;
        }
        return $data;
    }

    public function getDataPerujuk($dataTable)
    {
        $no = 1;
        $data =[];
        foreach ($dataTable as $key => $value) {
            $primaryKey = DocoHelpers::encrypt($value['perujuk_id']);
            $value['primary'] = $primaryKey;
            unset($value['perujuk_id']);

            $value['aksi']  = Html::button(
                "<i class='fa fa-eye'></i>", [
                    'style' => 'margin-right:5px',
                    'class' => 'btn btn-info btn-xs data-view',
                    'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_perujuk',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Lihat')
                ]
            );
            $value['aksi'] .= Html::button( 
                "<i class='fa fa-pencil'></i>", [
                    'style' => 'margin-right:5px ',
                    'class' => 'btn btn-primary btn-xs data-update-perujuk',
                    'action' => Url::home().$this->_module.'update?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_perujuk',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Ubah'),
                ]
            );
            $value['aksi'] .= Html::a(
                "<i class='fa fa-trash'></i>",'#', [
                    'id' => 'hapus_perujuk',
                    'style' => 'margin-right:5px',
                    'class' => 'btn btn-danger btn-xs delete data-delete-perujuk',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Hapus'),
                    'action' => Url::home().$this->_module.'delete?id='.$primaryKey
                ]
            );
            // $value['is_active'] = DocoHelpers::switchStatus($value['is_active'], $primaryKey, 'change-status-perujuk');
            $value['is_active'] = DocoHelpers::isActive($value['is_active']);
            $value['rowNum'] = $no;
            $data[$key] = $value;
            $no++;
        }
        return $data;
    }

    public function getDataRujukanKeluar($dataTable)
    {
        $data = [];
        $no = 1;
        foreach ($dataTable as $key => $value) {
            $primaryKey = DocoHelpers::encrypt($value['rujukankeluar_id']);
            $value['primary'] = $primaryKey;
            unset($value['rujukankeluar_id']);

            $value['aksi']  = Html::button(
                "<i class='fa fa-eye'></i>", [
                    'style' => 'margin-right:5px',
                    'class' => 'btn btn-info btn-xs data-view',
                    'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_rujukankeluar',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Lihat')
                ]
            );
            $value['aksi'] .= Html::button( 
                "<i class='fa fa-pencil'></i>", [
                    'style' => 'margin-right:5px ',
                    'class' => 'btn btn-primary btn-xs data-update-rujukan-keluar',
                    'action' => Url::home().$this->_module.'update?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_rujukankeluar',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Ubah'),
                ]
            );
            $value['aksi'] .= Html::a(
                "<i class='fa fa-trash'></i>",'#', [
                    'id' => 'hapus_rujukankeluar',
                    'style' => 'margin-right:5px',
                    'class' => 'btn btn-danger btn-xs delete data-delete-rujukan-keluar',
                    'data-popup' => "tooltip",
                    'data-placement' => 'bottom',
                    'data-original-title' => Yii::t('fe', 'Hapus'),
                    'action' => Url::home().$this->_module.'delete?id='.$primaryKey
                ]
            );
            $value['is_active'] = $value['is_active'] == true ? Html::tag('span', Yii::t('fe', 'Aktif'), ['class' => 'label label-success']) : Html::tag('span', Yii::t('fe', 'Tidak Aktif'), ['class' => 'label label-danger']);
            $value['rowNum'] = $no;
            $data[$key] = $value;
            $no++;
        }
        return $data;
    }

}
