<?php 

/**
 * @author Budi
 * @todo Transaksi Pemberian Piutang
 * @copyright 12 Desember 2019
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
use Doco\kasir\models\PemberianPiutangForm;

class PerbandinganHargaController extends DocoController
{
    use DocoSelect2Trait;
    
    protected $_title = "Transaksi Pemberian Piutang";
    protected $_module = '/kasir/pemberian-piutang';
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
        return [
            'get-data-pendaftaran' => [
                'class' => 'app\components\actions\GetDataAction',
                'serviceName' => $this->_restKasir,
                'serviceAction' => 'perbandingan-harga/get-data-pendaftaran',
                'data_name' => [
                    'no_pendaftaran',
                    'nama_pasien'
                ],
                'keyField' => 'pendaftaran_id'
            ]
        ];
    }

    public function actionIndex()
    {
        $kelasPelayanan = $this->guzzleExec($this->_restKasir,[
            'url' => 'allow/get-kelas'
        ]);
        return $this->render('index', get_defined_vars());
    }

    public function actionView()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName');
        $noPendaftaran = $request->get('noPendaftaran');
        return $this->renderAjax('view', get_defined_vars());
    }

    public function actionPreViewPdf($id, $kelasId)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/preview-perbandingan.pdf";
        try {
            $response = $this->_restKasir->post('perbandingan-harga/pre-view', [
                'save_to' => $path,
                'query' => [
                    'pendaftaranId' => $id,
                    'kelasId' => $kelasId
                ],
            ]);
            return DocoHelpers::previewPdf($path,$response);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionShowPopup()
    {
        $title = 'Preview Perbandingan harga';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $_GET['randString'] = $randString;
        $get = $request->get();
        Yii::$app->session->setFlash($randString, $get);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSync($randString)
    {
        $session = Yii::$app->session->getFlash($randString);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "perbandingan-harga/cetak-pdf",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $noPendaftaran = $request->get('noPendaftaran', null);
        $path = Yii::getAlias("@download")."/Cetak Perbandingan Harga - ".$noPendaftaran;
        $response = $this->_restKasir->get('perbandingan-harga/download-pdf',
        [
            'query' => [
                'fileName' => $fileName,
            ],
            'save_to' => $path,
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }
}