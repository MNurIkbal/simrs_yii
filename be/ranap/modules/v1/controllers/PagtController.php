<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use app\modules\v1\models\Pagt;
use app\modules\v1\models\PagtMonev;
use Doco\components\DocoConstants;
use Doco\Services\KasirService;

class PagtController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\AsesmenAwalGizi';
    protected $_title = 'Pagt';

   public function verbs()
   {
      $verbs = parent::verbs();
      return $verbs;
   }

   public function actions()
   {
      $actions = parent::actions();
      unset($actions['index']);
      return $actions;
   }

   public function actionIndex()
   {
      $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
      if (is_null($pendaftaran_id)) {
         return $this->responseJson(404, 'Data Tidak Ditemukan');
      }
      $getPagt = Pagt::find()->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
      $getPagtMonev = null;
      if (!is_null($getPagt)) {
         $getPagtMonev = PagtMonev::find()->where(['pagt_id' => $getPagt['pagt_id']])->orderBy([
               'pagtmonev_id' => SORT_DESC
         ])->limit(3)->asArray()->all();
      }

      $getHistory = $this->getHistoryMonev($pendaftaran_id);
      return [
         'pagt'      => $getPagt,
         'pagtmonev' => $getPagtMonev,
         'history' => $getHistory,
      ];
   }

   public function actionSavePagt()
   {
      $request  = Yii::$app->request;
      $dataPagt = $request->post('formdata', []);
      $dataPagtMonev  = isset($dataPagt['pagt_monev']) && !empty($dataPagt['pagt_monev']) ? $dataPagt['pagt_monev'] : [];
      $pendaftaran_id = !isset($dataPagt['pendaftaran_id']) || empty($dataPagt['pendaftaran_id']) ? null : $dataPagt['pendaftaran_id'];
      
      $transaction = Yii::$app->db->beginTransaction();
      $model = Pagt::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
      if ( empty($model) ) {
         $model = new Pagt;
      }
      $model->attributes = $dataPagt;
      if (!$model->save()) {
         $transaction->rollBack();
         return $this->responseJson(500, 'Terjadi Kesalahan pada server');
      }

      $pagt_id = $model->getPrimaryKey();
      if( !empty($dataPagtMonev) ){
         $modelPagtMonev = new PagtMonev;
         $modelPagtMonev->pagt_id = $pagt_id;
         $modelPagtMonev->tgl_monev = date('Y-m-d H:i:s');
         $modelPagtMonev->attributes = $dataPagtMonev;
         if (!$modelPagtMonev->save()) {
               $transaction->rollBack();
               return $this->responseJson(500, 'Terjadi Kesalahan pada server');
         }
      }

      $transaction->commit();
      $this->integrasiJasaAsuhan($pendaftaran_id);
      return $this->responseJson(200, 'Simpan PAGT Berhasil!');
   }

   private function getHistoryMonev($pendaftaran_id)
   {
      if( is_null($pendaftaran_id) ){
         return $this->responseJson(422, 'No pendaftaran tidak boleh kosong!');
      }

      return PagtMonev::find()
               ->select([
                  'pagtmonev_t.*'
               ])
               ->rightJoin('pagt_t', 'pagt_t.pagt_id = pagtmonev_t.pagt_id')
               ->where(['pendaftaran_id' => $pendaftaran_id])
               ->limit(2)
               ->orderBy(['tgl_monev' => SORT_DESC,'pagtmonev_id' => SORT_DESC])
               ->asArray()
               ->all();
   }

   public function integrasiJasaAsuhan($pendaftaran_id)
   {
      $jasaAsuhanGiziCache = $this->getOrSetCache(DocoConstants::K_T_JAG, (new \Doco\models\ConstantsId)->find()->select([
         'kode_id'
      ])->where([
               'kode_transaksi' => DocoConstants::K_T_JAG
         ]), false);
      if( !isset($jasaAsuhanGiziCache['kode_id']) || empty($jasaAsuhanGiziCache['kode_id'])){
         Yii::error([
               'log-pagt-kasir' => 'Const Jasa Asuhan Gizi Belum Ditambahkan'
         ]);
         return true;
      }
      $getPendaftaran = (new \app\modules\v1\models\PasienAdmisi)->find()->select([
         'pasienadmisi_t.pendaftaran_id',
         'pasienadmisi_t.pasienadmisi_id',
         'pasienadmisi_t.kelaspelayanan_id',
         'pasienadmisi_t.penjamin_id',
         'pendaftaran_t.no_pendaftaran'
      ])
      ->leftJoin('pendaftaran_t', 'pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id')->where([
         'pasienadmisi_t.pendaftaran_id' => $pendaftaran_id
      ])->asArray()->one();

      $checkIntegrasiJasa = (new \Doco\models\TindakanPelayanan)->find()
      ->select([
         'tindakanpelayanan_id',
      ])
      ->where([
         'pendaftaran_id' => $getPendaftaran['pendaftaran_id'],
         'pasienadmisi_id' => $getPendaftaran['pasienadmisi_id'],
         'kelaspelayanan_id' => $getPendaftaran['kelaspelayanan_id'],
         'penjamin_id' => $getPendaftaran['penjamin_id'],
         'ruangan_id' => Yii::$app->jwt->ruangan_id,
         'instalasi_id' => Yii::$app->jwt->instalasi_id,
         'daftartindakan_id' => $jasaAsuhanGiziCache['kode_id']
      ])->andWhere([
         'between', 'created_date', date('Y-m-d 00:00:00'), date('Y-m-d 23:58:00')
      ])
      ->one();
      if( $checkIntegrasiJasa ){
         return true;
      }
      return (new KasirService)->tagihan($getPendaftaran, [
         [
               'dokter_id' => Yii::$app->jwt->user->pegawai_id,
               'qty' => 1,
               'daftartindakan_id' => $jasaAsuhanGiziCache['kode_id'],
         ]
      ]);
   }
}
