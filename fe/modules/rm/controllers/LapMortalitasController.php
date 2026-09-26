<?php 
namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class LapMortalitasController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/lap-mortalitas/';
    protected $_controllerService = 'lap-mortalitas/';
    protected $_controllerAllow = 'allow/';
    const KTP = 94;

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Mortalitas');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->_restRm->get($this->_controllerService.'generate-api');
        $api = json_decode($api->getBody(), True);
        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $no = $request->get('start', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restRm->get($this->_controllerService.'data-laporan?'.http_build_query($yiiRestfulParams), [
                'form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $ktp = "";
                $arrUmur = !empty($value['tanggal_lahir']) ? self::generateUmur($value['tanggal_lahir'], $value['tgl_meninggal']) : [];
                $value['rowNum'] = $no;
                $value['tgllhr_tgl'] = date('d', strtotime($value['tanggal_lahir']));
                $value['tgllhr_bln'] = date('M', strtotime($value['tanggal_lahir']));
                $value['tgllhr_thn'] = date('Y', strtotime($value['tanggal_lahir']));
                $value['rt_rw'] = !empty($value['rt']) || !empty($value['rw']) ? $value['rt']."/".$value['rw'] : "";
                $value['jam_meninggal'] = date('H:i', strtotime($value['tgl_meninggal']));
                $value['tgl_meninggal'] = date('d-M-y', strtotime($value['tgl_meninggal']));
                $value['umur_tahun'] = !empty($arrUmur) ? $arrUmur['tahun'] : "";
                $value['umur_bulan'] = !empty($arrUmur) ? $arrUmur['bulan'] : "";
                $value['umur_hari'] = !empty($arrUmur) ? $arrUmur['hari'] : "";
                $value['khusus_perempuan_10_sampai_54'] = "";
                $value['diagnosa'] = '<b>Utama</b> : '.$value['diagnosa_utama'].'<br><b>Penyerta</b>: '.str_replace(" - ", ",<br>", $value['diagnosa_penyerta']);
                if($value['no_identitas_pasien'] && self::isJson($value['no_identitas_pasien'])) {
                    $tmpIdentitas = json_decode($value['no_identitas_pasien'], true);
                    foreach($tmpIdentitas as $k => $v) {
                        if($v['jenisidentitas'] == self::KTP) {
                            $ktp = $v['no_identitas_pasien'];
                        }
                    }
                } else {
                    $ktp = $value['no_identitas_pasien'];
                }
                $value['no_identitas_pasien'] = $ktp;
                $value['no_rekam_medik'] = !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '-';
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
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") .'/'.$this->_title.'.xlsx';

            $response = $this->_restRm->get($this->_controllerService.'export-excel?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path, true);
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

        $penjaminRequest = $this->_restRm->get($this->_controllerAllow.'list-penjamin?carabayar_id='.$carabayar_id);
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

    /**
     * @function : generate umur (helper doco bug sudah dipake banyak tempat sepertinya)
     */
    private static function generateUmur($date, $dateDie)
    {
        $arr_umur = [];
        $tmpDate = date('Y-m-d', strtotime($date));
        $diff = date_diff(date_create(date('Y-m-d', strtotime($tmpDate))), date_create(date('Y-m-d', strtotime($dateDie))));

        $arr_umur['hari'] = $diff->d;
        $arr_umur['bulan'] = $diff->m;
        $arr_umur['tahun'] = $diff->y;

        return $arr_umur;
    }

    private static function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}