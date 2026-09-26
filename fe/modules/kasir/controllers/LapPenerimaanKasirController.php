<?php
/**
 * @author: [Budi][budi@docotel.com]
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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LapPenerimaanKasirController extends DocoController
{
    protected $_title = "Laporan Penerimaan Kasir";
    protected $_module = 'kasir/lap-penerimaan-kasir/';
    protected $_restKasir; protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; $this->_restMaster = Yii::$app->docoRest->master;
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
        return $this->render('index', compact('title'));
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
            $response = $this->_restKasir->get('lap-penerimaan-kasir/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $instalasi_ruangan = '-';
                $instalasi = isset($value['instalasi_nama']) ? $value['instalasi_nama'] : '';
                $ruangan = isset($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
                $value['tanggal'] = !empty($value['tanggal']) ? date("j M Y", strtotime($value['tanggal'])) : '-';
                if(!empty($instalasi) && !empty($ruangan)) {
                    $instalasi_ruangan = $instalasi.' / '.$ruangan;
                }
                $value['instalasi_ruangan'] = $instalasi_ruangan;
                $value['deskripsi'] = isset($value['deskripsi']) ? $value['deskripsi'] : '-';
                $value['kelas_pelayanan'] = isset($value['kelaspelayanan_nama']) ? $value['kelaspelayanan_nama'] : '-';
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = count($body['response']['data']);
            $result['recordsFiltered'] = count($body['response']['data']);
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
        $path = Yii::getAlias("@download") . "/lap-penerimaan-kasir.xlsx";
        $response = $this->_restKasir->get('lap-penerimaan-kasir/export-excel?'.http_build_query($yiiRestfulParams),[
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), True);
        // dump($body);die;
        return DocoHelpers::downloadFile($path, true);
    }
}