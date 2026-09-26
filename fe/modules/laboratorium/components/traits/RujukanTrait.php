<?php

/**
 * @Author: Budi
 */

namespace app\modules\laboratorium\components\traits;

use Yii;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\laboratorium\models\PasienMasukPenunjangForm;
use Doco\laboratorium\models\UpdateTanggalRujukan;
use app\modules\laboratorium\response\Pasienlab;
use app\modules\laboratorium\models\BatalOrderPenunjangForm;

trait RujukanTrait
{
    public function actionRujukan()
    {
        $title = DHtml::getTitleMenu();
		$title = !empty($title) ? $title : 'Informasi Pasien Rujukan Laboratorium';
        $response = $this->_restLab->get('inf-pasien-rujukan-lab/get-options');
        $resResponseBody = json_decode($response->getBody(), True);
        $resResponseBody = ArrayHelper::getValue($resResponseBody, 'response', []);
        $getCaraBayar = ArrayHelper::getValue($resResponseBody, 'cara_bayar', []);
        $legendCaraBayar = [];
        if (is_array($getCaraBayar)) {
            foreach ($getCaraBayar as $key => $value) {
                $legendCaraBayar[$key]['carabayar_nama'] = ArrayHelper::getValue($value, 'carabayar_nama', '');
                $legendCaraBayar[$key]['carabayar_kode_warna'] = ArrayHelper::getValue($value, 'carabayar_kode_warna', '');
            }
        }

        return $this->renderAjax('components/rujukan/index', get_defined_vars());
    }

