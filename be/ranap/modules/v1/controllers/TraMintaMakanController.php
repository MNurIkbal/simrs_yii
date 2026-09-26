<?php
//@author: Ardi Pratama

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use app\modules\v1\models\InfoPasienGiziView;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\PermintaanMakan;
use app\modules\v1\models\PermintaanMakanDetail;
use app\modules\v1\models\JenisDiet;
use app\modules\v1\models\MenuDietView;
use app\modules\v1\models\InfoPermintaanMakanDetailView;
use app\modules\v1\models\InfoPermintaanMakanView;
use app\modules\v1\models\PegawaiView;

use Doco\Notifications\GiziNotification;

class TraMintaMakanController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\VisiteDokterView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionBundleDataPermintaanMakan()
    {
        try {
            $waktu_diet = Lookup::find(true)
                        ->where(['lookup_type'=>'waktu'])
                        ->andWhere('is_deleted = FALSE')
                        ->andWhere('is_active = TRUE')
                        ->orderby('lookup_urutan' , SORT_ASC)
                        ->asArray()->all();

            $keterangan = Lookup::find(true)
                        ->where(['lookup_type'=>'ket_pemberian'])
                        ->andWhere('is_deleted = FALSE')
                        ->andWhere('is_active = TRUE')
                        ->orderby('lookup_urutan' , SORT_ASC)
                        ->asArray()->all();

            $jenis_diet = JenisDiet::find()->where('jenisdiet_m.is_active <> FALSE')->asArray()->all();
            $jenisdiet = [];
            $no = 0;
            $jenisdiet[$no] = ['id'=> 0, 'text'=> 'Pilih Jenis Diet', 'disabled'=>'disabled', 'selected'=>'selected'];
            foreach ($jenis_diet as $key => $value) {
                $no++;
                $jenisdiet[$no] = [
                    'id' => $value['jenisdiet_id'],
                    'text' => $value['jenisdiet_kode']. ' - ' .$value['jenisdiet_nama']
                ];
            }
        } catch (\yii\db\Exception $e) {
            $waktu_diet = $jenisdiet = $keterangan = [];
        } catch (\Exception $e) {
            $waktu_diet = $jenisdiet = $keterangan = [];
        }
        return ['datamaster'=>['waktu_diet'=>$waktu_diet, 'jenis_diet'=>$jenisdiet, 'keterangan' => $keterangan]];
    }

    public function actionSimpanPermintaanMakan()
    {
        try {
            $params = Yii::$app->request;
            $pendaftaran_id = $params->post('pendaftaran_id',0);
            $pegawai_pemesan = $params->post('pegawai_pemesan',0);
            $transaction = Yii::$app->db->beginTransaction();

            $infoPasienRi = InfoPasienRiView::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();
            $pasienadmisi_id = $infoPasienRi->pasienadmisi_id;

            $modelMakan = new PermintaanMakan;
            $modelMakan->pendaftaran_id = $pendaftaran_id;
            $modelMakan->pasienadmisi_id = $pasienadmisi_id;
            $modelMakan->peg_pemesan_id = $pegawai_pemesan;
            $modelMakan->tgl_permintaanmakan = date('Y-m-d H:i:s');
            $modelMakan->status = 1;
            
            if(!$modelMakan->validate()){
                throw new \yii\db\Exception('Gagal Validasi Permintaan Makan', $modelMakan->getErrors(),500);
            }

            if(!$modelMakan->save()){
                throw new \yii\db\Exception('Gagal Simpan Permintaan Makan', $modelMakan->getErrors(),500);
            }

            $detailMakan = $params->post('detailMintaMakan',[]);
            if(is_array($detailMakan) && count($detailMakan)<1){
                throw new \yii\base\ErrorException("Tidak Ada Data Detail", 1);
            }

            foreach ($detailMakan as $val_detail_makan) {
                $modelMakanDetail = new PermintaanMakanDetail;
                $modelMakanDetail->permintaanmakan_id = $modelMakan->getPrimaryKey();
                $modelMakanDetail->jenisdiet_id = $val_detail_makan['jenis_diet'];
                $modelMakanDetail->makanandiet_id = $val_detail_makan['menu_diet'];
                $modelMakanDetail->waktu_diet = $val_detail_makan['waktu_diet'];
                $modelMakanDetail->jumlah = $val_detail_makan['jumlah_diet'];
                $modelMakanDetail->keterangan = isset($val_detail_makan['keterangan']) ? $val_detail_makan['keterangan'] : null;
                if(!$modelMakanDetail->validate()){
                    throw new \yii\db\Exception('Gagal Validasi Detail Permintaan Makan', $modelMakanDetail->getErrors(),500);
                }

                if(!$modelMakanDetail->save()){
                    throw new \yii\db\Exception('Gagal Simpan Detail Permintaan Makan', $modelMakanDetail->getErrors(),500);
                }
            }

            $transaction->commit();

            $modelMakanAfterSave = PermintaanMakan::findOne($modelMakan->getPrimaryKey());
            
            $dataDetails = InfoPermintaanMakanDetailView::find()->
                andWhere(['permintaaanmakan_id' => $modelMakan->getPrimaryKey()])->
                asArray()->all();
            GiziNotification::dietNotification($infoPasienRi, $modelMakanAfterSave, $dataDetails);
            return [
                'message'=>'Proses Berhasil!',
                'text' => 'Permintaan Makan Berhasil Dibuat',
                'no_permintaanmakan' => $modelMakanAfterSave->no_permintaanmakan
            ];

        } catch(\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo
            ];
        } catch(\yii\base\ErrorException $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'data'=>$e->getMessage(),
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan Internal',
                'errorInfo'=>$e->getName()
            ];
        } catch(\yii\base\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage(),
                'text' => 'Kesalahan Internal',
                'errorInfo'=>$e->getName()
            ];
        }
    }

    public function actionCariJenisDiet()
    {
        $params = Yii::$app->request;
        $term = $params->get('term','');
        $dataJenisDiet = JenisDiet::find()->where('jenisdiet_m.is_active <> FALSE');
        if($term){
            $dataJenisDiet->andWhere("LOWER(jenisdiet_kode) LIKE '%".$term."%' OR LOWER(jenisdiet_nama) LIKE '%".$term."%'");
        }

        return $dataJenisDiet->asArray()->all();
    }

    public function actionCariMenuDietByJenis()
    {
        $params = Yii::$app->request;
        $jenis_id = $params->get('jenis_id',0);
        $dataMenuDiet = MenuDietView::find()->where(['jenisdiet_id'=>$jenis_id])->andWhere('is_active = TRUE');

        return $dataMenuDiet->asArray()->all();
    }
}