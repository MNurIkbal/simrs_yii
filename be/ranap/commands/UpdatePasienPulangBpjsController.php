<?php 

namespace app\commands;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoPasienRiView;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use yii\console\Controller;
use app\modules\v1\models\PasienAdmisi;
use Doco\models\bpjs\Bpjs as BpjsRanap;

class UpdatePasienPulangBpjsController extends Controller
{
   public function actionIndex($startDate = null, $endDate = null)
   {
      $startDate = empty($startDate) ? date('Y-m-d', strtotime('-21 day')) : date('Y-m-d', strtotime($startDate));
      $endDate = empty($endDate) ? date('Y-m-d', strtotime('-1 day')) : date('Y-m-d', strtotime($endDate));
      
      $carakeluarBpjs = (new DocoConstansId)->actionGetAdditional('cara_pulang_bpjs',true);
      $dataBpjs = Yii::$app->db->createCommand("
         SELECT 
             infopasienri_v.tgl_pulang,
             bpjs_t.nosep,
             pasienpulang_t.carakeluar_id,
             pasienpulang_t.tgl_meninggal,
             pasienpulang_t.tglpasienpulang,
             pasienpulang_t.no_surat_kematian,
             loginpemakai_k.nama_pemakai as user,
             bpjs_t.bpjs_id
         FROM infopasienri_v
             JOIN pasienadmisi_t ON infopasienri_v.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
         JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
         JOIN pasienpulang_t ON pasienpulang_t.pasienpulang_id = infopasienri_v.pasienpulang_id
         JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = pasienpulang_t.created_by
         WHERE  bpjs_t.tglpulang IS NULL AND bpjs_t.nosep IS NOT NULL
         AND infopasienri_v.tgl_pulang::date BETWEEN :date_start AND :date_end
             GROUP BY 
             infopasienri_v.tgl_pulang,
             bpjs_t.nosep,
             pasienpulang_t.carakeluar_id,
             pasienpulang_t.tgl_meninggal,
             pasienpulang_t.tglpasienpulang,
             pasienpulang_t.no_surat_kematian,
             loginpemakai_k.nama_pemakai,
             bpjs_t.bpjs_id
      ")
         ->bindValue(':date_start', $startDate)
         ->bindValue(':date_end', $endDate)
         ->queryAll();

      if (!empty($dataBpjs)) {
         foreach ($dataBpjs as $value) {
            // id 5 adalah lain lain pada id bpjs
            $caraPulangBpjs = isset($carakeluarBpjs[$value['carakeluar_id']]) ? $carakeluarBpjs[$value['carakeluar_id']] : 5;
            $bpjsId = isset($value['bpjs_id']) ? $value['bpjs_id'] : null;
            $model = new BpjsRanap();
            $t_sep_new = [];

             $t_sep_new['noSep'] = $value['nosep'];
             $t_sep_new['statusPulang'] = $caraPulangBpjs;
             $t_sep_new['noSuratMeninggal'] = $value['carakeluar_id'] == 4 
                                                ? $value['no_surat_kematian'] : '';
            $t_sep_new['tglMeninggal'] = $value['carakeluar_id'] == 4 ?  date('Y-m-d', strtotime($value['tgl_meninggal'])) : '';
            $t_sep_new['tglPulang'] = date('Y-m-d', strtotime($value['tgl_pulang']));
            $t_sep_new['noLPManual'] = '';
            $t_sep_new['user'] = $value['user'];
            $model->t_sep = $t_sep_new;
            if ($t_sep_new['noSep'] != null && $t_sep_new['tglPulang'] != null) {
               $result = $model->updateTanggalPulangSepNew();
               if (isset($result['metaData']['code']) && ($result['metaData']['code'] == 200 || $result['metaData']['code'] == 201)) {
                  $updateBpjs = BpjsRanap::findOne($bpjsId);
                  $updateBpjs->tglpulang = date('Y-m-d', strtotime($value['tgl_pulang']));
                  $prosesUpdate = $updateBpjs->save();
               }
            }

         }
      }

      Yii::error('proses cron update pasien pulang');
   }
}


    