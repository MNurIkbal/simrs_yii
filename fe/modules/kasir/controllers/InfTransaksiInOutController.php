<?php


/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


namespace Doco\kasir\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

use app\components\DocoHelpers;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InfTransaksiInOutController extends DocoController
{
    protected $_title = "Informasi Penerimaan / Pengeluaran";
    protected $_restKasir;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
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
        $response = $this->_restKasir->get('inf-transaksi-in-out/get-lookup-type');
        $body = json_decode($response->getBody(), true);
        $response = isset($body['response']) ? $body['response'] : [];
        $metodeBayar = isset($response['metode_bayar']) ? $response['metode_bayar'] : [];
        $jenisTrans = isset($response['jenis_transaksi']) ? $response['jenis_transaksi'] : [];
        $tipeTrans = isset($response['tipe_transaksi']) ? $response['tipe_transaksi'] : [];
        return $this->render('index',get_defined_vars());
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

        $response = $this->_restKasir->get('inf-transaksi-in-out/index?'.http_build_query($yiiRestfulParams));
        $body = json_decode($response->getBody(), True);

        $no = $request->get('start',1);
        foreach ($body['response']['data'] as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['pembayarantransaksi_id']);
            unset($value['pembayarantransaksi_id']);

            $value['tgl_transaksi'] = date("j M Y", strtotime($value['tgl_transaksi']));
            $value['jumlah'] = DocoHelpers::formatNumber($value['jumlah']);

            $value['rowNum'] = $no; $value['primary'] = $primaryKey;
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
        $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
        return $result;
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "inf-transaksi-in-out/export-excel?".http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Informasi Transaksi Penerimaan Pengeluaran.xlsx";
        $response = $this->_restKasir->get($url,[
            'save_to' => $path,
        ]);
        $body = json_decode($response->getBody(), True);
        return DocoHelpers::downloadFile($path,true);

        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $result = [];
        $url = "";
        try {
            $path = Yii::getAlias("@download") . "/Informasi Transaksi Penerimaan Pengeluaran.xlsx";
            $response = $this->_restKasir->get('inf-transaksi-in-out/export-excel?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionCetakKwitansi()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        $path = Yii::getAlias("@download")."/cetak-kwitansi-transaksi.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $response = $this->_restKasir->get('inf-transaksi-in-out/cetak-kwitansi', [
            'query' => [
                'id' => $id,
                'nama_pegawai' => $userIdentity['nama_pegawai'],
            ],
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

}