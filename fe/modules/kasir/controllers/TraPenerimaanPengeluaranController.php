<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\kasir\models\PenerimaanPengeluaranForm;
use app\components\DocoConstants;

class TraPenerimaanPengeluaranController extends DocoController
{
    // allow sequa blok
    protected $allowAction = [ '*' ];

    protected $_title = "Transaksi Penerimaan/Pengeluaran";
    protected $_module = 'kasir/tra-penerimaan-pengeluaran/';
    protected $_restKasir; 
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
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
        $title = $this->_title;
        $request = Yii::$app->request;
        $model_form = new PenerimaanPengeluaranForm;
        if($request->post()){
            $model_form->load($request->post());
            if($model_form->validate()){
                $response = $this->_restKasir->post('tra-penerimaan-pengeluaran/create',[
                                'form_params'=>$model_form->attributes
                            ]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body);
            }else{
                return DocoHelpers::response($model_form->errors,422, "PenerimaanPengeluaranForm");
            }
        }else{
            $response = $this->_restKasir->get('tra-penerimaan-pengeluaran/get-attributes');
            $body = json_decode($response->getBody(), true);
            $response = isset($body['response']['listAttr']) ? $body['response']['listAttr'] : [];
            $vendor = isset($body['response']['vendor']) ? $body['response']['vendor'] : null;
            $karyawan = isset($body['response']['karyawan']) ? $body['response']['karyawan'] : null;
            $pasien = isset($body['response']['pasien']) ? $body['response']['pasien'] : null;
            $metodeBayar = isset($response['metode_bayar']) ? $response['metode_bayar'] : [];
            $jenisTrans = isset($response['jenis_transaksi']) ? $response['jenis_transaksi'] : [];
            $tipeTrans = isset($response['tipe_transaksi']) ? $response['tipe_transaksi'] : [];
            
            return $this->render('index', get_defined_vars());
        }
    }

    public function actionGetNoPendaftaran($pasien_id){
        $nopendaftarn = $this->guzzleExec($this->_restKasir, [
            'url' => 'tra-penerimaan-pengeluaran/get-no-pendaftaran-pasien',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'pasien_id' => $pasien_id
                ]
            ],
            'returnResponse' => true
        ]);

        if (!empty($nopendaftarn['data'])) {
            $data = $nopendaftarn['data'];
            $allData = [
                'id' => '%',
                'text' => \Yii::t('fe', 'Lihat Semua')
            ];
            $nopendaftarn['data'] = $data;
        }

        return $nopendaftarn;
    }
}