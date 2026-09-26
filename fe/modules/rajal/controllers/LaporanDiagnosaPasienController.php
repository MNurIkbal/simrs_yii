<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-26 14:36:48 
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 12:13:05
 * @Description: 
 */

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LaporanDiagnosaPasienController extends DocoController
{
    protected $_title = "Rajal :: Laporan diagnosa pasien rawat jalan";
    protected $_module = '/rajal';
    protected $_controller = '/rajal/laporan-diagnosa-pasien';
    protected $_page;
    protected $_restRajal;
    protected $_id_ruangan;
    protected $_nama_ruangan;

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name') ? Yii::$app->docoVars->workspace('ruangan_name') : '';
        $this->_page = Yii::t('fe', 'Diagnosa pasien rawat jalan');
    }

    public function actionIndex()
    {
        $id_ruangan = DocoHelpers::encrypt($this->_id_ruangan);
        $sub_title = $this->_page;
        $status = $this->_status;

        // data select
        $list_data = $this->getListData();
        $data_kelompokdiagnosa = $list_data["data_kelompokdiagnosa"];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $this->_id_ruangan;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRajal->get('lap-diagnosa-pasien/index?ruangan_id=' . $this->_id_ruangan . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $no = $request->get('start', 1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $value['tgl_diagnosa'] = date('d M Y H:i:s', strtotime($value['tgl_diagnosa']));
                $value['no_pendaftaran_rm'] = $value['no_pendaftaran'].' / '.$value['no_rekam_medik'];
                $value['rowNum'] = $no;
                $expDiagnosaNama =  explode(' - ', $value['diagnosa_nama']);
                $value['exp_nama_diagnosa'] = isset($expDiagnosaNama[1]) ? $expDiagnosaNama[1] : $value['diagnosa_nama'];

                $data[$key] = $value;
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

    

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $this->_id_ruangan;
        $path = Yii::getAlias("@download") . "/lap-diagnosa-pasien.pdf";
        try {
            $response = $this->_restRajal->get('lap-diagnosa-pasien/export-pdf?ruangan_id='.$this->_id_ruangan . '&' .http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportPdfPasien($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $path = Yii::getAlias("@download") . "/lap-diagnosa-per-pasien.pdf";
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $this->_id_ruangan;
        $yiiRestfulParams['advanced-filter']['pendaftaran_id'] = $pendaftaran_id;
        
        try {
            $response = $this->_restRajal->get('lap-diagnosa-pasien/export-pdf-pasien?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $this->_id_ruangan;
        try {
            $path = Yii::getAlias("@download") . "/laporan-diagnosa-pasien.xlsx";
            $query = [
                'ruangan_id' => $this->_id_ruangan
            ];
            $query = array_merge($query,$yiiRestfulParams);
            $response = $this->_restRajal->get('lap-diagnosa-pasien/export-excel',[
                'query' => $query,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
     *
     * private function
     *
     */

    private function getListData()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restRajal->get('lap-diagnosa-pasien/get-list-data?ruangan_id=' . $this->_id_ruangan);
            $body = json_decode($response->getBody(), true);

            $data_kelompokdiagnosa = empty($body['response']['data-kelompokdiagnosa']) ? [] : $body['response']['data-kelompokdiagnosa'];

            $result = [
                'data_kelompokdiagnosa' => $data_kelompokdiagnosa,
            ];

            return $result;
        } catch (RequestException $e) {
            return [];
        } catch (\Exception $e) {
            return [];
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
}