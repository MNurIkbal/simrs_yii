<?php

namespace app\modules\bedah\components\traits;

use Yii;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;
use yii\web\Response;
use yii\helpers\Html;

trait VerifikasiTagihanTrait
{
   /**
    * Function Verifikasi Tagihan
    * 
    * @param String id
    * @return View
    * @author : Rizqi Fitrianto (rizqi@docotel.com)
    * @modified : Budi
    * A product of PT. Docotel Teknologi
    * Powered by Sirs
    */
   public function actionVerifikasiTagihan()
   {
      $request = Yii::$app->request;
      $pasienpenunjangId = $request->get('id', null);
      $response = $this->helper->guzzleExec($this->_restBedah, [
         'method' => 'post',
         'url' => 'verifikasi-tagihan/save-log-verifikasi',
         'payload' => [
            'form_params' => [
               'pasienpenunjangId' => $pasienpenunjangId,
            ]
         ]
      ]);
      $status_periksa = isset($response['status_periksa']) ? $response['status_periksa'] : null;
      $isStopAkomodasi = isset($response['is_stop_akomodasi']) ? $response['is_stop_akomodasi'] : false;
      return $this->renderAjax('detail-partial/_verifikasitagihan', compact('pasienpenunjangId', 'status_periksa','isStopAkomodasi'));
   }
   
   /**
    * Function Simpan Verifikasi Tagihan
    * 
    * @param String id
    * @return Array 
    * @author : Rizqi Fitrianto (rizqi@docotel.com)
    * @modified : Budi
    * @modified : Dede Herdiana
    * A product of PT. Docotel Teknologi
    * Powered by Sirs
    */
   public function actionSimpanVerifikasi()
   {
      $request = Yii::$app->request;
      $pasienpenunjangId = $request->get('id', null);
      $verifikasibedah = $request->post('verifikasibedah', null);
      $response = $this->_restBedah->post('verifikasi-tagihan/save-verifikasi', [
         'form_params' => [
            'pasienpenunjangId' => $pasienpenunjangId,
            'verifikasibedah' => $verifikasibedah,
         ]
      ]);
      $response = json_decode($response->getBody(), true);
      return DocoHelpers::response($response);
   }

   public function actionBatalVerifikasi()
   {
      $request = Yii::$app->request;
      $nomasuk_penunjang = $request->get('id_penunjang');
      try {
         $response = $this->_restBedah->post('verifikasi-tagihan/batal-verifikasi',['form_params' => [
            'no_masukpenunjang' => $nomasuk_penunjang,
         ]]);
         $response = json_decode($response->getBody(), TRUE);
         if($response['metadata']['status'] == 422){
            throw new \Exception();
         }
         return DocoHelpers::response($response);
      } catch (RequestException $e) {
         $response['error'] = $e->getMessage();
         return $response;
      } catch (\Exception $e) {
         return DocoHelpers::responseTemplate(422, 'Error', $response['response']);
      }
   }

