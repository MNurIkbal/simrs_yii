<?php 
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\kasir\controllers;

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
    protected $_module = '/kasir/master-api';
    protected $_restKasir; 
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
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
            'get-data-tipe-transaksi' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-tipe-transaksi',
                'data_name' => [
                    'lookup_name'
                ],
                'keyField' => 'lookup_id'
            ],
            'get-data-metode-bayar' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-metode-bayar',
                'data_name' => [
                    'lookup_name'
                ],
                'keyField' => 'lookup_id'
            ],
            'get-data-karyawan' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-karyawan',
                'data_name' => [
                    'nomorindukpegawai',
                    'nama_pegawai'
                ],
                'keyField' => 'pegawai_id'
            ],
            'get-data-vendor' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-vendor',
                'data_name' => [
                    'supplier_nama'
                ],
                'keyField' => 'supplier_id'
            ],
            'get-data-pasien' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-pasien',
                'data_name' => [
                    'no_rekam_medik',
                    'nama_pasien'
                ],
                'keyField' => 'pasien_id'
            ],
            'get-data-kategori-trx' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-kategori-trx',
                'data_name' => [
                    'kategoritransaksi_kode',
                    'kategoritransaksi_nama'
                ],
                'keyField' => 'kategoritransaksi_id'
            ],
            'get-data-pendaftaran-reseptur' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-pendaftaran-reseptur',
                'data_name' => [
                    'no_pendaftaran',
                    'nama_pasien'
                ],
                'keyField' => 'pendaftaran_id'
            ],
            'get-data-nontunai' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-nontunai',
                'data_name' => [
                    'kode',
                    'nama'
                ],
                'keyField' => 'jenisnontunai_id'
            ],
            'get-data-bank' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-bank',
                'data_name' => [
                    'nama_bank'
                ],
                'keyField' => 'bank_id'
            ],
            'get-data-jasa-dokter' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-jasa-dokter',
                'data_name' => [
                    'jasadokter_kode',
                    'jasadokter_nama'
                ],
                'keyField' => 'jasadokter_id'
            ],
            'get-data-tenaga-medis' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-tenaga-medis',
                'data_name' => [
                    'nama_pegawai'
                ],
                'keyField' => 'pegawai_id'
            ],
            'get-data-edc' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-edc',
                'data_name' => [
                    'edclist_kode',
                    'edclist_namamesin'
                ],
                'keyField' => 'edclist_id'
            ],
            'get-data-instalasi' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-instalasi',
                'data_name' => [
                    'instalasi_nama',
                ],
                'keyField' => 'instalasi_id'
            ],
            'get-data-ruangan' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-ruangan',
                'data_name' => [
                    'ruangan_nama',
                ],
                'keyField' => 'ruangan_id'
            ],
            'get-data-metode-transaksi' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'master-api/get-data-metode-transaksi',
                'data_name' => [
                    'lookup_name'
                ],
                'keyField' => 'lookup_value'
            ],
        ];
    }
}