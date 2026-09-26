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
use Doco\laboratorium\models\SpecimentForm;
use GuzzleHttp\Exception\RequestException;

trait SpecimentTrait
{
   public function actionSpeciment()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $title = 'Input Speciment Laboratorium';
      $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
      $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
      $cache = Yii::$app->cache;
      $data = $this->getDataApi($id);
      $data_pasien = !empty($data['data_pasien']) ? $data['data_pasien'] : [];
      $status_periksa = !empty($data_pasien['status_periksa']) ? $data_pasien['status_periksa'] : null;
      $is_ambil_sample = ($status_periksa == Dococonstants::LAB_ST_PEN_AMBILSAMPLE) ? 1 : 0;
      $pasienmasukpenunjang_id = DocoHelpers::decrypt($id);
      $count_tindakan = !empty($data['data_pasien_detail']) ? count($data['data_pasien_detail']) : 0;
      $transLab = $cache->get('addSampleLab' . $ruangan_id . '-' . $pegawai_id . $pasienmasukpenunjang_id);
      if ($transLab === false) {
         $list_cache = [];
         $list_sample = json_encode($list_cache);
         $cache->set('addSampleLab'.  $ruangan_id . '-' . $pegawai_id . $pasienmasukpenunjang_id, $list_sample);
      }
      $transLab = $cache->get('addSampleLab' . $ruangan_id . '-' . $pegawai_id . $pasienmasukpenunjang_id);
      return $this->renderAjax('components/pasien-lab/speciment/index', get_defined_vars());
   }

   private function getDataApi($id = null)
   {
      $id = DocoHelpers::decrypt($id);
      try {
         $response = $this->_restLab->request('GET', 'speciment/generate-api?id=' . $id);
         $body = json_decode($response->getBody(),TRUE);
         $return = [
               'data_pasien' => $body['response']['data-pasien'],
               'data_pasien_detail' => $body['response']['data-pasien-detail'],
               'data_sample' => $body['response']['data-sample'],
               'data_satuan' => $body['response']['data-satuan'],
         ];
         return $return;
      } catch (RequestException $e) {
         echo $e->getMessage();
      } catch (\Exception $e) {
         echo $e->getMessage();
      }
   }
    
   public function actionGetDataSpeciment($id)
   {
      try {
         $request = Yii::$app->request;
         $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
         $response = $this->_restLab->request('get', 'speciment/index?id=' . $id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
         $row = $cache = [];
         $body = json_decode($response->getBody(), true);
         $no = $request->get('start', 1);
         $count = 0;
         if(!empty($body['response']['data'])) {
               $count = count($body['response']['data']);
               foreach ($body['response']['data'] as $key => $value) {
                  $no++;
                  $value['rowNum'] = $no;
                  $row[$key] = $value;
               }
         }
         
         $return = [
               'data' => $row,
               'draw' => $request->get('draw'),
               'recordsTotal' => $count,
               'recordsFiltered' => $count
         ];

         return DocoHelpers::response($return);
      } catch (RequestException $e) {
         return DocoHelpers::dataTabelsException($e->getMessage());
      } catch (\Exception $e) {
         return DocoHelpers::dataTabelsException($e->getMessage());
      }
   }

   public function actionSaveSpeciment()
   {
      $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
      $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
      $request = Yii::$app->request;
      $post = $request->post();
      try {
         $cacheTrans = Yii::$app->cache->get('addSampleLab' . $ruangan_id . '-' . $pegawai_id . $post['id']);
         $transLab = json_decode($cacheTrans, true);
         $response = $this->_restLab->request('POST', 'speciment/save', [
               'form_params' => $transLab
         ]);
         $response = json_decode($response->getBody(), true);
         if ($response['metadata']['status'] == 200) {
               $codeHttp = $response['metadata']['status'];
               Yii::$app->cache->delete('addSampleLab' . $ruangan_id . '-' . $pegawai_id . $post['id']);
               Yii::$app->cache->delete('urutSample' . $ruangan_id . '-' . $pegawai_id . $post['id']);
         }
         return DocoHelpers::response($response, $codeHttp);
      } catch (RequestException $e) {
         echo DocoHelpers::dataTabelsException($e->getMessage());
      } catch (\Exception $e) {
         echo DocoHelpers::dataTabelsException($e->getMessage());
      }
   }

   public function actionAddSpeciment($id)
   {
      $model = new SpecimentForm;
      $data = $this->getDataApi($id);
      $instalasi_id = isset($data['data_pasien']['instalasi_id']) ? $data['data_pasien']['instalasi_id'] : null;
      $required_tindakan = ($instalasi_id == DocoConstants::INSTALASI_MCU) ? '' : 'required';
      $required_paket = ($instalasi_id == DocoConstants::INSTALASI_MCU) ? 'required' : '';
      $tanggal_masuk = !empty($data['data_pasien']['tglmasukpenunjang'])
         ? $data['data_pasien']['tglmasukpenunjang']
         : date('d F Y H:i:s');

      $pasienmasukpenunjang_id = DocoHelpers::decrypt($id);
      $data_sample = !empty($data['data_sample']) 
         ? ArrayHelper::map($data['data_sample'], 'samplelab_id', 'nama_sample')
         : [];

      $data_satuan = !empty($data['data_satuan']) 
         ? ArrayHelper::map($data['data_satuan'], 'satuanlab_id', 'satuanlab_nama')
         : [];

      $list_pemeriksaan = $list_non_paket = $list_paket = [];
      if (!empty($data['data_pasien_detail'])) {
         foreach ($data['data_pasien_detail'] as $key => $value) {
            $list_pemeriksaan[$value['tindakanpelayanan_id'] . '-' .$value['daftartindakan_id']] = $value['daftartindakan_nama'];
         }
         foreach ($data['data_pasien_detail'] as $key => $value) {
            if ($value['jenis'] == 'NON_PAKET') {
               $list_non_paket[] = [
                  'jenis' => $value['jenis'],
                  'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                  'daftartindakan_id' => $value['daftartindakan_id'],
                  'daftartindakan_nama' => $value['daftartindakan_nama'],
                  'tipepaket_nama' => $value['tipepaket_nama'],
                  'jenispemeriksaanlab_nama' => $value['jenispemeriksaanlab_nama'],
               ];
            } elseif ($value['jenis'] == 'PAKET' || $value['jenis'] == 'PAKET_MCU') {
               if($instalasi_id == DocoConstants::INSTALASI_MCU) {
                  if(!empty($value['detail_2'])) {
                     $list_paket[$value['tipepaket_nama']][$value['detail_2']][] = [
                        'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                        'daftartindakan_id' => $value['daftartindakan_id'],
                        'daftartindakan_nama' => $value['daftartindakan_nama'],
                        'jenispemeriksaanlab_nama' => $value['jenispemeriksaanlab_nama'],
                     ];
                  }
                  else {
                     $list_non_paket[] = [
                        'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                        'daftartindakan_id' => $value['daftartindakan_id'],
                        'daftartindakan_nama' => $value['daftartindakan_nama'],
                        'tipepaket_nama' => $value['tipepaket_nama'],
                        'jenispemeriksaanlab_nama' => $value['jenispemeriksaanlab_nama'],
                     ];
                  }
               }
               else {
                  $list_paket[$value['tipepaket_nama']][] = [
                     'jenis' => $value['jenis'],
                     'tindakanpelayanan_id' => $value['tindakanpelayanan_id'],
                     'daftartindakan_id' => $value['daftartindakan_id'],
                     'daftartindakan_nama' => $value['daftartindakan_nama'],
                     'tipepaket_nama' => $value['tipepaket_nama'],
                     'detail_2' => $value['detail_2'],
                     'jenispemeriksaanlab_nama' => $value['jenispemeriksaanlab_nama'],
                  ];
               }
            }
         }
      }
      $listPaket = $this->generatePaket($list_paket);
      return $this->renderAjax('components/pasien-lab/speciment/form_add', get_defined_vars());
   }

   /**
    * @todo save sample to cache
    * @author Randy Vianda Putra <randy@docotel.com>
    */
   public function actionSaveCacheSpeciment()
   {
      $model = new SpecimentForm;
      $request = Yii::$app->request;
      $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
      $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
      $post = $request->post();
      $data = $post['SpecimentForm'];
      try {
         $model->load($post);
         $model->scenario = ($post['instalasi_id'] == DocoConstants::INSTALASI_MCU) ? 'paket' : 'lab';
         if($post['instalasi_id'] == DocoConstants::INSTALASI_MCU) {
            $model->list_paket = $model->pemeriksaan;
         }
         if ($model->validate()) {
               $keyId = $ruangan_id . '-' . $pegawai_id . $data['pasienmasukpenunjang_id'];
               $keyData = 'addSampleLab' . $keyId;
               $keyNomor = 'urutSample' . $keyId;
               $count = count($data['pemeriksaan']);
               $posisi = 0;
               $transLab = [];
               $cacheTrans = Yii::$app->cache->get($keyData);
               $transLab = json_decode($cacheTrans, true);
               $no_urut = Yii::$app->cache->get($keyNomor);
               if ($no_urut === false || $no_urut < 1) {
                  $start_urut = 0;
                  Yii::$app->cache->set($keyNomor, $start_urut);
               }
               $no_urut = Yii::$app->cache->get($keyNomor);
               if (!isset($post['posisi'])) {
                  $posisi = $no_urut + 1;
               }

               $tanggal = DocoHelpers::convertIndoToEnglish($data['tanggal'], true);
               $return = [
                  'posisi' => $posisi,
                  'pasienmasukpenunjang_id' => $data['pasienmasukpenunjang_id'],
                  'pegawai_id' => $pegawai_id,
                  'nama_sample' => $data['nama_sample'],
                  'satuan_nama' => $data['satuan_nama'],
                  'satuanlab_id' => $data['satuanlab_id'],
                  'samplelab_id' => $data['samplelab_id'],
                  'group' => ($count > 1) ? explode(',', $data['group']) : $data['group'],
                  'tindakanpelayanan_id' => ($count > 1) ? explode(',', $data['tindakanpelayanan_id']) : $data['tindakanpelayanan_id'],
                  'daftartindakan_id' => ($count > 1) ? explode(',', $data['daftartindakan_id']) : $data['daftartindakan_id'],
                  'nama_pemeriksaan' => ($count > 1) ? implode('<br> ', $data['pemeriksaan']) : $data['pemeriksaan'],
                  'jml_pemeriksaan' => $count,
                  'jumlah' => $data['jumlah'],
                  'keterangan' => $data['keterangan'],
                  'tgl' => date('d M Y', strtotime($tanggal)),
                  'jam' => date('H:i:s', strtotime($tanggal)),
                  'tanggal' => $tanggal
               ];
               Yii::$app->cache->delete($keyNomor);
               Yii::$app->cache->set($keyNomor, $posisi);
               $transLab[$posisi] = $return;
               $listTrans = json_encode($transLab);
               Yii::$app->cache->set($keyData, $listTrans);
               $result['data'] = $transLab[$posisi];
               $result['message'] = Yii::t('fe', 'Data berhasil di simpan');
               return DocoHelpers::response($result);
         } else {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, 'SpecimentForm');
         }
      } catch (RequestException $e) {
          echo DocoHelpers::dataTabelsException($e->getMessage());
      } catch (\Exception $e) {
          echo DocoHelpers::dataTabelsException($e->getMessage());
      }
   }


   public function actionDeleteCacheSpeciment()
   {
      $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
      $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
      $transLab = $listTrans = json_encode([]);
      $request = Yii::$app->request;
      $post = $request->post();
      try {
         $result = $cache = false;
         $cacheTrans = Yii::$app->cache->get('addSampleLab' . $ruangan_id . '-' . $pegawai_id . $post['penunjang_id']);
         if ($cacheTrans !== false) {
            $transLab = json_decode($cacheTrans, true);
            if (isset($transLab[$post['id']])) {
               unset($transLab[$post['id']]);
               if (!count($transLab)) {
                  Yii::$app->cache->delete('addSampleLab' . $ruangan_id . '-' . $pegawai_id . $post['penunjang_id']);
                  Yii::$app->cache->delete('urutSample' . $ruangan_id . '-' . $pegawai_id . $post['penunjang_id']);
                  $cache = true;
               }
               $listTrans = json_encode($transLab);
               $result = true;
            }
         }
         $listTrans = json_encode($transLab);
         Yii::$app->cache->set('addSampleLab' . $ruangan_id . '-' . $pegawai_id . $post['penunjang_id'], $listTrans);
         $return['message'] = 'Success';
         return DocoHelpers::response($return);
      } catch (RequestException $e) {
         echo DocoHelpers::dataTabelsException($e->getMessage());
      } catch (\Exception $e) {
         echo DocoHelpers::dataTabelsException($e->getMessage());
      }
   }

   private function generatePaket($list_paket)
   {
      $html = '';
      if(!empty($list_paket)) {
         foreach ($list_paket as $header => $detail_header) {
            $html .= '<li><a>'. $header .'</a>';
            if(!empty($detail_header)) {
               foreach ($detail_header as $key => $detail2) {
                  if(!isset($detail2[0])) {
                     $jenispemeriksaanlab_nama = $detail2['jenispemeriksaanlab_nama'];
                     $disabled = ($jenispemeriksaanlab_nama != null) ? '' : 'disabled';
                     $daftartindakan_nama = isset($detail2['daftartindakan_nama']) ? $detail2['daftartindakan_nama'] : '';
                     $tindakanpelayanan_id = isset($detail2['tindakanpelayanan_id']) ? $detail2['tindakanpelayanan_id'] : '';
                     $daftartindakan_id = isset($detail2['daftartindakan_id']) ? $detail2['daftartindakan_id'] : '';
                     $html .= '<br/>';
                     $html .= '<input 
                        type="checkbox"
                        name="SpecimentForm[pemeriksaan][]"
                        value="' . $daftartindakan_nama . '" 
                        '.$disabled.' 
                        data-val="' . $tindakanpelayanan_id . '"
                        data-tindakan="' . $daftartindakan_id . '"
                        data-group="'. $tindakanpelayanan_id . '-' . $daftartindakan_id .'"
                     > '.$daftartindakan_nama;
                  }
                  else {
                     if(!empty($key)) {
                        $html .= '<ul><li><a>'. $key .'</a><ul>';
                        if(!empty($detail2) && is_array($detail2)) {
                           $html .= '<li style="list-style: none;">';
                           foreach ($detail2 as $detail3) {
                              $jenispemeriksaanlab_nama = $detail3['jenispemeriksaanlab_nama'];
                              $disabled = ($jenispemeriksaanlab_nama != null) ? '' : 'disabled';
                              $daftartindakan_nama = isset($detail3['daftartindakan_nama']) ? $detail3['daftartindakan_nama'] : '';
                              $tindakanpelayanan_id = isset($detail3['tindakanpelayanan_id']) ? $detail3['tindakanpelayanan_id'] : '';
                              $daftartindakan_id = isset($detail3['daftartindakan_id']) ? $detail3['daftartindakan_id'] : '';
                              $html .= '<input 
                                       type="checkbox"
                                       name="SpecimentForm[pemeriksaan][]"
                                       value="' . $daftartindakan_nama . '" 
                                       '.$disabled.' 
                                       data-val="' . $tindakanpelayanan_id . '"
                                       data-tindakan="' . $daftartindakan_id . '"
                                       data-group="'. $tindakanpelayanan_id . '-' . $daftartindakan_id .'"
                                 > '.$daftartindakan_nama . 
                              '<br>';
                           }
                           $html .= '</li>';
                        }
                        $html .= '</ul></li></ul>';
                     }
                  }
               }
            }
            $html .= '</li>';
         }
      }
      return $html;
   }

   public function actionBatalSpeciment(){
      $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
      $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
      $request = Yii::$app->request;
      $post = $request->post();
      $pasienmasukpenunjang_id = $request->post('id', null);
      $params = [
         'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
         'pegawai_id' => $pegawai_id
      ];
      try {
         $response = $this->_restLab->request('POST', 'speciment/batal', [
               'form_params' => $params
         ]);
         $response = json_decode($response->getBody(), true);
         if ($response['metadata']['status'] == 200) {
               $codeHttp = $response['metadata']['status'];
         }
         return DocoHelpers::response($response, $codeHttp);
      } catch (RequestException $e) {
         Yii::error([
            "messageRequestException" => $e->getMessage()
         ]);
      } catch (\Exception $e) {
         Yii::error([
            "Exception" => $e->getMessage()
         ]);
      }
   }
}