<?php
/** Informasi Pasien Batal Bayar
 * Author : Dede Herdiana
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
use app\components\DHtml;

class LapBatalPembayaranController extends DocoController
{
    protected $_title = "Laporan Batal Pembayaran";
    protected $_module = 'kasir/lap-batal-pembayaran/';
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
        $restKasir = Yii::$app->docoRest->kasir;
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
            $response = $this->_restKasir->get('lap-batal-pembayaran/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['nama_pasien'] = $value['nama_pasien'] . ' - ' . $value['no_rm'];
                $value['jumlah_tagihan'] = DocoHelpers::formatNumber($value['jumlah_tagihan']);
                $value['alasan_batal'] = nl2br(strip_tags($value['alasan_batal']));
                $data[]= $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionExportExcel()
    {        
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $result = [];
        $url = "lap-batal-pembayaran/export-excel?".http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Laporan-Batal-Pembayaran.xlsx";
        try {
            $response = $this->_restKasir->get($url,[
                'save_to' => $path,
            ]);

            $body = json_decode($response->getBody(), True);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }

}