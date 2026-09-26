<?php 
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\igd\controllers;

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
    protected $_module = '/igd/master-api';
    protected $_restIgd; 
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restIgd = Yii::$app->docoRest->igd; 
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
        $request = Yii::$app->request;
        return [
            'list-all-new-dokter' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restIgd,
                'serviceAction' => 'master-api/get-all-new-dokter',
                'data_name' => [
                    'nama_pegawai'
                ],
                'keyField' => 'pegawai_id'
            ],
            'list-pemberi-instruksi' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restIgd,
                'serviceAction' => 'master-api/get-pemberi-instruksi?ruangan_id='.$request->get('ruangan_id', null),
                'data_name' => [
                    'nama_pegawai'
                ],
                'keyField' => 'pegawai_id'
            ],
            'list-fee-konsul' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restIgd,
                'serviceAction' => 'master-api/get-fee-konsul?ruangan_id='.$request->get('ruangan_id', null).'&penjamin_id='.$request->get('penjamin_id', null).'&kelaspelayanan_id='.$request->get('kelaspelayanan_id', null),
                'data_name' => [
                    'daftartindakan_nama'
                ],
                'keyField' => 'daftartindakan_id'
            ],
            'list-ruangan' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restIgd,
                'serviceAction' => 'master-api/get-ruangan',
                'data_name' => [
                    'ruangan_nama'
                ],
                'keyField' => 'ruangan_id'
            ],
        ];
    }
}
