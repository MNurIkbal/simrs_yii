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
use app\modules\laboratorium\response\Pasienlab;

trait PasienLabTrait
{
   public function actionPasienLab()
   {
      $title = DHtml::getTitleMenu();
      $title = !empty($title) ? $title : 'Informasi Pasien Laboratorium';
      $data = [];
      try {
         $response = $this->_restLab->get('inf-pasien-rujukan-lab/get-options');
         $body = json_decode($response->getBody(), true);
         $data = isset($body['response']) ? $body['response'] : [];
      } catch (RequestException $e) {
         $data = [];
      }
      
      $getCaraBayar = ArrayHelper::getValue($data, 'cara_bayar', []);
      $legendCaraBayar = [];
      if (is_array($getCaraBayar)) {
         foreach ($getCaraBayar as $key => $value) {
            $legendCaraBayar[$key]['carabayar_nama'] = ArrayHelper::getValue($value, 'carabayar_nama', '');
            $legendCaraBayar[$key]['carabayar_kode_warna'] = ArrayHelper::getValue($value, 'carabayar_kode_warna', '');
         }
      }
      
      return $this->renderAjax('components/pasien-lab/index', get_defined_vars());
   }

   public function actionGetDataPasienLab()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $type = $request->get('type', null);
      $payload = DocoDatatableHelper::advancedFilterParam();
      $payload['advanced-filter']['type'] = $type;
      $payload['advanced-filter']['tab'] = 'pasien-lab';
      $payload['advanced-filter']['is_referred'] = $request->get('is_referred');

      // filter per ruangan lab
      $payload['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
      $response = $this->guzzleExec($this->_restLab, [
         'url' => "inf-pasien-lab/index",
         'payload' => [
            'query' => $payload,
         ]
      ]);
      if(!empty($response['data'])) {
         foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['pasienmasukpenunjang_id']);
            $color = '#FFFfff';
            $font = 'black';
            $fontStatusBayar = 'black';
            $colorStatusBayar = '#FFFfff';
            $received_flag = !empty($value['received_flag']) ? $value['received_flag'] : false;
            $is_hasil = !empty($value['is_hasil']) ? $value['is_hasil'] : false;
            if ($value['status_periksa'] == DocoConstants::LAB_ST_PEN_BATAL) {
               $color = '#d64541';
               $font = 'white';
            } else if (($value['status_periksa'] == DocoConstants::LAB_ST_PEN_SELESAI) || ($received_flag && $is_hasil)) {
               $color = '#26A65B';
               $font = 'white';
               $value['status_periksa_nama'] = 'SELESAI';
            } else if(($value['status_periksa'] == DocoConstants::LAB_ST_PEN_PERIKSA) || ($received_flag && !$is_hasil)) {
               $color = '#05bbbe';
               $font = 'white';
               $value['status_periksa_nama'] = 'PERIKSA';
            } else if($value['status_periksa'] == DocoConstants::LAB_ST_PEN_AMBILSAMPLE) {
               $color = '#F5D76E';
               $font = 'black';
            }
            if ($value['is_status_bayar'] == true) {
               $colorStatusBayar = '#26A65B';
               $fontStatusBayar = 'white';
            }
            $response['data'][$key]['status_periksa_nama'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'; font-weight:bold;">'.$value['status_periksa_nama'].'</span>';
            $response['data'][$key]['status_bayar'] = '<span class="badge" style="background: '.$colorStatusBayar.'; color: '.$fontStatusBayar.'; font-weight:bold;">'.strtoupper($value['status_bayar']).'</span>';
            $pemeriksaan = "";
            if(!empty($value['pemeriksaan'])) {
               foreach($value['pemeriksaan'] as $tindakan) {
                   $pemeriksaan .= '<p><b>' .$tindakan['pemeriksaanlab_nama'].'</b></p>' ;
               }
           }
           $response['data'][$key]['pemeriksaan'] = $pemeriksaan;
         }
      }
      
