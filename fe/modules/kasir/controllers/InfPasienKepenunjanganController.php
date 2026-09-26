<?php
// Author : Ramdhan Nurrachman

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DHtml;

class InfPasienKepenunjanganController extends DocoController
{
    protected $_title = "Informasi Pasien Penunjang";
    protected $_module = 'kasir/inf-pasien-kepenunjangan/';
    protected $_restKasir; protected $_restMaster;

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
        $jsonInstalasi = json_encode(DocoConstants::INSTALASI_ID_PENUNJANG);
        $encryptedJson = DocoHelpers::encrypt($jsonInstalasi);
        $response = $this->_restKasir->get('inf-pasien-kepenunjangan/get-api?json_inst_id=' . $encryptedJson);
        $body = json_decode($response->getBody(), TRUE);
        $resMaster = isset($body['response']) ? $body['response'] : [];

        $getApiCaraBayar = $this->_restKasir->get('allow/get-cara-bayar');
        $bodyApiCaraBayar = json_decode($getApiCaraBayar->getBody(), TRUE);
        $getCaraBayar = isset($bodyApiCaraBayar['response']) ? $bodyApiCaraBayar['response'] : [];

        return $this->render('index', get_defined_vars());
    }
 
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restKasir->get('inf-pasien-kepenunjangan/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
                unset($value['pasienmasukpenunjang_id']);
                $value['ruangan'] = DocoHelpers::encrypt($value['ruangan_id']);

                $value['tglmasukpenunjang'] = date("j M Y", strtotime($value['tglmasukpenunjang']));
                $value['tgl_pendaftaran'] = date("j M Y", strtotime($value['tgl_pendaftaran']));

                $value['jumlah_tagihan'] = DocoHelpers::formatNumber($value['jumlah_tagihan']);
                $value['rowNum'] = $no; $value['primary'] = $primaryKey;
                // $value['kelompoktindakan'] = 
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionView($id,$status = null)
    {
        $roleBtnCloseBill = DHtml::cekHakAkses('close-bill');
        $roleBtnCloseBill = ($roleBtnCloseBill) ? 1 : 0;
        return Yii::$app->runAction('/kasir/pembayaran-tagihan',[
            'id' => $id,
            'kelompok' => DocoConstants::PASIEN_PENUNJANG,
            'status' => $status,
            'roleBtnCloseBill' => $roleBtnCloseBill,
        ]);
    }

    public function actionDetail($id)
    {
        $title = Yii::t('fe', 'Rincian tagihan pasien penunjang');
        $id = DocoHelpers::decrypt($id);
        $ruangan = isset($_GET['ruangan']) ? $_GET['ruangan'] : '';
        $request = Yii::$app->request;
        $response = $this->_restKasir->get('inf-pasien-kepenunjangan/detail?pendaftaran_id='.$id,['form_params'=>[]]);
        $data = json_decode($response->getBody(), true);
        $details = $data['response']['detail'];
        $dataHeader = $data['response']['header'];
        $detailPerRuangan = [];
        foreach ($details as $key => $detail) {
            $detailPerRuangan[$detail['ruangan_nama']][] = $detail;
        }
        return $this->render('detail', get_defined_vars());
    }

    public function actionPrintPdf($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/Tagihan-pasien-penunjang.pdf";
        try {
            $id = DocoHelpers::decrypt($id);
            $ruangan = DocoHelpers::decrypt($request->get('ruangan', null));
            
            $response = $this->_restKasir->get(
                'inf-pasien-kepenunjangan/print-detail',
                [
                    'save_to' => $path,
                    'query' => [
                        'id'=>$id,
                        'ruangan'=>$ruangan
                    ],
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return json_encode($e->getMessage());
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) { 
            return json_encode($e->getMessage());
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