    public function actionGetDataRujukan()
    {
    	Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $type = $request->get('type', null);
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['advanced-filter']['type'] = $type;

        // filter per ruangan lab
        $payload['advanced-filter']['ruanganpenunjang_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $response = $this->guzzleExec($this->_restLab, [
            'url' => "inf-pasien-rujukan-lab/index",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        // dump($response);die;
        foreach ($response['data'] as $key => $value) {
            $pemeriksaan = "";
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['pasienkirimkeunitlain_id']);
            $pemeriksaan = "";
            if(!empty($value['pemeriksaan'])) {
                foreach($value['pemeriksaan'] as $tindakan) {
                    $pemeriksaan .= '<p><b>' .$tindakan['pemeriksaanlab_nama'].'</b></p>' ;
                }
            }

            $no_sep = "-";
            if(!empty($value['no_sep'])) {
              $no_sep = explode('##', $value['no_sep']);
              $arr_nosep = [];
              for ($i=0; $i < count($no_sep); $i++) { 
                $arr_nosep[] = $no_sep[$i];
              }

              $no_sep = implode('<br>', $arr_nosep);
            }

            $response['data'][$key]['detail_diagnosa'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                'class' => 'btn btn-sm btn-success', 
                'data-source' => "/laboratorium/inf-pasien-rujukan-lab/detail-diagnosa?pasienkirimkeunitlain_id=".DocoHelpers::encrypt(ArrayHelper::getValue($value, 'pasienkirimkeunitlain_id')),'onclick'=> 'docoHelper.detail(this)']);
            $response['data'][$key]['pemeriksaan'] = $pemeriksaan;
            $response['data'][$key]['no_sep'] = $no_sep;
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionFormApproval($id)
    {
        $id = DocoHelpers::decrypt($id);
        $model = new PasienMasukPenunjangForm;
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id"); 
        $detail = $pemeriksaan = [];
        try {
            $response = $this->_restLab->get('inf-pasien-rujukan-lab/generate-api?id=' .$id. '&ruangan_id=' .$ruanganid);
            $body = json_decode($response->getBody(), true);
            $response = isset($body['response']) ? $body['response'] : [];
            $data = isset($response['labDetail']) ? $response['labDetail'] : [];
            $detail = isset($response['labDetail']) ? $response['labDetail'] : [];
            $penjaminId = !empty($data) ? DocoHelpers::encrypt($data['penjamin_id']) : null;
            $kelasPelayananId = !empty($data) ? DocoHelpers::encrypt($data['kelaspelayanan_id']) : null;
            $pemeriksaan = isset($response['listPemeriksaan']) ? $response['listPemeriksaan'] : [];
            $catatan_dokter = isset($data['catatan_dokterpengirim']) ? $data['catatan_dokterpengirim'] : null;
            $id_penunjang = $id;
            $dokter = ArrayHelper::map($response['listDokter'], 'pegawai_id', 'nama_pegawai');
            $catatan_dokterpengirim = $catatan_dokter;
            $model->catatan_dokterpengirim = $catatan_dokterpengirim;
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
        }
        return $this->renderAjax('components/rujukan/form_approval', get_defined_vars());
    }

    public function actionApprove()
    {
        $request = Yii::$app->request;
        $model = new PasienMasukPenunjangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $formData = $request->post(); 
                $form_params['pasienkirimkeunitlain_id'] = isset($formData['pasienkirimkeunitlain_id']) 
                    ? DocoHelpers::decrypt($formData['pasienkirimkeunitlain_id']) : null;
                $form_params['dokter_laboratorium'] = isset($formData['PasienMasukPenunjangForm']['pegawai_id']) 
                    ? $formData['PasienMasukPenunjangForm']['pegawai_id'] : null;
                $form_params['list_approved'] = isset($formData['list_approved']) 
                    ? json_decode($formData['list_approved'], true) : null;
                try {
                    $response = $this->_restLab->post('inf-pasien-rujukan-lab/proses-approve', [
                        'form_params' => $form_params
                    ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,$formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
 
        } else {
            return $this->renderAjax('form', get_defined_vars());
        }
    }

    private function getDataRujukan()
    {
        $response = $this->_restMaster->get('ruangan/generate-api');
        $body = json_decode($response->getBody(), true);

        $result = $body['response']['data'];

        return $result;
    }

    public function actionGetDokter($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $response = $this->_restLab->get('inf-pasien-rujukan-lab/get-dokter?advanced-filter[nama_pegawai]=' . $q);
        $body = json_decode($response->getBody(), true);
        $temp_dokter = array(); // array dokter temp
        foreach ($body['response']['data'] as $value)
            if(!array_key_exists($value['pegawai_id'],$temp_dokter)){
                $result['results'][] = [
                    'id' => $value['nama_pegawai'],
                    'text' => $value['nama_pegawai']
                ];
                $temp_dokter[$value['pegawai_id']] = $value['nama_pegawai'];
            }
        return $result;
    }

    public function actionGetInstalasi($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $result = [];
        $result['results'] = [];
        $response = $this->_restLab->get('inf-pasien-rujukan-lab/get-instalasi?advanced-filter[instalasi_nama]=' . $q);

        $body = json_decode($response->getBody(), true);
        $datas = $body['response']['data'];
        $temp_data = array();
        foreach ($datas as $value)
            if(!array_key_exists($value['instalasi_id'],$temp_data)){
                $result['results'][] = [
                    'id' => $value['instalasi_nama'],
                    'text' => $value['instalasi_nama']
                ];
                $temp_data[$value['instalasi_id']] = $value['instalasi_nama'];
            }
        return $result;
    }

    public function actionFormUpdate($id)
    {
        $request = Yii::$app->request;
        $model = new UpdateTanggalRujukan;
        $pasienRujukanId = DocoHelpers::decrypt($id);

        if (!empty($request->post())) {
            $model->load($request->post());
            $response = $this->_restLab->post('inf-pasien-rujukan-lab/update-kirim-unit',[
                'query' => [
                    'id' => $pasienRujukanId
                ],
                'form_params' => $model->attributes
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::response($body);
        }

        $responseLab = new Pasienlab;
        $response = $this->_restLab->get('inf-pasien-rujukan-lab/get-view',[
            'query' => [
                'id' => $pasienRujukanId
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        $responseLab->attributes = isset($body['response']) ? $body['response'] : [];
        return $this->renderAjax('components/rujukan/form-edit-rujukan', get_defined_vars());
    }

    public function actionGetDokterRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id"); 
        $response = $this->_restLab->get('inf-pasien-rujukan-lab/get-dokter?advanced-filter[nama_pegawai]=' . $q. '&advanced-filter[ruangan_id]='.$ruanganid);
        $body = json_decode($response->getBody(), true);
        foreach ($body['response']['data'] as $value)
            $result['results'][] = [
            'id' => $value['pegawai_id'],
            'text' => $value['nama_pegawai']
        ];
        return $result;
    }

    public function actionGetPegawaiRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id");
        $response = $this->_restMaster->get('pegawai/index?advanced-filter[nama_pegawai]=' . $q . '&advanced-filter[ruangan_id]=' . $ruanganid);
        $body = json_decode($response->getBody(), true);
        foreach ($body['response']['data'] as $value)
            $result['results'][] = [
            'id' => $value['pegawai_id'],
            'text' => $value['nama_pegawai']
        ];
        return $result;
    }

    public function actionFormBatal($id)
    {
      $id = DocoHelpers::decrypt($id);
      $model = new BatalOrderPenunjangForm;
      $detail = $pemeriksaan = [];
      try {
         $response = $this->_restLab->get('inf-pasien-rujukan-lab/generate-api?id=' . $id);
         $body = json_decode($response->getBody(), true);
         $data = $body['response']['labDetail'];
         $detail = $body['response']['labDetail'];
         $pemeriksaan = $body['response']['listPemeriksaan'];
         $dataBatal = $body['response']['dataBatal'];
         $dataPegawai = $body['response']['listDokter'];
         $dataPegawai = ArrayHelper::map($dataPegawai, 'pegawai_id', 'nama_pegawai');
         $model->tgl_batalorder = !empty($dataBatal['tgl_batalorder']) ? date('d-M-Y' , strtotime($dataBatal['tgl_batalorder'])) : date('d-M-Y');
         $model->alasan = @$dataBatal['alasan'];
      } catch (RequestException $e) {
         $cara_bayar = $penjamin = [];
      }
      return $this->renderAjax('components/rujukan/form_batal', get_defined_vars());
    }

   public function actionBatalOrder()
   {
      $request = Yii::$app->request;
      $model = new BatalOrderPenunjangForm;
      $formName = substr(strrchr(get_class($model), "\\"), 1);
      if ($request->post()) {
         $model->load($request->post());
         if ($model->validate()) {
            $formData = $request->post();
            $form_params['pasienkirimkeunitlain_id'] = DocoHelpers::decrypt($formData['pasienkirimkeunitlain_id']);
            $form_params['disetujui_oleh'] = $formData['BatalOrderPenunjangForm']['peg_menyetujui_id'];
            $form_params['tanggal_batal'] = date('Y-m-d H:i:s');
            $form_params['alasan_pembatalan'] = $formData['BatalOrderPenunjangForm']['alasan'];
            $form_params['list_batal'] = isset($formData['list_batal']) 
               ? json_decode($formData['list_batal'], true) : null;
            $response = $this->_restLab->post('inf-pasien-rujukan-lab/proses-batal', [
               'form_params' => $form_params
            ]);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response($response,false,$formName);

         } else {
            $errors = DocoHelpers::parseError($model->errors, $formName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
         }
      } 
   }

   public function actionGetDataPemeriksaan($id)
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $id = DocoHelpers::decrypt($id);
      $request = Yii::$app->request;
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      $yiiRestfulParams['id'] = $id;
      $draw = $request->get('draw', 1);
      $data = [];
      if ($yiiRestfulParams['per-page'] <= 0) {
         $yiiRestfulParams['per-page'] = 1000;
      }
      $response = $this->_restLab->get('inf-pasien-rujukan-lab/get-pemeriksaan-view', [
         'form_params' => [],
         'query' => $yiiRestfulParams
      ]);
      $body = json_decode($response->getBody(), true);
      $no = $request->get('start', 1);
      foreach ($body['response']['data'] as $key => $value) {
         $no++;
         $primaryKey = DocoHelpers::encrypt($value['pasienkirimkeunitlain_id']);
         $value['primary'] = $primaryKey;
         unset($value['pasienkirimkeunitlain_id']);
         $value['rowNum'] = $no;
         $value['is_checkbox'] = '';
         if($value['is_cyto']){
               $value['is_checkbox'] = '<input type="checkbox" class="checkbox" checked disabled >';
         }
         $data[$key] = $value;
      }
      $return = [
         'data' => $data,
         'draw' => $request->get('draw'),
         'recordsTotal' => $body['response']['_meta']['totalCount'],
         'recordsFiltered' => $body['response']['_meta']['totalCount']
      ];
      return DocoHelpers::response($return);
   }

   public function actionDetailDiagnosa()
   {
        $request = Yii::$app->request;
        $pasienkirimkeunitlain_id = $request->get('pasienkirimkeunitlain_id');

        try {
            $response = $this->_restLab->get('inf-pasien-rujukan-lab/detail-diagnosa?pasienkirimkeunitlain_id='.$pasienkirimkeunitlain_id);
            $body = json_decode($response->getBody(), true);
            $result = ArrayHelper::getValue($body, 'response', []);
            $diagnosa_utama = ArrayHelper::getValue($result, 'diagnosa_utama', '-');
            $diagnosa_penyerta = ArrayHelper::getValue($result, 'diagnosa_penyerta', []);

            return $this->renderAjax('components/rujukan/_detail_paket', compact('diagnosa_utama', 'diagnosa_penyerta'));
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
   }
}