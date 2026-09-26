<?php

/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-08-15 16:59:18
 * @Description:
 */

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\rajal\models\InformasiForm;
use app\modules\rajal\models\AnamnesaForm;
use app\modules\rajal\models\TindakanPelayananForm;
use app\modules\rajal\models\ObatAlkesPasienForm;
use app\modules\rajal\models\HasilPemeriksaanLabForm;
use app\modules\rajal\models\PemeriksaanFisikForm;
use app\modules\rajal\models\ResepturForm;
use app\modules\rajal\models\ResepturDetailForm;
use app\modules\rajal\models\PasienMorbiditasForm;
use app\modules\rajal\models\PasienDirujukKeluarForm;
use app\modules\rajal\models\PembebasanTarifForm;
use app\modules\rajal\models\KonsulpoliForm;
use app\modules\rajal\models\TindakankomponenForm;
use app\modules\rajal\models\BuatJanjiPoliForm;
use app\modules\rajal\models\PasienPulangForm;
use app\modules\rajal\models\PasienBatalPulangForm;

class InfPasienPulangController extends DocoController
{
    protected $_title = "Informasi Pasien Pulang Rawat Jalan";
    protected $_module = '/rajal/inf-pasien-pulang';
    protected $_controller = '/rajal/inf-pasien-pulang';
    protected $_restRajal;
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_instalasi_id;
    protected $_pegawai_id;

    public function init(){
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = !empty(Yii::$app->docoVars->workspace('ruangan_id')) ? (int)Yii::$app->docoVars->workspace('ruangan_id') : 0 ;
        $this->_instalasi_id = !empty(Yii::$app->docoVars->workspace('instalasi_id')) ? (int)Yii::$app->docoVars->workspace('instalasi_id') : 0;
        $this->_pegawai_id = !empty(Yii::$app->docoVars->workspace('id_pegawai')) ? (int)Yii::$app->docoVars->workspace('id_pegawai') : 0;
    }

    public function behaviors(){
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    public function actionIndex(){
         try {
            $userIdentity = Yii::$app->session->get('user_identity');
            $response = $this->_restRajal->get('allow/get-filter-pasien-pulang?instalasi_id='.$this->_instalasi_id.'&ruangan_id='.$this->_ruangan_id,['form_params'=> [] ]);
            $body = json_decode($response->getBody(), TRUE);
            $body = $body['response'];

            $dataRuangan = empty($body['dataRuangan']) ? [] : $body['dataRuangan'];
            $dataJenisKelamin = empty($body['dataJenisKelamin']) ? [] : $body['dataJenisKelamin'];
            $dataPenjamin = empty($body['dataPenjamin']) ? [] : $body['dataPenjamin'];
            $dataDokter = empty($body['dataDokter']) ? [] : $body['dataDokter'];
            $dataCaraKeluar = empty($body['dataCaraKeluar']) ? [] : $body['dataCaraKeluar'];
            $dataCaraBayar = empty($body['dataCaraBayar']) ? [] : $body['dataCaraBayar'];
            // echo "<pre>";var_dump($dataDokter);die();
            $result = [
                'dataRuangan' => $dataRuangan,
                'dataJenisKelamin' => $dataJenisKelamin,
                'dataPenjamin' => $dataPenjamin,
                'dataDokter' => $dataDokter,
                'dataCaraKeluar' => $dataCaraKeluar,
                'dataCaraBayar' => $dataCaraBayar,
            ];

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        try {
            $response = $this->_restRajal->get('inf-pasien-pulang/index?ruangan_id='.$this->_ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            $no = $request->get('start', 1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('d M Y H:i:s', strtotime($value['tgl_pendaftaran'])); 
                $value['tglpasienpulang'] = date('d M Y H:i:s', strtotime($value['tglpasienpulang'])); 
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionBatalPeriksa()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $pendaftaran_id = DocoHelpers::decrypt($get['pendaftaran_id']);
            $getDetailInfoPasienPulang = $this->GetDetailInfoPasienPulangRJRD($pendaftaran_id);
            $title = 'Pembatalan Pulang Pasien Rawat Jalan';
            $model = new PasienBatalPulangForm;
            $model->pendaftaran_id = $pendaftaran_id;

            return $this->renderPartial('_batal_periksa',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function GetDetailInfoPasienPulangRJRD($pendaftaran_id = null)
    {
        try {
             $response = $this->_restRajal->request('GET', 'inf-pasien-pulang/data-info-pasien-pulang', [
                            'query' => ['pendaftaran_id' => $pendaftaran_id]
                        ]);
            $body = json_decode($response->getBody(),TRUE);
            $return = $body['response'];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionSaveBatalPeriksa()
    {
        try {
            $request = Yii::$app->request;
            $model = new PasienBatalPulangForm;
            $post = $request->post('PasienBatalPulangForm');
            $model->attributes = $post;
            // $model->load($request->post());
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            if($model->validate()){
                if ($post) {
                    $response = $this->_restRajal->post('inf-pasien-pulang/batal-pulang', [
                        'form_params' => $post
                    ]);
                    $body = json_decode($response->getBody(), True);
                    Yii::$app->cache->set('pasien-pendaftaran-id-'.($this->helper->encrypt($model->pendaftaran_id)), []);
                    $data = [
                        'title' => \Yii::t('fe', 'Proses berhasil')." !",
                        'text' => \Yii::t('fe', "Status berhasil diubah.")
                    ];
                    return DocoHelpers::response($body);
                }
            }else{
                $response = $model->getErrors();
                return DocoHelpers::response($response,422,'PasienBatalPulangForm');
            }
        } catch (RequestException $e) {
           return DocoHelpers::responseTemplate(500, $e->getMessage());

        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/inf-pasien-pulang.pdf";
        try {
            $response = $this->_restRajal->get('inf-pasien-pulang/export-pdf?ruangan_id='.$this->_ruangan_id . '&' .http_build_query($yiiRestfulParams),[
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
        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien-pulang.xlsx";
            $query = [
                'ruangan_id' => $this->_ruangan_id
            ];
            $query = array_merge($query,$yiiRestfulParams);
            $response = $this->_restRajal->get('inf-pasien-pulang/export-excel',[
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
}