   public function actionGetDataVerifikasi()
   {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $request = Yii::$app->request;
      $pasienPenunjangId = $request->get('pasienmasukpenunjang_id');
      $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
      $yiiRestfulParams['pasienpenunjangId'] = $pasienPenunjangId;
      $draw = $request->get('draw', 1);
      $data = [];

      $result = $subTotal = $persentaseOperator = $qtyOperator = [];
      $result['data'] = $data;
      $result['draw'] = $draw;
      $result['recordsTotal'] = 0;
      $result['recordsTotal'] = 0;
      try {
         $response = $this->_restBedah->get('verifikasi-tagihan/get-data-bills?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
         $body = json_decode($response->getBody(), True);
         $data = isset($body['response']['data']) ? $body['response']['data'] : [];
         $status_operasi = isset($body['response']['status_operasi']) ? $body['response']['status_operasi'] : [];
         $status_operasi = isset($status_operasi['status_periksa']) ? $status_operasi['status_periksa'] : null;
         $no = $request->get('start',1);
         $posisiOperator = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;
         foreach ($data as $key => $value) {
            $no++;
            $primary = isset($value['id']) ? $value['id'] : null;
            $qty = isset($value['qty']) ? $value['qty'] : null;
            $is_cyto = ($value['is_cyto'] == true) ? 1 : 0;
            $is_penyulit = ($value['is_penyulit'] == true) ? 1 : 0;
            $useprice = ($value['useprice']) ? $value['useprice'] : false;
            $persencyto_tindakan = isset($value['persencyto_tindakan']) ? $value['persencyto_tindakan'] : null;
            $persen_penyulit = isset($value['persen_penyulit']) ? $value['persen_penyulit'] : null;
            $persentase = isset($value['persentase']) ? $value['persentase'] : null;
            $kode_posisi = isset($value['kode_posisi']) ? $value['kode_posisi'] : null;
            $tindakanId = $value['daftartindakan_id'];
            $additionalData = json_decode($value['additional_data'], true);
            $is_akomodasi = isset($additionalData['is_akomodasi']) ? $additionalData['is_akomodasi'] : false;

            $value['rowNum'] = $no;
            $value['input_qty'] = $qty;
            $value['input_persentase'] = $persentase;
            $value['input_cyto'] = '';
            $value['input_penyulit'] = '';
            $value['kegiatanoperasi_nama'] = !empty($value['kegiatanoperasi_nama']) ? $value['kegiatanoperasi_nama'] : '-';
            if($status_operasi == DocoConstants::VAR_P_SdhO) {
               $infoPersenCyto = ($is_cyto) ? '<span>&#10003;</span> &nbsp;<span style="font-weight:bold;">('.$persencyto_tindakan . '%)</span>' : '&nbsp;&nbsp;';
               $infoPersenPenyulit = ($is_penyulit) ? '<span>&#10003;</span> &nbsp;<span style="font-weight:bold;">('.$persen_penyulit . '%)</span>' : '&nbsp;&nbsp;';
               $value['input_qty'] = $qty;
               $value['input_persentase'] = $persentase;
               $value['input_cyto'] = ($kode_posisi == $posisiOperator) ? $infoPersenCyto : '';
               $value['input_penyulit'] = ($kode_posisi == $posisiOperator) ? $infoPersenPenyulit : '';
            }
            else {
               if($useprice) {
                  if(!$is_akomodasi) {
                     $value['input_qty'] = Html::input('text', 'qty', $qty, [
                        'class' => 'form-control doco-number', 
                        'id' => 'input-qty-'.$primary,
                        'onkeyup' => "generateTarif('qty',$primary,$qty,$tindakanId,$pasienPenunjangId)",
                     ]);
                     $value['input_persentase'] = Html::input('text', 'persentase', $persentase, [
                        'class' => 'form-control doco-number',
                        'id' => 'input-persentase-'.$primary,
                        'onkeyup' => "generateTarif('persentase',$primary,$persentase,$tindakanId,$pasienPenunjangId)",
                     ]);
                  }
                  if($kode_posisi == $posisiOperator && !$is_akomodasi) {
                     $infoPersenCyto = ($is_cyto) ? '&nbsp;&nbsp;<span style="font-weight:bold;">('.$persencyto_tindakan . '%)</span>' : '&nbsp;&nbsp;';
                     $infoPersenPenyulit = ($is_penyulit) ? '&nbsp;&nbsp;<span style="font-weight:bold;">('.$persen_penyulit . '%)</span>' : '&nbsp;&nbsp;';
                     $value['input_cyto'] = Html::input('checkbox', 'is_cyto', $is_cyto, [
                        'checked' => ($is_cyto == 1) ? true : false, 
                        'id' => 'input-cyto-'.$primary,
                        'onclick' => "generateTarif('cyto',$primary,$is_cyto,$tindakanId,$pasienPenunjangId)",
                     ]).$infoPersenCyto;
                     $value['input_penyulit'] = Html::input('checkbox', 'is_penyulit', $is_penyulit, [
                        'checked' => ($is_penyulit == 1) ? true : false, 
                        'id' => 'input-penyulit-'.$primary,
                        'onclick' => "generateTarif('penyulit',$primary,$is_penyulit,$tindakanId,$pasienPenunjangId)",
                     ]).$infoPersenPenyulit;
                  }
               }
            }
            if(!$is_akomodasi) {
               $value['total_harga_real'] = ($kode_posisi == $posisiOperator) ? $value['harga'] : $value['total_harga_real'];
            }
            else {
               $value['total_harga_real'] = $value['harga'];
            }
            if((int)$kode_posisi == $posisiOperator){
               $subTotal[$value['kegiatanoperasi_nama']] = isset($value['total_harga']) ? $value['total_harga'] : 0;
               $persentaseOperator[$value['kegiatanoperasi_nama']] = $persentase;
               $qtyOperator[$value['kegiatanoperasi_nama']] = $qty;
            }
            
            // Set harga Non Dokter Operator
             if( !empty($persentase) )
             {             
               if(!empty($subTotal[$value['kegiatanoperasi_nama']]) && (int)$kode_posisi != $posisiOperator){
                 $value['harga'] = $subTotal[$value['kegiatanoperasi_nama']] * ( 100 / $persentaseOperator[$value['kegiatanoperasi_nama']]) * (1 / $qtyOperator[$value['kegiatanoperasi_nama']] );
                 $value['total_harga'] = $value['harga'] * ($persentase/100) * $qty;
                 $value['total_harga_real'] = $value['harga'] * ($persentase/100) * $qty;
               }
            }

            $data[$key] = $value;
         }
         $result['data'] = $data;
         $result['recordsTotal'] = count($body['response']);
         $result['recordsFiltered'] = count($body['response']);

         return $result;
      } catch (\Throwable $th) {
         //throw $th;
      }
   }

   public function actionEditVerifikasi()
   {
      $request = Yii::$app->request;
      $response = $this->_restBedah->get('verifikasi-tagihan/edit-verifikasi', [
         'query' => $request->get(),
      ]);
      $response = json_decode($response->getBody(), true);
      return DocoHelpers::response($response);
   }
}
