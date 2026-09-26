<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use app\modules\v1\models\PermintaanMakan;
use app\modules\v1\models\PermintaanMakanDetail;
use Doco\models\TarifTotalFn;
use Doco\Services\KasirService;
use Doco\models\Pendaftaran;
class ApiController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\PermintaanMakan';
    
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['simpan-permintaan-makan'] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * Function for handle simpan permintaan makan request
     * 
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSimpanPermintaanMakan()
    {
        $integrateKasir = [];
        $post = Yii::$app->request->post();
        $detailPermintaanMakan = isset($post['detailPermintaanMakan']) ? $post['detailPermintaanMakan'] : [];
        $transaction = Yii::$app->db->beginTransaction();
        $model = new PermintaanMakan;
        $model->attributes = $post;
        $model->status = 1;
        if( !$model->save() ) {
            $transaction->rollBack();
            return $this->responseJson(422, 'Simpan Permintaan Makan Gagal');
        }
        $getPendaftaran = Pendaftaran::find()->select(['pegawai_id', 'no_pendaftaran'])->where(['pendaftaran_id' => $model->pendaftaran_id])->asArray()->one();
        if( $detailPermintaanMakan ){
            $permintaanMakanId = $model->getPrimaryKey();
            $detailSave = [];
            foreach($detailPermintaanMakan as $key => $item){
                $modelDetail = new PermintaanMakanDetail;
                $modelDetail->permintaanmakan_id = $permintaanMakanId;
                $modelDetail->attributes = $item;
                $detailSave[] = $modelDetail->attributes;
                $integrateKasir[] = [
                    'daftartindakan_id' => (int) $item['daftartindakan_id'],
                    'qty' => (int) $item['jumlah'],
                    'dokter_id' => $getPendaftaran['pegawai_id']
                ];
            }
            try{
                PermintaanMakanDetail::batchInsert($detailSave);
            } catch (\yii\db\Exception $e){
                $transaction->rollBack();
                return $this->responseJson(422, 'Simpan Permintaan Makan Gagal');
            }
        }
        $transaction->commit();
        $post['no_pendaftaran'] = $getPendaftaran['no_pendaftaran'];
        (new KasirService)->tagihan($post, $integrateKasir);
        return $this->responseJson(200, 'Simpan Permintaan Makan Berhasil!');
    }
    /**
     * Function for handle get all menu diet from function
     * 
     * @param String var
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetMenuDiet()
    {
        $penjaminId = Yii::$app->request->get('penjamin_id', null);
        $kelasPelayananId = Yii::$app->request->get('kelaspelayanan_id', null);
        $jenisDietId = Yii::$app->request->get('jenisdiet_id', null);
        $ruanganId = Yii::$app->request->get('ruangan_id', Yii::$app->jwt->ruangan_id);
        $query = (new TarifTotalFn([
            'extParam' => [
                $ruanganId,
                $penjaminId,
                $kelasPelayananId,
                'makanan'
            ]
        ]))
            ->find()
            ->select([
                'pemeriksaanlab_id as id',
                'pemeriksaanlab_nama as text',
                'daftartindakan_id'
            ])
            ->where([
                'kelompokpemeriksaanlab_id' => $jenisDietId
            ])->asArray()->all();
        return is_null($query) ? [] : $query;
    }
}