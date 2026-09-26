<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use app\modules\v1\models\LaporanEndoskopi;
use app\modules\v1\models\LaporanEndoskopiview;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\TimOperasi;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Operasi;
use app\modules\v1\models\DaftarTindakanV;
use app\modules\v1\models\InfoPasienOperasiView;
use app\modules\v1\models\InfoInpostOperasiDetailView;
use app\modules\v1\models\InfoPasienPenunjangView;
use app\modules\v1\models\RencanaOperasi;
use Doco\models\ProfilRsView;
use Doco\components\DocoConstansId;
use app\modules\v1\models\InpostOperasi; 

use app\modules\v1\models\Pasien; 
use Doco\models\Bedah\PasienMasukPenunjang; 
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
//use Doco\models\ProfilRsView;

class LaporanEndoskopiController extends DocoActiveController
{
   public $modelClass = 'app\modules\v1\models\LaporanEndoskopi';
   public function verbs()
   {
      $verbs = parent::verbs();
      $verbs["index"] = ["GET"];
      return $verbs;
   }

   public function actions()
   {
      $actions = parent::actions();
      unset($actions['index']);
      unset($actions['delete']);
      unset($actions['view']);
      unset($actions['create']);
      unset($actions['update']);
      return $actions;
   }
   public function init()
   {
      parent::init();
      $this->request = Yii::$app->request;
   } 
    

    /**
    * @controller actionCetakEndoskopipdf 
     * @attribute #id_pasien#  => idpasien 
     * @attribute #simptoms#  => simptoms 
     * @attribute #pre_diagnosis#  => pre_diagnosis 
     * @attribute #pre_diagnosis_sekunder#  => pre_diagnosis_sekunder 
     * @attribute #indications_examinations#  => indications_examinations 
     * @attribute #instrument#  => instrument 
     * @attribute #pre_medications#  => pre_medications 
     * @attribute #procedure_performed#  => procedure_performed 
     * @attribute #findings#  => findings 
     * @attribute #sampling#  => sampling 
     * @attribute #endoscopic_diagnosis#  => endoscopic_diagnosis 
     * @attribute #endoscopic_diagnosis_sekunder#  => endoscopic_diagnosis_sekunder 
     * @attribute #recommendations#  => recommendations
     * @attribute #tanggal_cetak#   => tanggal_cetak
     * @attribute #doctor_name# => doctor_name
     * @attribute #spesialis# => spesialis
     * @attribute #table#  => table
    **/
    public function actionCetakEndoskopiPdf($id = null)
    { 
      try { 
         
         $data = LaporanEndoskopiview::find()->select([
                  'no_rm',
                  'nama_pasien',
                  'no_pendaftaran',
                  'simptoms',
                  'pre_diagnosis',
                  'pre_diagnosis_sekunder',
                  'indications_examinations',
                  'instrument',
                  'pre_medications',
                  'procedure_performed',
                  'findings',
                  'sampling',
                  'endoscopic_diagnosis',
                  'endoscopic_diagnosis_sekunder',
                  'recommendations',
                  'additional_photo',
                  'dokter_operator', 
                  'spesialis'
                ]) 
                ->where(['pasienmasukpenunjang_id' => $id ])
                ->one();   

         $print = new DocoPrint('laporan-endoskopi'); 
           
         $strprocedure = $this->RemoveSpecialChar($data['procedure_performed']);
         $procedure_performed ='';
         if($strprocedure){
            for($i=0; $i<count($strprocedure); $i++){
               $procedure = explode("||" ,  $strprocedure[$i]);   
               if($procedure){
                  $procedure_performed .= isset($procedure[1]) ? $procedure[1]."," : '';
               }
            }
         } 
        
         $strpre_diag = $this->RemoveSpecialChar($data['pre_diagnosis']);
         $pre_diagnosis = '' ;
         if($strpre_diag){
            $pre_diag = explode ("||",  $strpre_diag); 
            if($pre_diag){
               $pre_diagnosis = isset($pre_diag[2]) ? $pre_diag[2] : '';
            }
         }
         
         
         $strpre_diag_sekunder = $this->RemoveSpecialChar($data['pre_diagnosis_sekunder']);         
         $pre_diagnosis_sekunder = '';
         if($strpre_diag_sekunder){
            for($i=0; $i<count($strpre_diag_sekunder); $i++){
               $pre_diag_sekunder = explode ("||", $strpre_diag_sekunder[$i]);
               if($pre_diag_sekunder){
                  $pre_diagnosis_sekunder .= isset($pre_diag_sekunder[2]) ? $pre_diag_sekunder[2] : '';
               }
            }
         }

         $strdiag = $this->RemoveSpecialChar($data['endoscopic_diagnosis']);         
         $diagnosis = '';   
         if($strdiag){ 
            $endoskop_diangnosis = explode ("||", $strdiag);
            if($endoskop_diangnosis){
               $diagnosis = isset($endoskop_diangnosis[2]) ? $endoskop_diangnosis[2] : '';
            }
         }     
          
         $print->attributes = [
            '#id_pasien#' => $data['no_rm'].'/'.$data['no_pendaftaran'] .'/'. $data['nama_pasien'] , 
            '#simptoms#'  => $data['simptoms'],
            '#pre_diagnosis#'  =>  rtrim($pre_diagnosis,","),
            '#pre_diagnosis_sekunder#'  => rtrim($pre_diagnosis_sekunder,","),
            '#indications_examinations#'  => $data['indications_examinations'],
            '#instrument#'  => $data['instrument'],
            '#pre_medications#'  => $data['pre_medications'],
            '#procedure_performed#'  => strtoupper(rtrim($procedure_performed,",")),
            '#findings#'  => $data['findings'],
            '#sampling#' => $data['sampling'],
            '#endoscopic_diagnosis#' => rtrim($diagnosis,","),
            //'#endoscopic_diagnosis_sekunder#' => $diagnosis_endos,
            '#recommendations#' => $data['recommendations'],
            '#tanggal_cetak#' => date('d-M-Y'),
            '#doctor_name#' => $data['dokter_operator'],  
            '#spesialis#' => $data['spesialis'],
            '#table#' => $this->renderPartial('index',[ 'data' => array( $data['additional_photo'])] ),
         ]; 
         $print->Output(); 
         
      } catch (\yii\db\Exception $e) {
         \Yii::$app->response->statusCode = 500;
         return [
            'message' => $e->getMessage()
         ];
      } catch (\Exception $e) {
         \Yii::$app->response->statusCode = 500;
         return [
            'message' => $e->getMessage()
         ];
      }
   }

