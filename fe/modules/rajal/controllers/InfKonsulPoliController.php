<?php
/**
 * @author: arief saputra
 * @description: informasi konsul poli rawat jalan
**/

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\rajal\models\KonsulpoliForm;
use GuzzleHttp\Exception\RequestException;


class InfKonsulPoliController extends DocoController
{
    protected $_title = 'Informasi Konsul Poli';
    protected $_module = 'inf-konsul-poli/';
    protected $_restRajal;
    protected $_ruangan_id;
    protected $_kelompok_pegawai_id;
    protected $allowAction = [
        'export-pdf-konsul',
    ];

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;

        $this->_ruangan_id = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_kelompok_pegawai_id = Yii::$app->user->identity->kelompokpegawai_id;
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
        // dump($this->_ruangan_id);die;
        $status = $this->_status;
        $title = Yii::t('fe', $this->_title);
        $default_url = Url::home().(Yii::$app->controller->module->id."/".Yii::$app->controller->id);
        // $response = $this->_restRajal->get('inf-konsul-poli/get-lookup-type');
        $response = $this->_restRajal->get('inf-konsul-poli/get-bundle-data');
        $body = json_decode($response->getBody(), true);
        $response = isset($body['response']) ? $body['response'] : [];
        $statusKonsul = isset($response['status_konsulpoli']) ? $response['status_konsulpoli'] : [];
        $ruangan = isset($response['ruangan']) ? $response['ruangan'] : [];
        $dokter = isset($response['dokter']) ? $response['dokter'] : [];

        $hasAccessApprove = DocoHelpers::checkButtonAccess('/rajal/inf-konsul-poli', 'approve');
        $hasAccessUpdate = DocoHelpers::checkButtonAccess('/rajal/inf-konsul-poli', 'update');

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $get = $request->get();
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

