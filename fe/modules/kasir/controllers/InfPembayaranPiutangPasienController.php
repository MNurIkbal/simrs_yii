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
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\kasir\models\InfoPembayaranPiutang;
use GuzzleHttp\Exception\RequestException;

class InfPembayaranPiutangPasienController extends DocoController
{
    protected $_title = "Informasi Pembayaran Piutang Pasien";
    protected $_module = '/kasir/inf-pembayaran-piutang-pasien';
    protected $_controller = '/kasir/inf-pembayaran-piutang-pasien';
    protected $_restKasir; 
    protected $_restMaster;

    // allow sequa blok
    protected $allowAction = [ '*' ];

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        
        $title = $this->_title;
        $api = $this->_restKasir->get('inf-pembayaran-piutang-pasien/generate-api-piutang');
        $api = json_decode($api->getBody(), True);
        $getMetodeBayar = $this->_restKasir->get('master-api/get-data-metode-bayar');
        $getMetodeBayar = json_decode($getMetodeBayar->getBody(), True);
        $getMetodeBayar = isset($getMetodeBayar['response']) ? $getMetodeBayar['response'] : [];
        $metodeBayar = ArrayHelper::map($getMetodeBayar , 'lookup_id', function ($model) {
            return $model['lookup_name'];
        });

        $getNonTunai = $this->_restKasir->get('master-api/get-data-nontunai');
        $getNonTunai = json_decode($getNonTunai->getBody(), True);
        $getNonTunai = isset($getNonTunai['response']) ? $getNonTunai['response'] : [];
        $metodeNonTunai = ArrayHelper::map($getNonTunai , 'jenisnontunai_id', function ($model) {
            return $model['nama'];
        });

        $listMetode = [];
        foreach($metodeBayar as $k => $v) {
            $listMetode[$k] = $v;
            if($v == "Non Tunai") {
                foreach($metodeNonTunai as $key => $value) {
                    $listMetode[$k."-".$key] = $v . " - " . $value;
                }
            }
        }

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
            $response = $this->_restKasir->get('inf-pembayaran-piutang-pasien/index?'. http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pembayaranpiutang_id']);
                unset($value['pembayaranpiutang_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['total_bayarpiutang'] = DocoHelpers::rupiahDisplay($value['total_bayarpiutang']);
                $value['tgl_pembayaranpiutang'] = date('d M Y', strtotime($value['tgl_pembayaranpiutang']));
                $value['total_sisapiutang'] = DocoHelpers::rupiahDisplay($value['total_sisapiutang']);
                $value['metode_pembayaran'] = isset($value['metode_pembayaran']) ? $value['metode_pembayaran_nama'] : '-';
                if (isset($value['jenisnontunai_nama'])) {
                    $value['metode_pembayaran'] = $value['metode_pembayaran'].' - '.$value['jenisnontunai_nama'];
                }
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

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "";
        try {
            $path = Yii::getAlias("@download") . "/inf-pembayaran-piutang-pasien.xlsx";
            $response = $this->_restKasir->get('inf-pembayaran-piutang-pasien/export-excel?' . http_build_query($yiiRestfulParams), ['form_params' => [],
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            return $e;
        } catch (\Exception $e) {
            return $e;
        }
    }

    private function downloadFile($filename)
    {
        $file = basename($filename);
        $fp = fopen($file, 'w');
        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'".xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;
    }


    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/inf-pembayaran-piutang-pasien.pdf";
        try {
            $response = $this->_restKasir->get('inf-pembayaran-piutang-pasien/cetak-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true); 

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakKwitansi()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        $path = Yii::getAlias("@download")."/cetak-kwitansi-pembayaran-piutang.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $urlReport = 'kwitansi-pembayaran-piutang';
        try {
            if(Yii::$app->report->isAvailable($urlReport)){
               return Yii::$app->report->exec($urlReport, [
                     'queryParameter' => [
                        'id' => $id,
                        'nama_pegawai' => $userIdentity['nama_pegawai'],
                     ],
               ]);
            }
            $response = $this->_restKasir->get('inf-pembayaran-piutang-pasien/cetak-kwitansi', [
                'query' => [
                    'id' => $id,
                    'nama_pegawai' => $userIdentity['nama_pegawai'],
                ],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}