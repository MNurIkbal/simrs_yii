<?php

/**
 * @Author: Budi
 */

namespace app\modules\laboratorium\components\traits;

use Yii;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DHtml;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;

trait HasilLabTrait
{
   public function actionHasilLab($id)
   {
      $title = 'Hasil Pemeriksaan Laboratorium';
      $cache = Yii::$app->cache;
      $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
      $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
      $data = $this->getDataApiHasilLab($id);
      $data_pasien = ArrayHelper::getValue($data, 'data_pasien', []);
      $data_hasil_lab = ArrayHelper::getValue($data, 'data_hasil_lab', []);
      $status_periksa = ArrayHelper::getValue($data_pasien, 'status_periksa');
      $pasienmasukpenunjang_id = DocoHelpers::decrypt($id);
      $count_expertise = $data['count_data_expertise'];
      $catatan = ArrayHelper::getValue($data_hasil_lab, 'catatan_instruksi');
      $pendaftaran_id = DocoHelpers::encrypt($data_pasien['pendaftaran_id']);
      $pasienadmisi_id = !empty($data_pasien['pasienadmisi_id']) ? $data_pasien['pasienadmisi_id'] : null;
      $is_bayar = isset($data_pasien['is_status_bayar']) ? $data_pasien['is_status_bayar'] : false;
      $is_bayar = ($is_bayar) ? 1 : 0;
      $isFinish = false;
      if(!empty($status_periksa) && $status_periksa == DocoConstants::LAB_ST_PEN_SELESAI) {
         $isFinish = true;
      }
      $dataRender = ($isFinish) ? 'riwayat?id=' : 'pasien-lab?id=';
      $dataTarget = ($isFinish) ? '#view-riwayat' : '#view-non-rujukan';
      $dataTab = ($isFinish) ? 'tab-riwayat' : 'tab-non-rujukan';
      $isReferred = ArrayHelper::getValue($data_pasien, 'is_referred', false);
      $data_rujukan = $tmp_data_rujukan = $list_data_rujukan = [];
      if($isReferred){
         //Untuk mengelompokkan data
         $tmp_data_rujukan = !empty($data_pasien['data_rujukan']) ? $data_pasien['data_rujukan'] : [];
         foreach($tmp_data_rujukan as $val){
            $tglRujukan = ArrayHelper::getValue($val, 'tgl_rujukan');
            $keyTglRujukan = strtotime($tglRujukan);
            $alasanDirujuk = ArrayHelper::getValue($val, 'alasan_dirujuk');
            $rujukanKeluarId = ArrayHelper::getValue($val, 'rujukankeluar_id');
            $pegawaiId = ArrayHelper::getValue($val, 'pegawai_id');
            $daftarTindakanNama = ArrayHelper::getValue($val, 'daftartindakan_nama');
            $key = $keyTglRujukan.'-'.$rujukanKeluarId.'-'.$pegawaiId;

            if(!empty($list_data_rujukan[$key])){
               $tmpData = $list_data_rujukan[$key];
               $tmpDaftarTindakanNama = ArrayHelper::getValue($tmpData, 'daftartindakan_nama');
               if(!is_array($tmpDaftarTindakanNama)){
                  $arrDaftarTindakanNama[] = $tmpDaftarTindakanNama; //init
                  $tmpDaftarTindakanNama = $arrDaftarTindakanNama;
               }
               array_push($tmpDaftarTindakanNama, $daftarTindakanNama);

               $val['daftartindakan_nama'] = $tmpDaftarTindakanNama;
            }
            $list_data_rujukan[$key] = $val; 
         }
      }
      
      foreach($list_data_rujukan as $key => $val){
         $data_rujukan[]= $val;
      }
      
      $is_periksa = ($status_periksa == Dococonstants::LAB_ST_PEN_PERIKSA) ?  1 : 0;
      $isException = $data['is_exception'];
      return $this->renderAjax('components/pasien-lab/hasil-lab/index', get_defined_vars());
   }
    
   private function getDataApiHasilLab($id = null)
   {
      $id = DocoHelpers::decrypt($id);
      try {
         $loginpemakai_id = Yii::$app->user->identity->loginpemakai_id;
         $response = $this->_restLab->request('GET', 'hasil-lab/generate-api?id=' . $id .'&loginpemakai_id=' . $loginpemakai_id);
         $body = json_decode($response->getBody(),TRUE);
         $return = [
            'data_pasien' => $body['response']['data-pasien'],
            'data_hasil_lab' => $body['response']['data-hasil-lab'],
            'count_data_expertise' => $body['response']['count-data-expertise'],
            'is_exception' => $body['response']['is_exception'],
         ];
         return $return;
      } catch (RequestException $e) {
         echo $e->getMessage();
      } catch (\Exception $e) {
         echo $e->getMessage();
      }
   }