            // return $yiiRestfulParams['advanced-filter'];
            $response = $this->_restRajal->get('inf-konsul-poli/index?ruangan_id='. $this->_ruangan_id .'&kelompokpegawai_id='.$this->_kelompok_pegawai_id.'&'. http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body['response']);
            $row = [];
            $no = $request->get('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;

                $primaryKey = DocoHelpers::encrypt($value['konsulpoli_id']);
                $value['primary'] = $primaryKey;

                unset($value['konsulpoli_id']);

                $value['tgl_konsulpoli'] = date('d-m-Y',strtotime($value['tgl_konsulpoli']));
                $value['nama_pasien'] = $value['nama_pasien'].' - </br>'.$value['no_rekam_medik'].' - </br>'.$value['no_pendaftaran'];
                $value['ruangan_asal'] = $value['ruangan_asal'].'</br>'.$value['dok_mengkonsul'];
                $value['tgl_selesaikonsul'] = $value['tgl_selesaikonsul'] !== null ? date('d-m-Y', strtotime($value['tgl_selesaikonsul'])).' - </br>'.$value['no_pendaftaran'] : "";
                $value['catatan_dokter_konsul'] = '<div><p class="wraptext">'.$value['catatan_dokter_konsul'].'</p></div>';
                $value['is_dokter_dirujuk'] = (Yii::$app->user->identity->id_pegawai == $value['pegawai_id']) ? 1 : 0;
                $value['is_not_dokter'] = (Yii::$app->user->identity->kelompokpegawai_id == DocoConstants::KELOMPOK_MEDIS) ? 0 : 1;
                $value['rowNum'] = $no;
                $value['status_konsul'] = $value['status_periksa'] == DocoConstants::BTL_PERIKSA ? $value['status'] : $value['status_konsul'];
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
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

    public function actionDetail($id)
    {
        try {
            $model = new KonsulpoliForm;
            $model->scenario = KonsulpoliForm::SCENARIO_UPDATE;

            $title = Yii::t('fe', "Detail ".$this->_title);
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($id);
            $title = $this->_title;
            $action = Yii::t('fe', 'Detail');

            $RuanganRequest = $this->_restRajal->get('inf-konsul-poli/list-ruangan');
            $body = json_decode($RuanganRequest->getBody(),TRUE);
            $list_ruangan = $body['response']['data-jadwalpoli'];
            $list_ruangan = ArrayHelper::map($list_ruangan, 'ruangan_id', 'ruangan_nama');;

            $response = $this->_restRajal->get('inf-konsul-poli/view?id='. $id);

            $data = json_decode($response->getBody(), true);
            $data = $data['response']['data'];

            $pk = DocoHelpers::encrypt($id);

            if($post = $request->post()){
                $post['konsulpoli_id'] = $id;
                $response = $this->_restRajal->request('POST', 'inf-konsul-poli/update', ['form_params'=>$post]);
                $body = json_decode($response->getBody(), true);

                return DocoHelpers::response($body, false, true);
            }else{
                return $this->renderAjax('detail', get_defined_vars());
            }

        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionBatal($id){
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);

        try {
            /* batal disini */
            $response = $this->_restRajal->request('POST', 'inf-konsul-poli/update', ['form_params'=>
                    [
                        'konsulpoli_id' => $id,
                        'status_periksa' => DocoConstants::BTL_PERIKSA
                    ]
                ]);

            $body = json_decode($response->getBody(), true);
            // $return = ['response'=>$body['response']];
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionListDokter()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $ruangan_id = $post['depdrop_parents'][0];

        $PropinsiRequest = $this->_restRajal->get('allow/list-dokter?ruangan_id='. $ruangan_id);
        $body = json_decode($PropinsiRequest->getBody(),TRUE);
        $ddlDokter = $body['response']['data'];

        $out = [];
        foreach($ddlDokter as $key => $value) {
            $status = true;
            $kuota = ($value['kuota_tersedia']) ? $value['kuota_tersedia'] : 0;
            if ($kuota > 0){
                $status = false;
            }

            $out[] = [
                    'id' => $value['pegawai_id'],
                    // 'name' => $value['nama_pegawai'] ." - ". $kuota,
                    'name' => $value['nama_pegawai'],
                    'options' => ['disabled' => $status, 'data-jadwaldokter' => $value['jadwaldokter_id']]
                ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    // Get data untuk beberapa dropdown
    private function getDataDropdown()
    {
        // Get data dropdown
        $dataDropdown = $this->_restRajal->request('POST', 'inf-konsul-poli/get-data-dropdown', ['form_params' => [
                        'ruangan_id' => $this->_ruangan_id,
                    ]
                ]);
        $dataDropdown = json_decode($dataDropdown->getBody(), true);
        $dataDropdown = $dataDropdown['response'];

        // Convert to array
        $dataDropdown['ruangan'] = ArrayHelper::map($dataDropdown['ruangan'], 'ruangan_id','ruangan_nama');
        $dataDropdown['dokter'] = ArrayHelper::map($dataDropdown['dokter'], 'pegawai_id','nama_pegawai');
        $dataDropdown['statusPeriksa'] = ArrayHelper::map($dataDropdown['statusPeriksa'], 'lookup_id','lookup_name');

        // Return
        return $dataDropdown;
    }

    public function actionExportPdfKonsul()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $id = $request->get('id');
        $id = DocoHelpers::decrypt($id);
        $jenis = $request->get('jenis');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $path = Yii::getAlias("@download") . "/konsulpoli_". $id .".pdf";
        try {

            if($jenis == 'jawaban-konsul'){
                $urlReport = 'jawaban-konsultasi';
            } else {
                $urlReport = 'permintaan-konsultasi';
            }
            if(Yii::$app->report->enabled){
                $id = $request->get('id', null);
                $query = [
                    'konsulpoli_id' => @DocoHelpers::decrypt($id),
                    'pegawai_id' => $pegawai_id
                ];
                $path = !empty($optGuzzle['save_to']) ? $optGuzzle['save_to'] : null;
    
                return Yii::$app->report->exec($urlReport,[
                    'queryParameter' => $query
                ]);
            }
            return DocoHelpers::previewPdf($path);
            // $response = $this->_restRajal->get('inf-konsul-poli/export-pdf?id='. $id.'&jenis='.$jenis,[
            //     'save_to' => $path
            // ]);
            // $body = json_decode($response->getBody(), true);

            // return DocoHelpers::downloadPdf($response, $path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionApprove($id){
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);

        try {
            /* batal disini */
            $response = $this->_restRajal->request('POST', 'inf-konsul-poli/approve', ['form_params'=>
                    [
                        'konsulpoli_id' => $id,
                        'status_approve' => DocoConstants::VAR_STATUS_DAFTAR_OL_DISETUJUI //ambil dari lookup yg sama dengan pendaftaran online
                    ]
                ]);

            $body = json_decode($response->getBody(), true);
            // $return = ['response'=>$body['response']];
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}
