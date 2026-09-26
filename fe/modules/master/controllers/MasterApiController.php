<?php 
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoSelect2Trait;

class MasterApiController extends DocoController
{
    // allow sequa blok
    protected $allowAction = [ '*' ];
    
    use DocoSelect2Trait;
    
    protected $_title = "Master API";
    protected $_module = '/master/master-api';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions()
    {
        /**
         * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
         * 
         * DATA ATTRIBUTE YANG BISA DIGUKANAN
         * 
         * --------------------------------------------------
         * data_name : nama kolom yang akan dimunculkan pada value text select2 (MAX 4 DATA)
         * keyField : sebagai id pada dropdown select2
         */
        return [
            'get-list-komponen' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'master-api/get-list-komponen',
                'data_name' => [
                    'komponentarif_kode',
                    'komponentarif_nama',
                ],
                'keyField' => 'komponentarif_id'
            ],
            'get-service-category' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'odoo/get-service-category',
                'data_name' => [
                    'servicecategory_nama',
                ],
                'keyField' => 'servicecategory_id'
            ],
            'get-service-group' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'odoo/get-service-group',
                'data_name' => [
                    'servicegroup_nama',
                ],
                'keyField' => 'servicegroup_id'
            ],
            'get-list-dokter' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'master-api/get-list-dokter',
                'data_name' => [
                    'nama_pegawai',
                ],
                'keyField' => 'pegawai_id'
            ],
            'get-list-kamar' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'master-api/get-list-kamar',
                'data_name' => [
                    'kamarruangan_nokamar',
                ],
                'keyField' => 'kamarruangan_id'
            ],
            'get-list-kelas' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'master-api/get-list-kelas',
                'data_name' => [
                    'kelaspelayanan_nama',
                ],
                'keyField' => 'kelaspelayanan_id'
            ],
            'get-list-carabayar' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'master-api/get-list-carabayar',
                'data_name' => [
                    'carabayar_nama',
                ],
                'keyField' => 'carabayar_id'
            ],
            'get-list-penjamin' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'master-api/get-list-penjamin?carabayar_id=',
                'data_name' => [
                    'penjamin_nama',
                ],
                'keyField' => 'penjamin_id'
            ],
            'get-data-instalasi' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'master-api/get-data-instalasi',
                'data_name' => [
                    'instalasi_nama',
                ],
                'keyField' => 'instalasi_id'
            ],
            'get-data-ruangan' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restMaster,
                'serviceAction' => 'master-api/get-data-ruangan',
                'data_name' => [
                    'ruangan_nama',
                ],
                'keyField' => 'ruangan_id'
            ],
        ];
    }
}