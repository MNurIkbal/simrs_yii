<?php

/**
 * @author Randy Vianda Putra
 * @todo Konfig Farmasi
 * @copyright 23 April 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\components\ApotekComponent;
use Doco\components\DocoMessages;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Loginpemakai;
use Doco\models\LookupTransaksi;

class KonfigFarmasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KonfigFarmasi';

    public function verbs()
    {
        $verbs = parent::verbs();
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

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $model = new KonfigFarmasi;
            $query = $model::find();
            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $tgl_transaksi =  false;
            $start = date('Y-m-01 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglberlaku'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglberlaku']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglberlaku']); // Unset Advanced Filter  date range
                    $tgl_transaksi = true;
                }
            }
            if ($tgl_transaksi) {
                $query->andWhere(['between', 'tglberlaku', $start, $end]);
            }
            /**
             * End Special Condition date range
             **/

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo get lookup by lookup_type
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param string lookup_type
     */
    private function getLookup($type)
    {
        $model = new Lookup;
        $query = $model::find();
        if ($type) {
            $query->where(['=', 'lookup_type', $type]);
        }

        return $query->asArray()->all();
    }

    /**
     * @todo get all lookup for konfig
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionGetAllLookup()
    {
        $lookup_harga = $this->getLookup(DocoConstants::VAR_MH);
        $lookup_antrian_obat = $this->getLookup(DocoConstants::VAR_OA);
        $lookuptr_kronis_limit = LookupTransaksi::find()
            ->select(['kode_transaksi', 'kode_id as kronis_limit'])
            ->where(['kode_transaksi' => DocoConstants::KRONIS_LIMIT])
            ->asArray()->one();

        return [
            'lookup_harga' => $lookup_harga,
            'lookup_antrian_obat' => $lookup_antrian_obat,
            'kronis_limit' => !empty($lookuptr_kronis_limit) ? $lookuptr_kronis_limit['kronis_limit'] : ''
        ];
    }

    /**
     * @todo save konfig
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSave()
    {

        $model = new KonfigFarmasi;
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        try {
            $transaction = $connection->beginTransaction();
            $konfig = $model->find()->where([
                "konfigfarmasi_id" => '1',
            ])->one();

            $konfig->persenppn = ArrayHelper::getValue($post, 'persen_ppn');
            $konfig->persen_diskon = ArrayHelper::getValue($post, 'persen_diskon');
            $konfig->hargaygdigunakan = ArrayHelper::getValue($post, 'harga_digunakan');
            $konfig->metodeantrian = ArrayHelper::getValue($post, 'metode_antrian');
            $konfig->pesan_etiket = ArrayHelper::getValue($post, 'pesan_etiket');
            $konfig->pesandistruk = ArrayHelper::getValue($post, 'pesan_struk');
            $konfig->embalase_racikan = ArrayHelper::getValue($post, 'embalase_racikan');
            $konfig->embalase_nonracikan = ArrayHelper::getValue($post, 'embalase_nonracikan');
            $konfig->is_verifpemesanan = ArrayHelper::getValue($post, 'is_verifpemesanan');
            $konfig->is_verifpenerimaan = ArrayHelper::getValue($post, 'is_verifpenerimaan');
            $konfig->is_verifstokopname = ArrayHelper::getValue($post, 'is_verifstokopname');
            $konfig->penjaminkaryawan_id = ArrayHelper::getValue($post, 'penjaminkaryawan_id');
            $konfig->po_expired = ArrayHelper::getValue($post, 'po_expired');
            $konfig->is_bypassworklist = ArrayHelper::getValue($post, 'is_bypassworklist');
            $konfig->max_dataso = ArrayHelper::getValue($post, 'max_dataso');
            $konfig->harga_donasi = ArrayHelper::getValue($post, 'harga_donasi');
            $konfig->batal_pesan_by = ArrayHelper::getValue($post, 'batal_pesan_by');
            $konfig->is_large_unit_pr = ArrayHelper::getValue($post, 'is_large_unit_pr');
            $konfig->is_fulfilledso = ArrayHelper::getValue($post, 'is_fulfilledso');
            $konfig->is_tgl_implementasi_sesuai_verif = ArrayHelper::getValue($post, 'is_tgl_implementasi_sesuai_verif');
            $konfig->is_returnstock = ArrayHelper::getValue($post, 'is_returnstock');
            $konfig->is_disabledfulfilled_so = ArrayHelper::getValue($post, 'is_disabledfulfilled_so');
            $konfig->auto_validasi_po_manual = ArrayHelper::getValue($post, 'auto_validasi_po_manual');
            $konfig->is_pesanstokobat_0 = ArrayHelper::getValue($post, 'is_pesanstokobat_0');
            $konfig->is_freetext = ArrayHelper::getValue($post, 'is_freetext');
            $konfig->is_others = ArrayHelper::getValue($post, 'is_others');
            $konfig->get_stok_rs = ArrayHelper::getValue($post, 'get_stok_rs');
            $konfig->use_ppn = ArrayHelper::getValue($post, 'use_ppn');
            $konfig->use_discount = ArrayHelper::getValue($post, 'use_discount');
            $konfig->enable_split_kronis = ArrayHelper::getValue($post, 'enable_split_kronis');
            $konfig->hari_resep_kronis = ArrayHelper::getValue($post, 'hari_resep_kronis');

            if ($konfig->save()) {
                $transaction->commit();
                $cacheKey = 'konfig_farmasi';
                Yii::$app->cache->delete($cacheKey);
                \yii\caching\TagDependency::invalidate(Yii::$app->cache, $cacheKey);
                return $this->responseJson(200,DocoMessages::SUC_MESSAGE_UPDATED);
            }
            
        } catch (\yii\db\Exception $e) {
            $transaction->rollback(); 
            $this->logError($e);
            $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $transaction->rollback();
            $this->logError($e);
            $this->responseJson(500, $e->getMessage());
        }
        
    }

    public function actionDelete($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $result = (new KonfigFarmasi)->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetKonfig($id)
    {
        try {
            $result = KonfigFarmasi::find()->joinWith([
                'editedBy' => function($query){
                    $query->select([
                        'loginpemakai_k.loginpemakai_id',
                        'loginpemakai_k.pegawai_id',
                    ])->joinWith([
                        'pegawai' => function($query){
                            $query->select([
                                'pegawai_m.pegawai_id',
                                'pegawai_m.nama_pegawai'
                            ]);
                        }
                    ]);
                },
                'penjamin' => function($query){
                    $query->select([
                        'penjamin_v.carabayar_nama',
                        'penjamin_v.penjamin_nama'
                    ]);
                }
            ]);
            if ($id) {
                $result->select(["konfigfarmasi_k.*", "pegawai_m.nama_pegawai","penjamin_v.carabayar_nama","penjamin_v.penjamin_nama"])->where(['=', 'konfigfarmasi_id', $id]);
            }
            $konfig = $result->asArray()->one();

            return $konfig;

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

}
