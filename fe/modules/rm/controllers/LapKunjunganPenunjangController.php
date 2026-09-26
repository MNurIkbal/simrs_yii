<?php 
/**
 * @author : Ali (ali.padilah@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\rm\controllers;

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
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;

class LapKunjunganPenunjangController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-kunjungan-penunjang/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Pemeriksaan Penunjang');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actions()
    {
        $actions = parent::actions();
        $newActions = [
            // Excel BGProcess
            'show-popup-excel' => 'Doco\rm\actions\LapKunjunganPenunjang\ShowPopupExcelAction',
            'process-sync-excel' => 'Doco\rm\actions\LapKunjunganPenunjang\ProcessSyncExcelAction',
            'download-file-excel' => 'Doco\rm\actions\LapKunjunganPenunjang\DownloadFileExcelAction',
            // PDF BGProcess
            'show-popup-pdf' => 'Doco\rm\actions\LapKunjunganPenunjang\ShowPopupPdfAction',
            'process-sync-pdf' => 'Doco\rm\actions\LapKunjunganPenunjang\ProcessSyncPdfAction',
            'download-file-pdf' => 'Doco\rm\actions\LapKunjunganPenunjang\DownloadFilePdfAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get('lap-kunjungan-penunjang/generate-api');
        $api = json_decode($api->getBody(), True);

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
        // dump($this->generateJumlah($yiiRestfulParams));die;
        try {
            $response = $this->_restRm->get('lap-kunjungan-penunjang/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['primary'] = ArrayHelper::getValue($value, 'no_pendaftaran');
                $value['tglmasukpenunjang'] = DocoHelpers::display_label($value['tglmasukpenunjang'], true, 
                    date('d M Y H:i:s', strtotime($value['tglmasukpenunjang'])));
                $value['custom_field_penjamin'] = $value['carabayar_nama'] . " / " . $value['penjamin_nama'];
                $value['rowNum'] = $no;
                // $value['primary'] = $value['no_rekam_medik'];
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['rowJumlah'] = $this->generateJumlah($yiiRestfulParams);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('lap-kunjungan/get-ruangan-by?id='.$parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['ruangan_id'], 
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPenjamin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restRm->get('lap-kunjungan/get-penjamin-by?id='.$parent_label);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) 
                $result['output'][] = [
                    'id' => $value['penjamin_id'], 
                    'name' => $value['penjamin_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPegawai()
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
            $response = $this->_restRm->get('lap-kunjungan/list-pegawai?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['nama_pegawai'],
                    'title' => \Yii::t('fe', 'Pilih'),
                ]);
                
                $value['rowNum'] = $no;
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

    public function actionSearch($tipe = NULL)
    {        
        $jabatan = $this->_restRm->get('lap-kunjungan/list-jabatan');
        $jabatan = json_decode($jabatan->getBody(), True);

        return $this->renderPartial('search', get_defined_vars());
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/lap-kunjungan-penunjang.xlsx";
            $response = $this->_restRm->get('lap-kunjungan-penunjang/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        try {
            $path = Yii::getAlias("@download") . "/Rekap-Kunjungan-Penunjang.pdf";
            $request = Yii::$app->request;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

            $response = $this->_restRm->get('lap-kunjungan-penunjang/export-pdf', [
                'query' => $filter,
                'save_to' => $path
            ]);

            // dump(json_decode($response->getBody(), true)); die();

            return DocoHelpers::previewPdf($path);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionListPenjamin() {
        $request = Yii::$app->request;
        $post = $request->post();
        $carabayar_id = $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restRm->get('allow/list-penjamin?carabayar_id='.$carabayar_id);
        $body = json_decode($penjaminRequest->getBody(),TRUE);
        $responses = $body['response'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        return json_encode(['output'=>$out, 'selected'=>'']);
    }

    public function actionListJenisPemeriksaan() {
        $request = Yii::$app->request;
        $post = $request->post();
        $instalasi = $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restRm->get('lap-kunjungan-penunjang/list-pemeriksaan?instalasi='.$instalasi);
        $body = json_decode($penjaminRequest->getBody(),TRUE);
        $responses = $body['response'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $response,
                'name' => $response
            ];
        }

        return json_encode(['output'=>$out, 'selected'=>'']);
    }

    private function generateJumlah($yiiRestfulParams = null) 
    {
        $response = $this->_restRm->get('lap-kunjungan-penunjang/generate-row-jumlah?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
        $body = json_decode($response->getBody(), True);
        return $body['response'];
    }
}