   public function actionGetDataHasilLab($id)
   {
      try {
         $request = Yii::$app->request;
         $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
         $response = $this->_restLab->request('get', 'hasil-lab/index?id=' . $id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
         $row = $cache = [];
         $body = json_decode($response->getBody(), true);
         $no = $request->get('start', 1);
         foreach ($body['response']['data'] as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['samplelab_id']);
            $pasienmasukpenunjang_id = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
            $pelayananId = !empty($value['tindakanpelayanan_id'])? DocoHelpers::encrypt($value['tindakanpelayanan_id']) : null;
            $value['primary'] = $primaryKey;
            $value['pelayananId'] = $pelayananId;
            $value['penunjang_id'] = $pasienmasukpenunjang_id;
            unset($value['samplelab_id']);
            $value['rowNum'] = $no;
            $value['is_expertise'] = empty($value['is_expertise']) ? '' : '&#10003;';
            $row[$key] = $value;
         }
         $return = [
            'data' => $row,
            'draw' => $request->get('draw'),
            'recordsTotal' => count($body['response']['data']),
            'recordsFiltered' => count($body['response']['data'])
         ];

         return DocoHelpers::response($return);
      } catch (RequestException $e) {
         return DocoHelpers::dataTabelsException($e->getMessage());
      } catch (\Exception $e) {
         return DocoHelpers::dataTabelsException($e->getMessage());
      }
   }

   public function actionCetakHasil($id = null, $penunjang_id)
   {
      $id = DocoHelpers::decrypt($id);
      $penunjang_id = DocoHelpers::decrypt($penunjang_id);
      $request = Yii::$app->request;
      $path = Yii::getAlias("@download") . "/cetak-pemeriksaan.pdf";
      $activeWorkspace = Yii::$app->session->get('active_workspace');
      $modulAlias = $activeWorkspace['modul_alias'];
      try {
         $response = $this->_restLab->get('input-hasil/cetak-hasil-pdf', [
            'save_to' => $path,
            'query' => [
               'pelayanan_id' => $id,
               'id' => $penunjang_id,
               'modul' => $modulAlias,
            ]
         ]);
         $body = json_decode($response->getBody(), true);
         return DocoHelpers::previewPdf($path, $response);
      } catch (RequestException $e) {
         throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
      } catch (\Exception $e) {
         throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
      }
   }

   public function actionCetak($id)
   {
      $id = DocoHelpers::decrypt($id);
      $request = Yii::$app->request;
      $path = Yii::getAlias("@download") . "/cetak-pemeriksaan.pdf";
      $activeWorkspace = Yii::$app->session->get('active_workspace');
      $modulAlias = $activeWorkspace['modul_alias'];
      try {
         $response = $this->_restLab->get('input-hasil/cetak-hasil-pdf', [
            'save_to' => $path,
            'query' => [
               'id' => $id,
               'modul' => $modulAlias,
            ]
         ]);
         $body = json_decode($response->getBody(), true);
         return DocoHelpers::previewPdf($path, $response);
      } catch (RequestException $e) {
         throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
      } catch (\Exception $e) {
         throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
      }
   }

   public function actionVerifikasi($id)
   {
      if(!is_numeric($id)) {
         $id = DocoHelpers::decrypt($id);
      }
      try {
         $response = $this->_restLab->request('GET', 'hasil-lab/verifikasi?id=' . $id);
         $body = json_decode($response->getBody(), true);
         if (!$body['response']) {
            $result['response']['title'] = Yii::t('fe', 'Tidak Berhasil !');
            $result['response']['text'] = Yii::t('fe', 'Masih ada pemeriksaan yang belum memiliki expertise');
            return DocoHelpers::response($result, 422);
         } else {
            $result['response']['text'] = Yii::t('fe', 'Data berhasil di verifikasi');
         }
         return DocoHelpers::response($result);
      } catch (RequestException $e) {
         echo $e->getMessage();
      } catch (\Exception $e) {;;
         echo $e->getMessage();
      }
   }

   public function actionUnverifikasi($id)
   {
      if(!is_numeric($id)) {
         $id = DocoHelpers::decrypt($id);
      }
      try {
         $response = $this->_restLab->request('GET', 'hasil-lab/unverifikasi?id=' . $id);
         $body = json_decode($response->getBody(), true);
         $result['response']['text'] = Yii::t('fe', 'Data berhasil di Unverifikasi');
         return DocoHelpers::response($result);
      } catch (RequestException $e) {
         echo $e->getMessage();
      } catch (\Exception $e) {
         echo $e->getMessage();
      }
   }
}