      $response['recordsTotal'] = $response['_meta']['totalCount'];
      $response['recordsFiltered'] = $response['_meta']['totalCount'];
      return $response;
   }

   private function getAksiListPasien($data)
   {
      $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);
      $return_data = '';
      if(!empty($data['no_antrian'])){
         $return_data .= Html::button(
            '<i class="fa fa-volume-up"></i> '.$data['no_antrian'] ,
            [
               'class' => 'btn btn-turquoise btn-xs-new antrian',
               'data-tooltip' => "tooltip",
               'data-original-title' => Yii::t('fe', 'Panggil antrian'),
               'data-id' => $data['pendaftaran_id'],
               'data-antrian' => $data['no_antrian'],
            ]
         );
      }
      $return_data .= '&nbsp;&nbsp;';
      return $return_data;
   }

   public function actionPrintRincian($id)
    {
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/_rincian_tagihan_lab.pdf";
        try {
            $response = $this->_restLab->get('inf-pasien-lab/print-rincian?id='.$id, ['save_to' => $path]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

   public function actionFormEditPemeriksaan($noRegis, $id)
   {
      $statusBelumPeriksa = DocoConstants::LAB_ST_PEN_BELUMPERIKSA;
      $response = $this->_restLab->get('inf-pasien-lab/pasien-lab-view',[
         'query' => [
            'id' =>  DocoHelpers::decrypt($id)
         ]
      ]);
      $body = json_decode($response->getBody(), true);
      $response = isset($body['response']) ? $body['response'] : [];
      $noRegis = isset($response['no_pendaftaran']) ? $response['no_pendaftaran'] : null;
      $responseLab = new Pasienlab;
      $responseLab->attributes = isset($body['response']) ? $body['response'] : [];
      $pasienmasukpenunjang_id = $id;
      $no_masukpenunjang = $responseLab->no_masukpenunjang;
      $path = Yii::$app->docoPlugin->execute($this, 'form_edit_pemeriksaan');
      return $this->renderAjax($path, get_defined_vars());
   }

   public function actionGetDataPemeriksaanLab($id)
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $id = DocoHelpers::decrypt($id);
      $request = Yii::$app->request;
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      $yiiRestfulParams['id'] = $id;
      $draw = $request->get('draw', 1);
      $data = [];
      try {
         $response = $this->_restLab->get('inf-pasien-lab/get-pemeriksaan-view', [
            'form_params' => [],
            'query' => $yiiRestfulParams
         ]);
         $body = json_decode($response->getBody(), true);
         $no = $request->get('start', 1);
         foreach ($body['response']['data'] as $key => $value) {
               $no++;
               $primaryKey = DocoHelpers::encrypt($value['tindakanpelayanan_id']);
               $value['primary'] = $primaryKey;
               $value['tp'] = $value['tindakanpelayanan_id'];
               unset($value['tindakanpelayanan_id']);
               $value['rowNum'] = $no;
               $color = '#FFFfff';
               $font = 'black';
               $received_flag = !empty($value['received_flag']) ? $value['received_flag'] : false;
               $is_hasil = !empty($value['is_hasil']) ? $value['is_hasil'] : false;
               if (strtolower($value['status_periksa']) == 'batal') {
                  $color = '#D24D57';
                  $font = 'white';
               } else if ($received_flag && $is_hasil) {
                  $color = '#26A65B';
                  $font = 'white';
                  $value['status_periksa'] = 'SELESAI';
               } else if($received_flag && !$is_hasil) {
                  $color = '#2574A9';
                  $font = 'white';
                  $value['status_periksa'] = 'DIPERIKSA';
               } else if(strtolower($value['status_periksa']) == 'ambil sampel') {
                  $color = '#F5D76E';
                  $font = 'white';
               }
               $value['status_periksa_btn'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'">'.$value['status_periksa'].'</span>';

               if (strtolower($value['status_bayar']) == 'batal bayar') {
                  $color = '#D24D57';
                  $font = 'white';
               } else if (strtolower($value['status_bayar']) == 'sudah bayar') {
                  $color = '#26A65B';
                  $font = 'white';
               }
               $value['status_bayar_btn'] = '<span class="badge" style="background: '.$color.'; color: '.$font.'">'.$value['status_bayar'].'</span>';

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
         $result['error'] = $e->getMessage();
         return $result;
      } catch (\Exception $e) {
         $result['error'] = $e->getMessage();
         return $result;
      }
   }

   public function actionBatalPemeriksaanLab($noRegis)
   {
      $request = Yii::$app->request;
      if ($request->post()) {
         $formData = $request->post(); 
         $pasienmasukpenunjang_id= DocoHelpers::decrypt(isset($formData['pasienmasukpenunjang_id']) ? $formData['pasienmasukpenunjang_id'] : null);
         $no_masukpenunjang= isset($formData['no_masukpenunjang']) ? $formData['no_masukpenunjang'] : null;
         $is_all = isset($formData['is_all']) ? json_decode($formData['is_all'], true) : null;
         $form_params['pasienmasukpenunjang_id']= $pasienmasukpenunjang_id;
         $form_params['detail_tindakan'] = isset($formData['list_tindakan']) ? json_decode($formData['list_tindakan'], true) : null;
         $form_params['is_all'] = $is_all;
         $form_params['no_pendaftaran'] =  $noRegis;
         $form_params['no_masukpenunjang'] =  $no_masukpenunjang;
         try {
            $response = $this->_restLab->post('inf-pasien-lab/batal-pemeriksaan', [
               'form_params' => $form_params
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
         } catch (RequestException $e) {
            return DocoHelpers::response($e->getMessage());
         } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
         }
      }
      else{
         return DocoHelpers::response([
            'response'=>[
               'title'=>"Proses Gagal", 
               'text'=>'Parameter tidak dikenali'
            ]
         ]);
      }
   }

   public function actionGetDokterLab()
   {
      return $this->guzzleExec($this->_restLab, [
         'url' => 'inf-pasien-lab/get-dokter-lab-table',
         'method' => 'get',
         'payload' => [
            'query' => array_merge(Yii::$app->request->get('payload', []), [
               'type' => 'selectScroll'
            ])
         ],
         'returnResponse' => true
      ]);
   }

   public function actionUpdateDokterLab()
   {
      $request = Yii::$app->request;
      $formData = $request->post();
      $form_params['pasienmasukpenunjang_id'] = ArrayHelper::getValue($formData, 'pasienmasukpenunjang_id');
      $form_params['pasienkirimkeunitlain_id'] = ArrayHelper::getValue($formData, 'pasienkirimkeunitlain_id');
      $form_params['pendaftaran_id'] = ArrayHelper::getValue($formData, 'pendaftaran_id');
      $form_params['pasienadmisi_id'] = ArrayHelper::getValue($formData, 'pasienadmisi_id');
      $form_params['pegawai_id'] = ArrayHelper::getValue($formData, 'pegawai_id');
      try {
         return $this->guzzleExec($this->_restLab, [
            'url' => 'inf-pasien-lab/update-dokter-lab',
            'method' => 'POST',
            'returnResponse' => true,
            'payload' => [
               'form_params' => $form_params
            ]
         ]);
      } catch (RequestException $e) {
         return DocoHelpers::response($e->getMessage());
      } catch (\Exception $e) {
         return DocoHelpers::responseTemplate(500, $e->getMessage());
      }
   }
}