   function RemoveSpecialChar($str) {
      $res = str_replace( array( '[', ']' ), '', $str); 
      // Returning the result 
      return $res;
   }

   public function actionGetDataEndoskopi(){
      $request = Yii::$app->request;
      $post = $request->post();
      $id = $post['id'];
      $data = LaporanEndoskopi::find()->where(['pasienmasukpenunjang_id' => $id])->asArray()->one();
      return $data;
   }


   public function actionSimpanLaporan()
   {
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $post = $request->post();
      $connection = Yii::$app->db;
      $transaction = $connection->beginTransaction();
      $result = [];
      try {
         $pasienPenunjang = PasienMasukPenunjang::findOne($id);
         if (empty($pasienPenunjang)) {
            $result['status'] = 500;
            $result['title'] = 'Proses Gagal!';
            $result['text'] = 'Penyimpanan Laporan Operasi Gagal';
         }

         $cekDataExist = LaporanEndoskopi::find()->where(['pasienmasukpenunjang_id' => $id])->one();
         $model = !empty($cekDataExist) ? $cekDataExist : new LaporanEndoskopi;
         $model->attributes = $post;
         if ($model->validate() && $model->save()) {
            $transaction->commit();
            $result = [
               'status' => 200,
               'title' => 'Input Berhasil',
               'text' => 'Laporan Endoskopi Berhasil Disimpan'
            ];
         } else {
            $transaction->rollBack();
            $result['status'] = 500;
            $result['title'] = 'Gagal insert';
            $result['text'] = $model->getErrors();
         }
         return $result;
      } catch (\Exception $e) {
         $transaction->rollBack();
         \Yii::$app->response->statusCode = 500;
         \Yii::error([
            "File" => $e->getFile(),
            "Message" => $e->getMessage(),
            "Line" => $e->getLine(),
         ]);
         return [
            'message' => $e->getMessage()
         ];
      }
      return $post;
   }

   public function actionGetTindakanEndoskopi()
    {
         $tindakan_endoskopi = json_decode(DocoConstansId::actionGetAdditional('tindakan_endoskopi'), true);

         $term = Yii::$app->request->get('q', null);
         $page = Yii::$app->request->get('page', 1);
         $query = DaftarTindakanV::find()
               ->select([
                  'daftartindakan_id as id',
                  'daftartindakan_nama as text'
               ]);
         if (!empty($tindakan_endoskopi)) {
            $query->where(['in','kelompoktindakan_id', $tindakan_endoskopi]);
         }
         if (!empty($term)) {
               $query->andWhere([
                  'ilike',
                  'daftartindakan_nama',
                  $term
               ]);
         }
         $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
         return $query->orderBy('text', 'ASC')
               ->limit($limit + 1)
               ->offset(($page - 1) * $limit)
               ->asArray()
               ->all();
   }
   
}
