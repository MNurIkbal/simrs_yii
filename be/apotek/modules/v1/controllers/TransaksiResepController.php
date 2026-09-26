<?php

/**
 * @author Randy Vianda Putra
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @edited by : Anggoro (tri.anggoro@docotel.com)
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use SirsCore\features\FeatureTindakanBmhp;
use SirsCore\features\FeatureResep;
use SirsCore\features\FeaturePendaftaran;
use SirsCore\features\IntegrasiAkunting;
use app\components\ApotekComponent;
use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\InfoDetailResepturView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PasienView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\InfoResepturDetailView;
use app\modules\v1\models\InfoResepView;
use app\modules\v1\models\InfoResepDetailView;
use app\modules\v1\models\InformasiResepDetailView;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\SyncPengeluaranobat;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\KonfigAntrianFarmasi;
use app\modules\v1\models\Racikan;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\PenjaminV;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\ResepturRacikan;
use app\modules\v1\models\SatuanUnit;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\WorklistDetailView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoAkunting;
use app\modules\v1\businessLogic\StokObatAlkes as BLStokObatAlkes;
use app\modules\v1\models\Carabayar as ModelsCarabayar;
use app\modules\v1\models\KetersediaanObatView;
use app\modules\v1\models\RiwayatAlergiView;
use Doco\models\Pendaftaran;
use Doco\models\HargaObatAlkesFn;
use ErrorException;
use Exception;

class TransaksiResepController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InformasiReseptur';
    public $konfig_farmasi;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        $custom_actions = [
            'edit-resep' => 'app\modules\v1\actions\TransaksiResep\EditResepAction',
            'simpan-reseptur' => 'app\modules\v1\actions\TransaksiResep\SimpanResepturAction',
            'approve-resep' => 'app\modules\v1\actions\TransaksiResep\ApproveResepAction'
        ];
        $actions = array_merge($actions, $custom_actions);
        return $actions;
    }

    public function init()
    {
        // $this->konfig_farmasi = DocoConstants::konfigFarmasi();
        parent::init();
    }

    /**
     * @todo get all data resep
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer tipe resep
     */
    private function getAllDataResep($id = null)
    {
        $resep = InformasiResepturView::find()->select([
            'reseptur_id',
            'noresep',
            'tglreseptur',
            'nama_pasien',
            'instalasi_reseptur',
            'ruangan_reseptur',
            'no_pendaftaran',
            'nama_pegawai',
            'pendaftaran_id',
            'carabayar_id',
            'penjamin_id',
            'pasien_id',
            'pasienadmisi_id',
            'penjualanresep_id',
            // 'kelaspelayanan_id'
            'iter'
        ]);

        if ($id) {
            $resep->where('reseptur_id = :id', ['id' => $id]);
        }

        return $resep;
    }

    /**
     * @todo get all detail obat resep
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer reseptur id
     */
    private function getAllDetailResep($id)
    {
        $detail = InfoDetailResepturView::find()->where(['reseptur_id' => $id]);

        return $detail;
    }

    /**
     * @todo get all data with ajax
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionAjax()
    {
        try {
            $data_resep = $this->getAllDataResep()->asArray()->all();
            $data_pegawai = $this->getAllDataDokter()->asArray()->all();
            $data_pasien = $this->getAllDataPasien()->asArray()->all();

            return [
                'data-resep' => $data_resep,
                'data-pegawai' => $data_pegawai,
                'data-pasien' => $data_pasien
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionFillerPasien()
    {
        $cacheDuration = 60*5;
        $ruangan_id = Yii::$app->request->get('ruangan_id',null);
        $cacheParam = Yii::$app->request->get('cache',null);
        if($cacheParam != null && $cacheParam == 0){
            \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
        }

        try{
            $data_signa = SignaObat::getDb()->cache(function($db){
                return SignaObat::find()
                ->select(['signa_id', 'signa_kode', 'signa_nama', 'concat(signa_kode,\' \',signa_nama) AS kode_nama'])
                ->asArray()->all();
            },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));

            $data_cara_bayar = CaraBayar::getDb()->cache(function($db) {
                return Carabayar::find()
                ->select(['carabayar_id', 'carabayar_nama'])
                ->where(['is_deleted' => false, 'is_active' => true])
                ->asArray()->all();
            }, $cacheDuration, new \yii\caching\TagDependency(['tags' => 'obat']));

            $data_obat_ruangan = Yii::$app->db->cache(function($db)use($ruangan_id){
                return KetersediaanObatView::find()
                    ->select(['obatalkes_id','obatalkes_nama','obatalkes_namalain','qty_dipesan','qty_stok AS qty_tersedia'])
                    ->where(['ruangan_id' => $ruangan_id])
                    ->orderBy(['obatalkes_nama' => SORT_ASC])->asArray()->all();
            },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));

            $konfig_farmasi = KonfigFarmasi::getDb()->cache(function($db){
                return KonfigFarmasi::find()
                    ->select("konfigfarmasi_k.*, penjamin_m.carabayar_id")
                    ->join('JOIN', 'penjamin_m', "konfigfarmasi_k.penjaminkaryawan_id = penjamin_m.penjamin_id")
                    ->asArray()->one();
            },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));

            $penjamin = [];
            if (isset($konfig_farmasi['penjaminkaryawan_id'])) {
                $penjamin = PenjaminV::getDb()->cache(function($db) use($konfig_farmasi){
                    return PenjaminV::find()
                    ->select("carabayar_id, carabayar_nama, penjamin_id, penjamin_nama")
                    ->where(['penjamin_id' => $konfig_farmasi['penjaminkaryawan_id']])
                    ->asArray()->one();
                },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));

                $penjamin['penjamin_id'] = DocoHelpers::encrypt($penjamin['penjamin_id']);
                $penjamin['carabayar_id'] = DocoHelpers::encrypt($penjamin['carabayar_id']);

            }

            $satuanUnit = SatuanUnit::getDb()->cache(function($db){
                return SatuanUnit::find()->where(['is_active' => true])->asArray()->all();
            },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));

            return [
                'data'=>[
                    'penjamin_karyawan' => $penjamin,
                    'signa' => $data_signa,
                    'satuan_unit' => $satuanUnit,
                    'data_cara_bayar' => $data_cara_bayar
                ]
            ];
        }catch(\Exception $e){
            return [
                'message' => $e->getMessage(),
                'data' =>[
                    'penjamin' => [],
                    'signa' => [],
                    'satuan_unit' => [],
                    'data_cara_bayar' => []
                ]
            ];
        }
    }

    public static function getInfoResepturData($id, $duration = 5, $tagName = null)
    {
        return InformasiResepturView::getDb()->cache(function ($db) use ($id) {
            return InformasiResepturView::find()->where('reseptur_id = :id', ['id' => $id])->asArray()->one();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getInfoResepData($id, $duration = 5, $tagName = null)
    {
        return InfoResepView::getDb()->cache(function ($db) use ($id) {
            return InfoResepView::find()->where('penjualanresep_id = :id', ['id' => $id])->asArray()->one();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getDetailResepData($id, $isReseptur = true, $duration = 5, $tagName = null)
    {
        return InformasiResepDetailView::getDb()->cache(function ($db) use ($id, $isReseptur) {
            $model = InformasiResepDetailView::find(true);
            if ($isReseptur) {
                $model->where(['reseptur_id' => $id, 'jenis' => 'reseptur'])
                    ->orderBy(['resepturdetail_id'=>SORT_ASC]);
            } else {
                $model->where(['penjualanresep_id' => $id])
                    ->orderBy(['penjualanresep_id' => SORT_ASC]);
            }
            return $model->asArray()->all();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getListKetersediaanObatRuangan($ids, $ruanganId, $duration = 5, $tagName = null)
    {
        return KetersediaanObatView::getDb()->cache(function ($db) use ($ids, $ruanganId) {
            return KetersediaanObatView::find()->where(['ruangan_id' => $ruanganId])->andWhere(['obatalkes_id' => $ids])->asArray()->all();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function listInfoObatR($obatalkesIds, $penjaminId, $kelaspelayananId, $duration = 5, $tagName = null)
    {
        return HargaObatAlkesFn::getDb()->cache(function ($db) use ($obatalkesIds, $penjaminId, $kelaspelayananId) {           
            return (new HargaObatAlkesFn([
                'extParam' => [
                    (string) $penjaminId,
                    (string) $kelaspelayananId
                ]
            ]))
            ->find()
            ->select([
                'obatalkes_id',
                'obatalkes_nama',
                'hargaygdipakai'
            ])
            ->where([
                'obatalkes_id' => $obatalkesIds
            ])->asArray()->all();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getRiwayatAlergiPasien($id, $duration = 5, $tagName = null)
    {
        return RiwayatAlergiView::getDb()->cache(function ($db) use ($id) {
            return RiwayatAlergiView::find()->where(['pasien_id' => $id])->asArray()->all();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getRiwayatPersonalPasien($id, $duration = 5, $tagName = null)
    {
        return Yii::$app->db->cache(function ($db) use ($id) {
            return $db->createCommand("SELECT catatanpenting_pasien, riwayat_penyakit, riwayat_obat, riwayat_alergi FROM riwayatpersonalpasien_v WHERE pasien_id = :pasien_id")
                ->bindValue(":pasien_id", $id)->queryOne();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getListSignaObat($ids, $duration = 5, $tagName = null)
    {
        return SignaObat::getDb()->cache(function ($db) use ($ids) {
            $model = SignaObat::find()->select(['signa_id', 'signa_nama', 'signa_kode', 'qty_obat', 'iterasi', 'concat(signa_kode,\' \',signa_nama) AS kode_nama']);
            if (!empty($ids)) {
                $model->where(['signa_id' => $ids]);
            }
            return $model->asArray()->all();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public static function getListSatuanUnit($ids, $duration = 5, $tagName = null)
    {
        return SatuanUnit::getDb()->cache(function ($db) use ($ids) {
            $model = SatuanUnit::find()->where(['is_active' => true]);
            if (!empty($ids)) {
                $model->andWhere(['satuanunit_id' => $ids]);
            }
            return $model->asArray()->all();
        }, $duration, empty($tagName) ? null : new \yii\caching\TagDependency(['tags' => $tagName]));
    }

    public function actionGetResepData()
    {
        $req = Yii::$app->request;
        $resep_id = $req->get('resep_id', null);
        try {
            $data_resep = self::getInfoResepData($resep_id);
            return [
                'data_resep' => $data_resep,
                'data_pendaftaran' => self::getInfoPendaftaranPasien($data_resep['pendaftaran_id'], $data_resep['pasienadmisi_id']),
                'data_alergi' => self::getRiwayatAlergiPasien($data_resep['pasien_id']),
                'riwayat_personal' => self::getRiwayatPersonalPasien($data_resep['pasien_id']),
            ];
        } catch(\yii\base\ErrorException $e) {
            Yii::error($e);
            return [
                'message' => $e->getMessage(),
                'data_resep' => null,
                'data_alergi' => [],
                'riwayat_personal' => null,
            ];
        }
    }

    public function actionGetResepturData()
    {
        $req = Yii::$app->request;
        $reseptur_id = $req->get('reseptur_id', null);
        try {
            $data_resep = self::getInfoResepturData($reseptur_id);
            return [
                'data_resep' => $data_resep,
                'data_alergi' => self::getRiwayatAlergiPasien($data_resep['pasien_id']),
                'riwayat_personal' => self::getRiwayatPersonalPasien($data_resep['pasien_id']),
            ];
        } catch(\yii\base\ErrorException $e) {
            Yii::error($e);
            return [
                'message' => $e->getMessage(),
                'data_resep' => null,
                'data_alergi' => [],
                'riwayat_personal' => null,
            ];
        }
    }

    public function actionGetDetailResep()
    {
        $req = Yii::$app->request;
        $resep_id = $req->get('resep_id', null);
        $reseptur_id = $req->get('reseptur_id', null);
        $ruangan_id = $req->get('ruangan_id', null);
        try {
            $detail_resep = [];
            $stockObatRuangan = [];
            if ($reseptur_id || $resep_id) {
                $detail_resep = self::getDetailResepData(!empty($reseptur_id) ? $reseptur_id : $resep_id, !empty($reseptur_id));
                $obatalkes_ids = ArrayHelper::getColumn($detail_resep, 'obatalkes_id');
                $stockObatRuangan = self::getListKetersediaanObatRuangan($obatalkes_ids, $ruangan_id);
                $stockObatRuangan_idx = ArrayHelper::index($stockObatRuangan, 'obatalkes_id');
                foreach($detail_resep as $idx => $value) {
                    $detail_resep[$idx]['qty_tersedia'] = ArrayHelper::getValue($stockObatRuangan_idx, "{$value['obatalkes_id']}.qty_tersedia", 0);
                }
            }
            return $detail_resep;
            // return [
            //     'detail_resep' => $detail_resep,
            //     'data_obat_ruangan' => $stockObatRuangan,
            // ];
        } catch(\yii\base\ErrorException $e) {
            Yii::error($e);
            return [];
            // return [
            //     'detail_resep' => [],
            //     'data_obat_ruangan' => [],
            // ];
        }
    }

    public function actionGetDetailEtiket()
    {
        $nomor = Yii::$app->request->get('nomor',null);
        $detail_resep = (new \yii\db\Query())
            ->select([
                'inforesepdetail_v.resepturdetail_id',
                'inforesepdetail_v.obatalkespasien_id',
                'inforesepdetail_v.obatalkes_nama',
                'inforesepdetail_v.rke',
                'inforesepdetail_v.signa_nama',
                'om.is_oral'
            ])
            ->from('inforesepdetail_v')
            ->where([
                'noresep' => $nomor
            ])
            ->leftJoin('obatalkes_m om', 'om.obatalkes_id = inforesepdetail_v.obatalkes_id')
            ->all();
        return $detail_resep;
    }

    public function actionGetListResepturRacikan()
    {
        $req = Yii::$app->request;
        $reseptur_id = $req->get('reseptur_id', null);
        try {
            return empty($reseptur_id) ? [] : ResepturRacikan::find()->where(['reseptur_id' => $reseptur_id])->asArray()->all();
        } catch(\yii\base\ErrorException $e) {
            Yii::error($e);
            return [];
        }
    }

    public function actionGetListDataSigna()
    {
        $req = Yii::$app->request;
        $signa_id = $req->get('signa_id', []);
        $cacheDuration = 60 * 15;
        try {
            return self::getListSignaObat($signa_id, $cacheDuration);
        } catch(\yii\base\ErrorException $e) {
            Yii::error($e);
            return [];
        }
    }

    public function actionGetListSatuanUnit()
    {
        $req = Yii::$app->request;
        $satuanunit_id = $req->get('satuanunit_id', []);
        $cacheDuration = 60 * 15;
        try {
            return self::getListSatuanUnit($satuanunit_id, $cacheDuration);
        } catch(\yii\base\ErrorException $e) {
            Yii::error($e);
            return [];
        }
    }

    public function actionGetKonfigFarmasi($cache_duration = 0)
    {
        $selected = Yii::$app->request->get('selected', []);
        try {
            return AllowController::getKonfigFarmasi($selected, $cache_duration);
        } catch(Exception $e) {
            Yii::error($e);
            return [];
        }
    }

    public function actionGetKonfigSystem()
    {
        $req = Yii::$app->request;
        $selected = $req->get('selected', []);
        $cacheDuration = 60 * 15;
        try {
            return AllowController::getKonfigFarmasi($selected, $cacheDuration);
        } catch(\yii\base\ErrorException $e) {
            Yii::error($e);
            return null;
        }
    }

    public function actionFillerEditReseptur()
    {
        $cacheDuration = 60*5;
        $reseptur_id = Yii::$app->request->get('reseptur_id',null);
        $resep_id = Yii::$app->request->get('resep_id',null);
        $ruangan_id = Yii::$app->request->get('ruangan_id',null);
        try{
            $data_signa = [];
            $data_obat_ruangan = [];

            $data_signa = SignaObat::getDb()->cache(function($db){
                return SignaObat::find()
                ->select(['signa_id', 'signa_kode', 'signa_nama', 'concat(signa_kode,\' \',signa_nama) AS kode_nama'])
                ->asArray()->all();
            },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));
            if(!is_null($reseptur_id)) {
                $data_resep = InformasiResepturView::find()->where('reseptur_id = :id', ['id' => $reseptur_id])->asArray()->one();
                $detail_resep = InformasiResepDetailView::find(true)->where(['reseptur_id' => $reseptur_id, 'jenis' => 'reseptur'])
                                ->orderBy(['resepturdetail_id'=>SORT_ASC])->asArray()->all();
                $list_id_obat = ArrayHelper::getColumn($detail_resep,'obatalkes_id');
                $getStok = KetersediaanObatView::find()
                            ->where(['ruangan_id' => $ruangan_id])->andWhere(['obatalkes_id'=>$list_id_obat])
                            ->asArray()->all();
                foreach($detail_resep as $_keydetail => &$value ){
                    foreach($getStok as $_getstok){
                        if($value['obatalkes_id'] == $_getstok['obatalkes_id']){
                            $detail_resep[$_keydetail]['qty_tersedia'] = $_getstok['qty_tersedia'];
                        }
                    }
                }
                unset($value); 
            } else {
                $data_resep = InfoResepView::find()->where('penjualanresep_id = :id', ['id' => $resep_id])->asArray()->one();
                $detail_resep = InformasiResepDetailView::find(true)->where(['penjualanresep_id' => $resep_id])
                                ->orderBy(['penjualanresep_id' => SORT_ASC])->asArray()->all();
                $list_id_obat = ArrayHelper::getColumn($detail_resep,'obatalkes_id');
                $getStok = KetersediaanObatView::find()
                            ->where(['ruangan_id' => $ruangan_id])->andWhere(['obatalkes_id'=>$list_id_obat])
                            ->asArray()->all();
                foreach($detail_resep as $_keydetail => &$value ){
                    foreach($getStok as $_getstok){
                        if($value['obatalkes_id'] == $_getstok['obatalkes_id']){
                            $detail_resep[$_keydetail]['qty_tersedia'] = $_getstok['qty_tersedia'];
                        }
                    }
                }
                unset($value);                   
            }

            $data_alergi = RiwayatAlergiView::getDb()->cache(function($db)use($data_resep){
                return $alergi = RiwayatAlergiView::find()->where([
                    'pasien_id' => $data_resep['pasien_id']
                ])->all();
            },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));

            $riwayat_personal = [];
            if(isset($data_resep['pasien_id'])){
                $riwayat_personal = Yii::$app->db->createCommand("SELECT catatanpenting_pasien, riwayat_penyakit, riwayat_obat, riwayat_alergi FROM riwayatpersonalpasien_v WHERE pasien_id = :pasien_id")->bindValue(":pasien_id",$data_resep['pasien_id'])->queryOne();
            }

            $konfig = KonfigFarmasi::getDb()->cache(function($db){
                return KonfigFarmasi::findOne(1);
            },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));

            $konfig_sys = KonfigSystem::getDb()->cache(function($db){
                return KonfigSystem::findOne(1);
            },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));

            $data_racikan = is_null($reseptur_id) ? [] : ResepturRacikan::find()->where(['reseptur_id' => $reseptur_id])->asArray()->all();
            $satuanUnit = SatuanUnit::getDb()->cache(function($db){
                return SatuanUnit::find()->where(['is_active' => true])->asArray()->all();
            },$cacheDuration, new \yii\caching\TagDependency(['tags'=>'obat']));
            return [
                'message' => 'OK',
                'data'=>[
                    'alergi' => $data_alergi,
                    'riwayat_personal' => $riwayat_personal,
                    'detailResep' => $detail_resep,
                    'konfig' => $konfig,
                    'konfigSys' => $konfig_sys,
                    'resep' => $data_resep,
                    'signa' => $data_signa,
                    'obatRuangan' => $data_obat_ruangan,
                    'obatRacikan' => $data_racikan,
                    'satuan_unit' => $satuanUnit
                ]
            ];
        }catch(\Exception $e){
            return [
                'message' => $e->getMessage(),
                'data' =>[
                    'alergi' => [],
                    'riwayat_personal' => [],
                    'detailResep' => [],
                    'konfig' => [],
                    'konfigSys' => [],
                    'resep' => [],
                    'signa' => [],
                    'obatRuangan' => [],
                    'obatRacikan' => [],
                    'satuan_unit' => []
                ]
            ];
        }
    }

    /**
     * @todo get all detail data resep obat
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer reseptur id
     */
    public function actionDetailResep($id)
    {
        $detail_resep = $this->getAllDetailResep($id)->asArray()->all();

        return [
            'detail-resep' => $detail_resep
        ];
    }

    /**
     * @todo get all data resep pasien list modal
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionDataResep()
    {
        try {
            $model = new InformasiResepturView;
            $request = Yii::$app->request;

            $result = $this->getAllDataResep()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($tgl_resep = $request->post('tgl_resep_submit')) {
                $result->where('tglresep = :tgl_resep', ['tgl_resep' => $tgl_resep]);
            }

            if ($no_resep = $request->post('no_resep')) {
                $result->andFilterWhere(['ILIKE', 'noresep', $no_resep]);
            }

            if ($instalasi = $request->post('instalasi')) {
                $result->andFilterWhere(['ILIKE', 'instalasireseptur_nama', $instalasi]);
            }

            if ($ruangan = $request->post('ruangan')) {
                $result->andFilterWhere(['ILIKE', 'ruangan_nama', $ruangan]);
            }

            if ($nama_pasien = $request->post('nama_pasien')) {
                $result->andFilterWhere(['ILIKE', 'nama_pasien', $nama_pasien]);

            }

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
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

    /**
     * @todo save multiple table
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSaveRs()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $post = $request->post();
        // $post = json_decode($post['data'],true);
        try {
            $now = date('Y-m-d H:i:s');
            $db = Yii::$app->db;
            $user_login = Yii::$app->user->identity->pegawai_id;
            $data_info_resep = $this->getAllDataResep($post['reseptur_id'])->asArray()->one();

            $resepturId = $data_info_resep['reseptur_id'];
            $total_harganetto = $post['totalharga_netto'];
            $total_hargajual = $post['totalharga_jual'];
            $keterangan = $post['keterangan'];
            $del_ResepturDetailId = [];
            if (!empty($post['del_ResepturDetailId'])) {
                $del_ResepturDetailId = $post['del_ResepturDetailId'];
            }

            $listResepturId = $this->updateReseptur($resepturId, $post['list_obat'], $del_ResepturDetailId);
            if (count($listResepturId) == 0) {
                return $response['response'] = [
                            'title' => 'Terjadi Kesalahan !',
                            'text' => 'Obat tidak boleh kosong !',
                            'status' => 422
                       ];
            }
            $tanggalBerlaku = date('Y-m-d');
            $konfig = $connection->createCommand("
                            SELECT metodeantrian FROM konfigfarmasi_k
                            WHERE tglberlaku >= '{$tanggalBerlaku}'
                            AND konfigfarmasi_aktif = true
                            AND is_active = true
                        ")->queryOne();
            // Mencari Metode dengan nilai default FEFO
            $currentMetode = BLStokObatAlkes::FEFO;
            if ($konfig) {
                $currentMetode = isset($konfig['metodeantrian'])
                                    ? strtoupper($konfig['metodeantrian']) : BLStokObatAlkes::FEFO;
            }

            $model = new PenjualanResep;
            $model->pegawai_id = $user_login;
            $model->pendaftaran_id = $data_info_resep['pendaftaran_id'];
            $model->penjamin_id = $data_info_resep['penjamin_id'];
            $model->carabayar_id = $data_info_resep['carabayar_id'];
            $model->pasien_id = $data_info_resep['pasien_id'];
            $model->ruangan_id = $post['ruangan_id'];
            $model->reseptur_id = $data_info_resep['reseptur_id'];
            $model->iter = $data_info_resep['iter'];
            $model->totharganetto = $total_harganetto;
            $model->totalhargajual = $total_hargajual;
            $model->tglpenjualan = $now;
            $model->jenispenjualan = DocoConstants::PENJUALAN_RESEP_RS;
            $model->tglresep = $now;
            $model->created_by = $user_login;
            $model->catatan = $keterangan;

            if ($model->validate() && $model->save()) {
                $penjualan_id = $model->penjualanresep_id;
                $obj_array_insert = [];
                $list_obat = $post['list_obat'];
                foreach ($list_obat as $key => $value) {
                    $obj_array_insert[] = [
                        'ruangan_id' => $post['ruangan_id'],
                        'carabayar_id' => $data_info_resep['carabayar_id'],
                        'pendaftaran_id' => $data_info_resep['pendaftaran_id'],
                        'pasien_id' => $data_info_resep['pasien_id'],
                        'penjamin_id' => $data_info_resep['penjamin_id'],
                        'penjualanresep_id' => $penjualan_id,
                        'tglpelayanan' => $now,
                        'pegawai_id' => $user_login,
                        'racikan_id' => ($value['racikan_id'] == '-') ? 2 : $value['racikan_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'qty_oa' => $value['qty'],
                        'signa_oa' => (string)$value['signa_id'],
                        'rke' => (empty($value['r_ke']) || $value['r_ke'] == '-') ? 0 : $value['r_ke'],
                        'hargasatuan_oa' => $value['hargajual'],
                        'harganetto_oa' => $value['harganetto'],
                        'hargajual_oa' => $value['subtotal'],
                        'resepturdetail_id' => $listResepturId[$value['obatalkes_id']],
                        'created_by' => $user_login,
                        'additional_data'=> json_encode([
                            'posisi' => isset($value['posisi']) ? $value['posisi'] : 9999
                        ])
                    ];
                    $detailTrans[] = [
                        'obatalkes_id' => $value['obatalkes_id'],
                        'qty_satuanpakai' => $value['qty'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'harganetto' => $value['harganetto'],
                        'persendiscount' => $value['persendiscount'],
                        'persenppn' => $value['persenppn'],
                        'persenmargin' => $value['persenmargin'],
                        'jmldiscount' => $value['jmldiscount'],
                        'jmlmargin' => $value['jmlmargin'],
                        'jmlppn' => $value['jmlppn'],
                    ];
                }
                ObatAlkesPasien::batchInsert($obj_array_insert);
                $getAlkesPasien = ObatAlkesPasien::find()->select(['penjualanresep_id', 'obatalkespasien_id','obatalkes_id'])->where(['penjualanresep_id'=>$penjualan_id])->asArray()->all();
                $dataAlkes = [];
                foreach($getAlkesPasien as $key => $value):
                    $dataAlkes[$value['obatalkes_id']] = $value;
                endforeach;
                foreach($detailTrans as $key => $value):
                    $detailTrans[$key]['obatalkespasien_id'] = $dataAlkes[$value['obatalkes_id']]['obatalkespasien_id'];
                endforeach;

                BLStokObatAlkes::$distribusi = false;
                if ($currentMetode === BLStokObatAlkes::FEFO) {
                    $methode = BLStokObatAlkes::methodeFEFO($detailTrans,$now);
                } else {
                    $methode = BLStokObatAlkes::methodeFIFO($detailTrans,$now);
                }

                $approve_status = DocoConstants::VAR_AR;
                $sql_update = "UPDATE reseptur_t SET
                    penjualanresep_id = ". $penjualan_id . ",
                    status_reseptur = {$approve_status}
                    WHERE
                        penjualanresep_id is null
                    AND
                        reseptur_id = ". $post['reseptur_id'] ."
                ";
                $db->createCommand($sql_update)->execute();
            }
            $pendafatran_id = $data_info_resep['pendaftaran_id'];
            $status = DocoConstants::BELUM_LUNAS;
            Yii::$app->db->createCommand("
                UPDATE pendaftaran_t SET status_bayar = {$status}
                WHERE pendaftaran_id = {$pendafatran_id}
            ")->execute();
            $getData = PenjualanResep::findOne($penjualan_id);
            $noresep = isset($getData['noresep']) ? $getData['noresep'] : '';
            $return_integrate = IntegrasiAkunting::integrateByNoResep($noresep); // $this->integrate($noresep);
            $transaction->commit();
            return ['message' => 'Data Berhasil di simpan', 'id'=>$penjualan_id,'enc' => DocoHelpers::encrypt($penjualan_id), 'nomor' => $noresep];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo save resep RS, modified from save rs
     * @author Ardi Pratama
     */
    public function actionSaveResepRs()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $post = $request->post();
        try {

            $now = date('Y-m-d H:i:s');
            $db = Yii::$app->db;
            $user_login = Yii::$app->user->identity->pegawai_id;
            $data_info_resep = $this->getAllDataResep($post['reseptur_id'])->asArray()->one();
            $resepturId = $data_info_resep['reseptur_id'];
            $total_hargajual = $post['totalharga_jual'];
            $biayaadministrasi = $post['biayaadministrasi'];
            $total_harganetto = $post['totalharga_netto'];
            $keterangan = $post['keterangan'];
            $del_ResepturDetailId = [];
            if (!empty($post['del_ResepturDetailId'])) {
                $del_ResepturDetailId = $post['del_ResepturDetailId'];
            }
            $listResepturId = $this->updateReseptur($resepturId, $post['list_obat'], $del_ResepturDetailId);
            if (count($listResepturId) == 0) {
                return $response['response'] = [
                            'title' => 'Terjadi Kesalahan !',
                            'text' => 'Obat tidak boleh kosong !',
                            'status' => 422
                       ];
            }
            $model = new PenjualanResep;
            $model->pegawai_id = $user_login;
            $model->pendaftaran_id = $data_info_resep['pendaftaran_id'];
            $model->penjamin_id = $data_info_resep['penjamin_id'];
            $model->carabayar_id = $data_info_resep['carabayar_id'];
            $model->pasien_id = $data_info_resep['pasien_id'];
            $model->ruangan_id = $post['ruangan_id'];
            $model->reseptur_id = $data_info_resep['reseptur_id'];
            $model->iter = $data_info_resep['iter'];
            $model->totharganetto = $total_harganetto;
            $model->totalhargajual = $total_hargajual;
            $model->tglpenjualan = $now;
            $model->jenispenjualan = DocoConstants::PENJUALAN_RESEP_RS;
            $model->tglresep = $now;
            $model->created_by = $user_login;
            $model->biayaadministrasi = $biayaadministrasi;
            $model->catatan = $keterangan;

            if ($model->validate() && $model->save()) {
                $penjualan_id = $model->penjualanresep_id;
                $obj_array_insert = [];
                $list_obat = $post['list_obat'];

                foreach ($list_obat as $key => $value) {
                    $row = [
                        'ruangan_id' => $post['ruangan_id'],
                        'carabayar_id' => $data_info_resep['carabayar_id'],
                        'pendaftaran_id' => $data_info_resep['pendaftaran_id'],
                        'pasien_id' => $data_info_resep['pasien_id'],
                        'penjamin_id' => $data_info_resep['penjamin_id'],
                        'penjualanresep_id' => $penjualan_id,
                        'tglpelayanan' => $now,
                        'pegawai_id' => $user_login,
                        'racikan_id' => ($value['racikan_id'] == '-') ? 2 : $value['racikan_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'qty_oa' => $value['qty_konversi'],
                        'signa_oa' => (string)$value['signa_id'],
                        'rke' => (empty($value['r_ke']) || $value['r_ke'] == '-') ? 0 : $value['r_ke'],
                        'harganetto_oa' => !empty($value['harganetto']) ? $value['harganetto'] : 0,
                        'hargasatuan_oa' => $value['hargajual']*($value['qty_konversi']/$value['qty']),
                        'hargajual_oa' => $value['subtotal'],
                        'created_by' => $user_login,
                        'additional_data'=> json_encode([
                            'posisi' => isset($value['posisi']) ? $value['posisi'] : 9999
                        ]),
                        'biayaadministrasi' => $post['biayaadministrasi'],
                        'is_ditagihkan' => true
                    ];
                    $row['resepturdetail_id'] = $listResepturId[$value['obatalkes_id']][$row['rke']];
                    $obj_array_insert[] = $row;
                    $detailTrans[] = [
                        'obatalkes_id' => $value['obatalkes_id'],
                        'qty_satuanpakai' => $value['qty'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'harganetto' => $value['harganetto'],
                        'persendiscount' => $value['persendiscount'],
                        'persenppn' => $value['persenppn'],
                        'persenmargin' => $value['persenmargin'],
                        'jmldiscount' => $value['jmldiscount'],
                        'jmlmargin' => $value['jmlmargin'],
                        'jmlppn' => $value['jmlppn'],
                    ];
                }
                /*Parent Transaction OA*/
                $trx_oa = [
                    'primary_key' => 'penjualanresep_id',
                    'penjualanresep_id' => $model->getPrimaryKey(),
                    'pendaftaran_id' => $data_info_resep['pendaftaran_id'],
                    'carabayar_id' => $data_info_resep['carabayar_id'],
                    'penjamin_id' => $data_info_resep['penjamin_id'],
                    'ruangan_id' => $post['ruangan_id'],
                ];
                $obj_oa = [
                    'trx_oa' => $trx_oa,
                    'trx_oa_detail' => $obj_array_insert
                ];
                $return_feature = FeatureTindakanBmhp::createOA($obj_oa,false);
                if(!$return_feature){
                    throw new \Exception("Tidak Dapat Memproses Transaksi Obat Alkes", 1);
                }

                $approve_status = DocoConstants::VAR_AR;
                $sql_update = "UPDATE reseptur_t SET
                    penjualanresep_id = ". $penjualan_id . ",
                    status_reseptur = {$approve_status}
                    WHERE
                        penjualanresep_id is null
                    AND
                        reseptur_id = ". $post['reseptur_id'] ."
                ";
                $db->createCommand($sql_update)->execute();
            }
            $pendaftaran_id = $data_info_resep['pendaftaran_id'];

            $update_tagihan = FeaturePendaftaran::updateTagihan($pendaftaran_id);
            if(!$update_tagihan){
                throw new \Exception("Tidak Dapat Memproses Tagihan Pendaftaran", 1);
            }
            $transaction->commit();
            $getData = PenjualanResep::findOne($penjualan_id);
            $noresep = isset($getData['noresep']) ? $getData['noresep'] : '';
            $return_integrate = IntegrasiAkunting::integrateByNoResep($noresep);
            return ['message' => 'Data Berhasil di simpan', 'id'=>$penjualan_id,'enc' => DocoHelpers::encrypt($penjualan_id), 'nomor' => $noresep];

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateReseptur($resepturId, $list_obat, $del_ResepturDetailId){
        $connection = Yii::$app->db;
        try{
            /*Find iter*/
            $inforesep = InformasiResepturView::find()->where(['reseptur_id'=>$resepturId])->asArray()->one();
            $iter_resep = isset($inforesep->iter) ? $inforesep->iter : 0;

            $new_status_implementasi = '' ;
            $getResepturDetail = ResepturDetail::find(true)->where(['reseptur_id'=>$resepturId])->asArray()->all();
            foreach ($getResepturDetail as $key => $value) {
                $new_status_implementasi =  $value['status_implementasi'];
            }
            $status_implementasi = (int)$new_status_implementasi;
            $arr_delResepturDetailId = [];
            if (count($del_ResepturDetailId) > 0) {
                foreach ($del_ResepturDetailId as $k => $v) {
                    $arr_delResepturDetailId[] = $v;
                }
            }
            if(count($arr_delResepturDetailId) > 0){

                $data = [
                        'is_deleted' => true,
                        'deleted_date' => date('Y-m-d H:i:s'),
                        'deleted_by' => Yii::$app->user->identity->pegawai_id
                        ];
                $condition = [
                            'resepturdetail_id' => $arr_delResepturDetailId
                            ];
                $params = [':resepturdetail_id' => $arr_delResepturDetailId
                            ];
                $result = $connection->createCommand()->update(
                            'resepturdetail_t', $data, $condition
                            )->execute();
                /*$update = ResepturDetail::updateAll([
                            'is_deleted' => true,
                            'deleted_date' => date('Y-m-d H:i:s'),
                            'deleted_by' => Yii::$app->user->identity->pegawai_id
                            ],
                            [
                            'resepturdetail_id' => $arr_delResepturDetailId
                            ]);*/
                /*$modelDetail = ResepturDetail::find()->where(['resepturdetail_id' => $arr_delResepturDetailId])->all();
                foreach ($modelDetail as $key => $value) {
                        $value->delete();*/
            }
 // return [
 //                            'title' => 'ini tracenya',
 //                            'text' => $list_obat,
 //                            'status' => 422
 //                       ];
            $listReseptur = [];
            foreach($list_obat as $key => $value):

                $data                      = [];
                $data['satuaninput_id']    = $value['satuaninput_id'];
                $data['satuan_input']      = $value['satuan_input'];
                $data['satuankonversi_id'] = $value['satuankonversi_id'];
                $data['satuan_konversi']   = $value['satuan_konversi'];
                $data['harga_konversi']    = $value['harga_konversi'];
                $tmpJson                   = json_encode($data);

                if(empty($value['resepturdetail_id'])){
                    $listReseptur[] = [
                        'obatalkes_id' => (int)$value['obatalkes_id'],
                        'satuankecil_id' => (int)$value['satuankecil_id'],
                        'reseptur_id' => (int)$resepturId,
                        'rke' => (empty($value['r_ke']) || $value['r_ke'] == '-') ? NULL : $value['r_ke'],
                        'qty_reseptur' => $value['qty'],
                        'hargasatuan_reseptur' => $value['hargajual'],
                        'harganetto_reseptur' => $value['harganetto'],
                        'hargajual_reseptur' => $value['hargajual']*$value['qty_konversi'],
                        'signa_id' => (empty($value['signa_id']) || $value['signa_id'] == '') ? NULL : (int)$value['signa_id'],
                        'r'=> null,
                        'status_implementasi'=> $status_implementasi,
                        'racikan_id' => ($value['racikan_id'] == '-') ? 2 : $value['racikan_id'] ,
                        'iter' => $iter_resep,
                        'etiket' => $value['etiket'],
                        'qty_konversi' => $value['qty_konversi'],
                        'additional_data' => $tmpJson
                    ];
                }else{
                    $modelResepturDetail = ResepturDetail::findOne( $value['resepturdetail_id'] );
                    if (!empty($modelResepturDetail)) {
                        $modelResepturDetail->obatalkes_id = (int)$value['obatalkes_id'];
                        $modelResepturDetail->satuankecil_id = (int)$value['satuankecil_id'];
                        $modelResepturDetail->reseptur_id = (int)$resepturId;
                        $modelResepturDetail->rke = (empty($value['r_ke']) || $value['r_ke'] == '-') ? NULL : $value['r_ke'];
                        $modelResepturDetail->qty_reseptur = $value['qty'];
                        $modelResepturDetail->hargasatuan_reseptur = $value['hargajual'];
                        $modelResepturDetail->harganetto_reseptur = $value['harganetto'];
                        $modelResepturDetail->hargajual_reseptur = $value['subtotal'];
                        $modelResepturDetail->signa_id = (empty($value['signa_id']) || $value['signa_id'] == '') ? NULL : (int)$value['signa_id'];
                        $modelResepturDetail->r = null;
                        $modelResepturDetail->status_implementasi = $status_implementasi;
                        $modelResepturDetail->racikan_id = ($value['racikan_id'] == '-') ? 2 : $value['racikan_id'];
                        $modelResepturDetail->iter = $iter_resep;
                        $modelResepturDetail->etiket = $value['etiket'];
                        $modelResepturDetail->qty_konversi = $value['qty_konversi'];
                        $modelResepturDetail->additional_data = $tmpJson;

                        $modelResepturDetail->save();
                    }
                }
            endforeach;
            if (count($listReseptur) > 0) {
                ResepturDetail::batchInsert($listReseptur);
            }
            $getReseptur = ResepturDetail::find()->select(['resepturdetail_id', 'obatalkes_id', 'reseptur_id', 'rke'])->where(['reseptur_id'=>$resepturId])->asArray()->all();
            $result = [];
            foreach($getReseptur as $key => $value):
                $rke = !empty($value['rke']) ? $value['rke'] : 0;
                $result[$value['obatalkes_id']][$rke] = $value['resepturdetail_id'];
            endforeach;

            return $result;
        } catch (\yii\db\Exception $e) {
            throw new \yii\db\Exception($e->getMessage(), 1);
        } catch (\Exception $e) {
            throw new \yii\db\Exception($e->getMessage(), 1);
        }
    }

    /**
     * @todo save multiple table, save bebas
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSaveBebas()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $now = date('Y-m-d H:i:s');
            $db = Yii::$app->db;

            $user_login = Yii::$app->user->identity->pegawai_id;
            $tanggal_penjualan = !empty($post['tanggal_penjualan'])
            ? date('Y-m-d H:i:s', strtotime($post['tanggal_penjualan']))
            : date('Y-m-d H:i:s');
            $total_harganetto = 0;
            foreach ($post['list_obat'] as $key => $value) {
                $total_harganetto += $value['harganetto'];
            }
            $tanggalBerlaku = date('Y-m-d');

            $konfig = $connection->createCommand("
            SELECT metodeantrian FROM konfigfarmasi_k
            WHERE tglberlaku >= '{$tanggalBerlaku}'
            AND konfigfarmasi_aktif = true
            AND is_active = true
            ")->queryOne();
            // Mencari Metode dengan nilai default FEFO
            $currentMetode = BLStokObatAlkes::FEFO;
            if ($konfig) {
                $currentMetode = isset($konfig['metodeantrian'])
                ? strtoupper($konfig['metodeantrian']) : BLStokObatAlkes::FEFO;
            }
            $model = new PenjualanResep;
            $model->pasien_id = empty($post['pasien_id']) ? null : $post['pasien_id'];
            $model->pegawai_id = $post['dokter_id'];
            $model->nama_pembeli = $post['nama_pembeli'];
            $model->iter = $post['iter'];
            $model->penjamin_id = $post['penjamin_id'];
            $model->carabayar_id = $post['carabayar_id'];
            $model->ruangan_id = $post['ruangan_id'];
            $model->totharganetto = $total_harganetto;
            $model->totalhargajual = $post['total_obat'];
            $model->biayaadministrasi = $post['biaya_admin'];
            $model->totaltarifservice = $post['jasa_racik'];
            $model->pembulatanharga = $post['pembulatan'];
            $model->tglpenjualan = $tanggal_penjualan;
            $model->jenispenjualan = DocoConstants::PENJUALAN_RESEP_BEBAS;
            $model->tglresep = $tanggal_penjualan;
            $model->created_by = $user_login;
            $model->catatan = $post['catatan'];
            if ($model->validate() && $model->save()) {
                $penjualan_id = $model->penjualanresep_id;
                $obj_array_insert = [];
                foreach ($post['list_obat'] as $key => $value) {
                    $obj_array_insert[$key] = [
                        'ruangan_id' => $post['ruangan_id'],
                        'carabayar_id' => $post['carabayar_id'],
                        'penjamin_id' => $post['penjamin_id'],
                        'pegawai_id' => $post['dokter_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'racikan_id' => $value['racikan_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'penjualanresep_id' => $penjualan_id,
                        'tglpelayanan' => $now,
                        'qty_oa' => $value['qty'],
                        'hargasatuan_oa' => $value['hargajual'],
                        'harganetto_oa' => $value['harganetto'],
                        'hargajual_oa' => $value['subtotal'],
                        'signa_oa' => $value['signa'],
                        'created_by' => $user_login,
                        'signa_oa' => $value['signa_id'],
                        'created_by' => $user_login,
                        'additional_data' => json_encode([
                            'posisi' => isset($value['posisi']) ? $value['posisi'] : 9999
                        ])
                    ];
                    $detailTrans[] = [
                        'obatalkes_id' => $value['obatalkes_id'],
                        'qty_satuanpakai' => $value['qty'],
                        'satuankecil_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                        'harganetto' => $value['harganetto'],
                        'persendiscount' => $value['persendiscount'],
                        'persenppn' => $value['persenppn'],
                        'persenmargin' => $value['persenmargin'],
                        'jmldiscount' => $value['jmldiscount'],
                        'jmlmargin' => $value['jmlmargin'],
                        'jmlppn' => $value['jmlppn'],
                    ];
                    if (!empty($value['r_ke'])) {
                        $obj_array_insert[$key]['rke'] = $value['r_ke'];
                    }
                }

                ObatAlkesPasien::batchInsert($obj_array_insert);
                $getAlkesPasien = ObatAlkesPasien::find()->select(['penjualanresep_id', 'obatalkespasien_id','obatalkes_id'])->where(['penjualanresep_id'=>$penjualan_id])->asArray()->all();
                $dataAlkes = [];
                foreach($getAlkesPasien as $key => $value):
                    $dataAlkes[$value['obatalkes_id']] = $value;
                endforeach;
                foreach($detailTrans as $key => $value):
                    $detailTrans[$key]['obatalkespasien_id'] = $dataAlkes[$value['obatalkes_id']]['obatalkespasien_id'];
                endforeach;
                BLStokObatAlkes::$distribusi = false;
                if ($currentMetode === BLStokObatAlkes::FEFO) {
                    $methode = BLStokObatAlkes::methodeFEFO($detailTrans,$now);
                } else {
                    $methode = BLStokObatAlkes::methodeFIFO($detailTrans,$now);
                }

                $transaction->commit();
                $getData = PenjualanResep::findOne($penjualan_id);
                $noresep = isset($getData['noresep']) ? $getData['noresep'] : '';
                $this->integrate($noresep);
                return ['message' => 'Data Berhasil di simpan', 'id' => $penjualan_id, 'nomor' => $noresep];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @todo save resep bebas, modified from save bebas
     * @author Ardi Pratama
     */
    public function actionSaveResepBebas() {
        try {
            $now = date('Y-m-d H:i:s');
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $request = Yii::$app->request;
            $post = $request->post();

            $user_login = Yii::$app->user->identity->pegawai_id;
            $tanggal_penjualan = !empty($post['tanggal_penjualan'])
            ? date('Y-m-d H:i:s', strtotime($post['tanggal_penjualan']))
            : date('Y-m-d H:i:s');
            $total_harganetto = 0;
            foreach ($post['list_obat'] as $key => $value) {
                $total_harganetto += empty($value['harganetto'])?0:$value['harganetto'];
            }

            $racikanKode = [];
            foreach ($post['list_obat'] as $key => $value) {
                $total_harganetto += empty($value['harganetto'])?0:$value['harganetto'];
                $racikanKode[] = $value['racikan_id'];
            }

            $model = new PenjualanResep;
            $model->pasien_id = empty($post['pasien_id']) ? null : $post['pasien_id'];
            $model->pegawai_id = $post['pegawai_id'];
            $model->nama_pembeli = $post['nama_pembeli'];
            $model->iter = $post['iter'];
            $model->penjamin_id = $post['penjamin_id'];
            $model->carabayar_id = $post['carabayar_id'];
            $model->ruangan_id = $post['ruangan_id'];
            $model->totharganetto = $total_harganetto;
            $model->totalhargajual = empty($post['total_obat'])?0:$post['total_obat'];
            $model->biayaadministrasi = $post['biayaadministrasi'];
            $model->tglpenjualan = $tanggal_penjualan;
            $model->jenispenjualan = DocoConstants::PENJUALAN_RESEP_BEBAS;
            $model->tglresep = $tanggal_penjualan;
            $model->created_by = $user_login;
            $antrian_id = self::generateAntrian($model, $racikanKode);
            $model->antrian_id = $antrian_id;
            $model->catatan = $post['catatan'];
            if ($model->validate() && $model->save()) {
                $penjualan_id = $model->penjualanresep_id;
                $obj_array_insert = [];
                foreach ($post['list_obat'] as $key => $value) {
                    $obj_array_insert[$key] = [
                        'ruangan_id' => $post['ruangan_id'],
                        'carabayar_id' => $post['carabayar_id'],
                        'penjamin_id' => $post['penjamin_id'],
                        'pegawai_id' => $post['pegawai_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'racikan_id' => $value['racikan_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'penjualanresep_id' => $penjualan_id,
                        'tglpelayanan' => $now,
                        'qty_oa' => $value['qty_konversi'],
                        'hargasatuan_oa' => empty($value['harga'])?0:$value['harga'],
                        'harganetto_oa' => empty($value['harganetto'])?0:$value['harganetto'],
                        'hargajual_oa' => empty($value['subtotal'])?0:$value['subtotal'],
                        'signa_oa' => $value['signa'],
                        'created_by' => $user_login,
                        'signa_oa' => $value['signa_id'],
                        'created_by' => $user_login,
                        'additional_data' => json_encode([
                            'posisi' => isset($value['posisi']) ? $value['posisi'] : 9999,
                            'qty_input' => $value['qty'],
                            'satuaninput_id' => isset($value['satuaninput_id']) ? $value['satuaninput_id'] : null,
                            'satuan_input' => isset($value['satuan_input']) ? $value['satuan_input'] : null,
                            'satuankonversi_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : null,
                            'satuan_konversi' => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : null,
                            'harga_konversi' => isset($value['harga_konversi']) ? $value['harga_konversi'] : null,
                            'nilai_konversi' => isset($value['nilai_konversi']) ? $value['nilai_konversi'] : null,
                        ]),
                        'is_ditagihkan' => true,
                        'etiket' => $value['catatan']
                    ];
                    $detailTrans[] = [
                        'obatalkes_id' => $value['obatalkes_id'],
                        'qty_satuanpakai' => $value['qty'],
                        'satuankecil_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                        'harganetto' => empty($value['harganetto'])?0:$value['harganetto'],
                        'persendiscount' => empty($value['persendiscount'])?0:$value['persendiscount'],
                        'persenppn' => empty($value['persenppn'])?0:$value['persenppn'],
                        'persenmargin' => empty($value['persenmargin'])?0:$value['persenmargin'],
                        'jmldiscount' => empty($value['jmldiscount'])?0:$value['jmldiscount'],
                        'jmlmargin' => empty($value['jmlmargin'])?0:$value['jmlmargin'],
                        'jmlppn' => empty($value['jmlppn'])?0:$value['jmlppn'],
                    ];
                    if (!empty($value['r_ke'])) {
                        $obj_array_insert[$key]['rke'] = $value['r_ke'];
                    }
                }
                /*Parent Transaction OA*/
                $trx_oa = [
                    'primary_key' => 'penjualanresep_id',
                    'penjualanresep_id' => $model->getPrimaryKey(),
                    'carabayar_id' => $post['carabayar_id'],
                    'penjamin_id' => $post['penjamin_id'],
                    'ruangan_id' => $post['ruangan_id'],
                    'antrian_id' => $antrian_id,
                ];
                $obj_oa = [
                    'trx_oa' => $trx_oa,
                    'trx_oa_detail' => $obj_array_insert
                ];
                $return_feature = FeatureTindakanBmhp::createOA($obj_oa,false);
                if (is_array($return_feature)) {
                    return [
                        'status' => 422,
                        'text' => 'Terdapat Obat yang tidak dapat di proses',
                        'message' => 'Terjadi Kesalahan',
                        'list_obat_tidak_cukup' => $return_feature,
                    ];
                }
                if(!$return_feature){
                    throw new \Exception("Tidak Dapat Memproses Transaksi Obat Alkes", 1);
                }
                $transaction->commit();
                $getData = PenjualanResep::findOne($penjualan_id);
                $noresep = isset($getData['noresep']) ? $getData['noresep'] : '';
                $return_integrate = IntegrasiAkunting::integrateByNoResep($noresep);
                // $this->integrate($noresep);
                return ['message' => 'Data Berhasil di simpan', 'id' => DocoHelpers::encrypt($penjualan_id), 'nomor' => $noresep];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'text' => 'Kesalahan',
                'message' => $e->getMessage(),
                'list_obat_tidak_cukup'=>[]
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'text' => 'Kesalahan',
                'message' => $e->getMessage(),
                'list_obat_tidak_cukup'=>[]
            ];
        }
    }

    /**
     * @todo save multiple table, save karyawan
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public function actionSaveKaryawan()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $now = date('Y-m-d H:i:s');
            $db = Yii::$app->db;
            $user_login = Yii::$app->user->identity->pegawai_id;
            $tanggal_penjualan = !empty($post['tanggal_penjualan'])
                ? date('Y-m-d H:i:s', strtotime($post['tanggal_penjualan']))
                : date('Y-m-d H:i:s');
            $total_harganetto = 0;
            foreach ($post['list_obat'] as $key => $value) {
                $total_harganetto += $value['harganetto'];
            }
            $tanggalBerlaku = date('Y-m-d');
            $konfig = $connection->createCommand("
                            SELECT metodeantrian FROM konfigfarmasi_k
                            WHERE tglberlaku >= '{$tanggalBerlaku}'
                            AND konfigfarmasi_aktif = true
                            AND is_active = true
                        ")->queryOne();
            // Mencari Metode dengan nilai default FEFO
            $currentMetode = BLStokObatAlkes::FEFO;
            if ($konfig) {
                $currentMetode = isset($konfig['metodeantrian'])
                                    ? strtoupper($konfig['metodeantrian']) : BLStokObatAlkes::FEFO;
            }
            $model = new PenjualanResep;
            $model->karyawan_id = empty($post['pasien_id']) ? null : $post['pasien_id'];
            $model->pegawai_id = empty($post['pegawai_id']) ? $user_login : $post['pegawai_id'];
            $model->iter = $post['iter'];
            $model->penjamin_id = $post['penjamin_id'];
            $model->carabayar_id = $post['carabayar_id'];
            $model->ruangan_id = $post['ruangan_id'];
            $model->totharganetto = $total_harganetto;
            $model->totalhargajual = empty($post['total_obat'])?0:$post['total_obat'];
            $model->biayaadministrasi = $post['biaya_admin'];
            $model->totaltarifservice = $post['jasa_racik'];
            $model->pembulatanharga = $post['pembulatan'];
            $model->tglpenjualan = $tanggal_penjualan;
            $model->jenispenjualan = DocoConstants::PENJUALAN_RESEP_KARYAWAN;
            $model->tglresep = $tanggal_penjualan;
            $model->created_by = $user_login;
            $model->catatan = $post['catatan'];
            if ($model->validate() && $model->save()) {
                $penjualan_id = $model->penjualanresep_id;

                $obj_array_insert = [];
                $forMethod = [];
                foreach ($post['list_obat'] as $key => $value) {
                    $obj_array_insert[$key] = [
                        'ruangan_id' => $post['ruangan_id'],
                        'carabayar_id' => $post['carabayar_id'],
                        'penjamin_id' => $post['penjamin_id'],
                        'pegawai_id' => empty($post['pegawai_id']) ? $user_login : $post['pegawai_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'racikan_id' => $value['racikan_id'],
                        'penjualanresep_id' => $penjualan_id,
                        'tglpelayanan' => $now,
                        'qty_oa' => $value['qty_konversi'],
                        'hargasatuan_oa' => empty($value['hargajual'])?0:$value['hargajual'],
                        'harganetto_oa' => empty($value['harganetto'])?0:$value['harganetto'],
                        'hargajual_oa' => empty($value['subtotal'])?0:$value['subtotal'],
                        'signa_oa' => $value['signa_id'],
                        'created_by' => $user_login,
                        'additional_data' => json_encode([
                            'posisi' => isset($value['posisi']) ? $value['posisi'] : 9999
                        ])
                    ];
                    $detailTrans[] = [
                        'obatalkes_id' => $value['obatalkes_id'],
                        'qty_satuanpakai' => $value['qty'],
                        'satuankecil_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                        'harganetto' => empty($value['harganetto'])?0:$value['harganetto'],
                        'persendiscount' => empty($value['persendiscount'])?0:$value['persendiscount'],
                        'persenppn' => empty($value['persenppn'])?0:$value['persenppn'],
                        'persenmargin' => empty($value['persenmargin'])?0:$value['persenmargin'],
                        'jmldiscount' => empty($value['jmldiscount'])?0:$value['jmldiscount'],
                        'jmlmargin' => empty($value['jmlmargin'])?0:$value['jmlmargin'],
                        'jmlppn' => empty($value['jmlppn'])?0:$value['jmlppn'],
                    ];
                    if (!empty($value['r_ke'])) {
                        $obj_array_insert[$key]['rke'] = $value['r_ke'];
                    }
                }
                ObatAlkesPasien::batchInsert($obj_array_insert);
                $getAlkesPasien = ObatAlkesPasien::find()->select(['penjualanresep_id', 'obatalkespasien_id','obatalkes_id'])->where(['penjualanresep_id'=>$penjualan_id])->asArray()->all();
                $dataAlkes = [];
                foreach($getAlkesPasien as $key => $value):
                    $dataAlkes[$value['obatalkes_id']] = $value;
                endforeach;
                foreach($detailTrans as $key => $value):
                    $detailTrans[$key]['obatalkespasien_id'] = $dataAlkes[$value['obatalkes_id']]['obatalkespasien_id'];
                endforeach;
                BLStokObatAlkes::$distribusi = false;
                if ($currentMetode === BLStokObatAlkes::FEFO) {
                    $methode = BLStokObatAlkes::methodeFEFO($detailTrans,$now);
                } else {
                    $methode = BLStokObatAlkes::methodeFIFO($detailTrans,$now);
                }

                $transaction->commit();
                $getData = PenjualanResep::findOne($penjualan_id);
                $noresep = isset($getData['noresep']) ? $getData['noresep'] : '';
                return ['message' => 'Data Berhasil di simpan', 'id'=>$penjualan_id,'enc' => DocoHelpers::encrypt($penjualan_id), 'nomor' => $noresep];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSaveResepKaryawan()
    {
        $_jenis_resep_karyawan = DocoConstants::PENJUALAN_RESEP_KARYAWAN;

        try {
            $now = date('Y-m-d H:i:s');
            $connection = Yii::$app->db;
            $request = Yii::$app->request;
            $post = $request->post();
            $user_login = Yii::$app->user->identity->pegawai_id;

            $konfig_farmasi = $this->getKonfigFarmasi();

            $carabayar_id   = isset($konfig_farmasi['carabayar_id'])
             ? (string) $konfig_farmasi['carabayar_id'] : $post['carabayar_id'];
            $penjamin_id    = isset($konfig_farmasi['penjaminkaryawan_id'])
             ? (string) $konfig_farmasi['penjaminkaryawan_id'] : $post['penjamin_id'];

            $tanggal_penjualan = !empty($post['tanggal_penjualan'])
                ? date('Y-m-d H:i:s', strtotime($post['tanggal_penjualan']))
                : $now;

            $post['list_obat'] = is_array($post['list_obat']) ? $post['list_obat'] :
                json_decode($post['list_obat'], true);

            $total_harganetto = 0;
            foreach ($post['list_obat'] as $key => $value) {
                $total_harganetto += $value['harganetto'];
            }

            $racikanKode = [];
            foreach ($post['list_obat'] as $key => $value) {
                $total_harganetto += $value['harganetto'];
                $racikanKode[] = $value['racikan_id'];
            }

            $model = new PenjualanResep;
            $model->pegawai_id        = $post['pegawai_id'];
            $model->karyawan_id       = $post['karyawan_id'];
            $model->nama_pembeli      = $post['nama_pembeli'];
            $model->iter              = $post['iter'];
            $model->penjamin_id       = $penjamin_id;
            $model->carabayar_id      = $carabayar_id;
            $model->ruangan_id        = $post['ruangan_id'];
            $model->totharganetto     = $total_harganetto;
            $model->totalhargajual    = $post['total_obat'];
            $model->biayaadministrasi = $post['biayaadministrasi'];
            $model->tglpenjualan      = $tanggal_penjualan;
            $model->jenispenjualan    = $_jenis_resep_karyawan;
            $model->tglresep          = $tanggal_penjualan;
            $model->created_by        = $user_login;
            $antrian_id               = self::generateAntrian($model, $racikanKode);
            $model->antrian_id        = $antrian_id;
            $model->catatan           = $post['catatan'];

            $transaction = $connection->beginTransaction();
            if ($model->validate() && $model->save()) {
                $penjualan_id = $model->penjualanresep_id;
                $obj_array_insert = [];
                foreach ($post['list_obat'] as $key => $value) {
                    $obj_array_insert[$key] = [
                        'carabayar_id'      => $carabayar_id,
                        'created_by'        => $user_login,
                        'created_by'        => $user_login,
                        'is_ditagihkan'     => true,
                        'penjamin_id'       => $penjamin_id,
                        'penjualanresep_id' => $penjualan_id,
                        'tglpelayanan'      => $now,
                        'pegawai_id'        => $post['pegawai_id'],
                        'ruangan_id'        => $post['ruangan_id'],
                        'etiket'            => $value['catatan'],
                        'hargajual_oa'      => empty($value['subtotal'])?0:$value['subtotal'],
                        'harganetto_oa'     => empty($value['harganetto'])?0:$value['harganetto'],
                        'hargasatuan_oa'    => empty($value['harga'])?0:$value['harga'],
                        'obatalkes_id'      => $value['obatalkes_id'],
                        'qty_oa'            => $value['qty_konversi'],
                        'racikan_id'        => $value['racikan_id'],
                        'satuankecil_id'    => $value['satuankecil_id'],
                        'signa_oa'          => $value['signa'],
                        'signa_oa'          => $value['signa_id'],

                        'additional_data' => json_encode([
                            'harga_konversi'    => isset($value['harga_konversi']) ? $value['harga_konversi'] : null,
                            'nilai_konversi'    => isset($value['nilai_konversi']) ? $value['nilai_konversi'] : null,
                            'posisi'            => isset($value['posisi']) ? $value['posisi'] : 9999,
                            'qty_input'         => $value['qty'],
                            'satuan_input'      => isset($value['satuan_input']) ? $value['satuan_input'] : null,
                            'satuan_konversi'   => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : null,
                            'satuaninput_id'    => isset($value['satuaninput_id']) ? $value['satuaninput_id'] : null,
                            'satuankonversi_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : null,
                        ]),
                    ];

                    $detailTrans[] = [
                        'harganetto'      => empty($value['harganetto'])?0:$value['harganetto'],
                        'persendiscount'  => empty($value['persendiscount'])?0:$value['persendiscount'],
                        'persenppn'       => empty($value['persenppn'])?0:$value['persenppn'],
                        'persenmargin'    => empty($value['persenmargin'])?0:$value['persenmargin'],
                        'jmldiscount'     => empty($value['jmldiscount'])?0:$value['jmldiscount'],
                        'jmlmargin'       => empty($value['jmlmargin'])?0:$value['jmlmargin'],
                        'jmlppn'          => empty($value['jmlppn'])?0:$value['jmlppn'],
                        'obatalkes_id'    => $value['obatalkes_id'],
                        'qty_satuanpakai' => $value['qty'],
                        'satuankecil_id'  => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                    ];
                    if (!empty($value['r_ke'])) {
                        $obj_array_insert[$key]['rke'] = $value['r_ke'];
                    }
                }

                $trx_oa = [
                    'primary_key'       => 'penjualanresep_id',
                    'penjualanresep_id' => $model->getPrimaryKey(),
                    'antrian_id'        => $antrian_id,
                    'carabayar_id'      => $carabayar_id,
                    'penjamin_id'       => $penjamin_id,
                    'ruangan_id'        => $post['ruangan_id'],
                ];

                $obj_oa = [
                    'trx_oa'        => $trx_oa,
                    'trx_oa_detail' => $obj_array_insert
                ];
                $return_feature = FeatureTindakanBmhp::createOA($obj_oa,false);

                if (is_array($return_feature)) {
                    return [
                        'status'                => 422,
                        'text'                  => 'Terdapat Obat yang tidak dapat di proses',
                        'message'               => 'Terjadi Kesalahan',
                        'list_obat_tidak_cukup' => $return_feature,
                    ];
                }
                if(!$return_feature){
                    throw new \Exception("Tidak Dapat Memproses Transaksi Obat Alkes", 1);
                }

                $transaction->commit();

                $getData = PenjualanResep::findOne($penjualan_id);
                $noresep = isset($getData['noresep']) ? $getData['noresep'] : '';
                $return_integrate = IntegrasiAkunting::integrateByNoResep($noresep);
                return ['message' => 'Data Berhasil di simpan', 'id' => DocoHelpers::encrypt($penjualan_id), 'nomor' => $noresep];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'list_obat_tidak_cukup'=>[]
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'list_obat_tidak_cukup'=>[]
            ];
        }
    }

    public function actionSaveResepPasien() {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $post = $request->post();

            $user_login = Yii::$app->user->identity->pegawai_id;
            $tanggal_penjualan = !empty($post['tanggal_penjualan'])
            ? date('Y-m-d H:i:s', strtotime($post['tanggal_penjualan']))
            : date('Y-m-d H:i:s');
            $total_harganetto = 0;
            $racikanKode = [];

            $list_obat = is_array($post['list_obat']) ? $post['list_obat'] : json_decode($post['list_obat'], true);

            foreach ($list_obat as $key => $value) {
                $total_harganetto += $value['harganetto'];
                $racikanKode[] = $value['racikan_id'];
            }

            $model = new PenjualanResep;
            $model->pasien_id = empty($post['pasien_id']) ? null : $post['pasien_id'];
            $model->pegawai_id = $post['pegawai_id'];
            $model->pegawairesep_id = empty($post['pegawai_id']) ? $user_login : $post['pegawai_id'];
            $model->pendaftaran_id = $post['pendaftaran_id'];
            $model->iter = $post['iter'];
            $model->penjamin_id = $post['penjamin_id'];
            $model->carabayar_id = $post['carabayar_id'];
            $model->ruangan_id = $post['ruangan_id'];
            $model->totharganetto = $total_harganetto;
            $model->totalhargajual = $post['total_obat'];
            $model->tglpenjualan = $tanggal_penjualan;
            $model->jenispenjualan = DocoConstants::PENJUALAN_RESEP_RS;
            $model->tglresep = $tanggal_penjualan;
            $model->created_by = $user_login;
            $model->catatan = $post['catatan'];
            $antrian_id = self::generateAntrian($model, $racikanKode);
            $model->antrian_id = $antrian_id;
            $model->biayaadministrasi = $post['biayaadministrasi'];

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            if ($model->validate() && $model->save()) {
                $penjualan_id = $model->penjualanresep_id;
                $obj_array_insert = [];
                foreach ($list_obat as $key => $value) {
                    $obj_array_insert[$key] = [
                        'ruangan_id' => $post['ruangan_id'],
                        'carabayar_id' => $post['carabayar_id'],
                        'penjamin_id' => $post['penjamin_id'],
                        'pendaftaran_id' => $post['pendaftaran_id'],
                        'pegawai_id' => $post['pegawai_id'],
                        'satuankecil_id' => $value['satuankecil_id'],
                        'racikan_id' => $value['racikan_id'],
                        'obatalkes_id' => $value['obatalkes_id'],
                        'penjualanresep_id' => $penjualan_id,
                        'tglpelayanan' => $now,
                        'qty_oa' => $value['qty_konversi'],
                        'hargajual_oa' => empty($value['subtotal'])?0:$value['subtotal'],
                        'harganetto_oa' => empty($value['harganetto'])?0:$value['harganetto'],
                        'hargasatuan_oa' => empty($value['harga'])?0:$value['harga'],
                        'signa_oa' => $value['signa'],
                        'created_by' => $user_login,
                        'signa_oa' => $value['signa_id'],
                        'created_by' => $user_login,
                        'etiket' => $value['etiket'],
                        'biayaadministrasi' => $post['biayaadministrasi'],
                        'additional_data' => json_encode([
                            'posisi' => isset($value['posisi']) ? $value['posisi'] : 9999,
                            'qty_input' => $value['qty'],
                            'satuaninput_id' => isset($value['satuaninput_id']) ? $value['satuaninput_id'] : null,
                            'satuan_input' => isset($value['satuan_input']) ? $value['satuan_input'] : null,
                            'satuankonversi_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : null,
                            'satuan_konversi' => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : null,
                            'harga_konversi' => isset($value['harga_konversi']) ? $value['harga_konversi'] : null,
                            'nilai_konversi' => isset($value['nilai_konversi']) ? $value['nilai_konversi'] : null,
                        ]),
                        'is_ditagihkan' => true
                    ];
                    $detailTrans[] = [
                        'obatalkes_id' => $value['obatalkes_id'],
                        'qty_satuanpakai' => $value['qty'],
                        'satuankecil_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                        'harganetto' => empty($value['harganetto'])?0:$value['harganetto'],
                        'persendiscount' => empty($value['persendiscount'])?0:$value['persendiscount'],
                        'persenppn' => empty($value['persenppn'])?0:$value['persenppn'],
                        'persenmargin' => empty($value['persenmargin'])?0:$value['persenmargin'],
                        'jmldiscount' => empty($value['jmldiscount'])?0:$value['jmldiscount'],
                        'jmlmargin' => empty($value['jmlmargin'])?0:$value['jmlmargin'],
                        'jmlppn' => empty($value['jmlppn'])?0:$value['jmlppn']
                    ];
                    if (!empty($value['r_ke'])) {
                        $obj_array_insert[$key]['rke'] = $value['r_ke'];
                    }
                }

                /*Parent Transaction OA*/
                $trx_oa = [
                    'primary_key' => 'penjualanresep_id',
                    'penjualanresep_id' => $model->getPrimaryKey(),
                    'carabayar_id' => $post['carabayar_id'],
                    'penjamin_id' => $post['penjamin_id'],
                    'ruangan_id' => $post['ruangan_id'],
                    'pasien_id' => $post['pasien_id'],
                    'antrian_id' => $antrian_id,
                    'pendaftaran_id' => $post['pendaftaran_id']
                ];

                $obj_oa = [
                    'trx_oa' => $trx_oa,
                    'trx_oa_detail' => $obj_array_insert
                ];

                $return_feature = FeatureTindakanBmhp::createOA($obj_oa,false);
                if (is_array($return_feature)) {
                    return [
                        'status' => 422,
                        'text' => 'Terdapat Obat yang tidak dapat di proses',
                        'message' => 'Terjadi Kesalahan',
                        'list_obat_tidak_cukup' => $return_feature,
                    ];
                }

                if(!$return_feature){
                    throw new \Exception("Tidak Dapat Memproses Transaksi Obat Alkes", 1);
                }

                $transaction->commit();

                $getData = PenjualanResep::findOne($penjualan_id);
                $noresep = isset($getData['noresep']) ? $getData['noresep'] : '';
                $return_integrate = IntegrasiAkunting::integrateByNoResep($noresep);
                // $this->integrate($noresep);
                return ['message' => 'Data Berhasil di simpan', 'id' => DocoHelpers::encrypt($penjualan_id), 'nomor' => $noresep];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'list_obat_tidak_cukup' => []
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'list_obat_tidak_cukup' => []
            ];
        }
    }

    public function generateAntrian($modelResep, $racikanKode) {
        $const = DocoConstants::VAR_FA_NR;
        $racikanType = "NR";
        $racikanKode = array_unique($racikanKode);
        if(count($racikanKode) > 1 || $racikanKode[0] == "OR"){
            $const = DocoConstants::VAR_FA_R;
            $racikanType = "OR";
        }
        $list_racikan = Racikan::find()->all();
        $list_racikan = ArrayHelper::map($list_racikan, 'racikan_singkatan', 'racikan_id');

        $data_konfigantrianfarmasi = KonfigAntrianFarmasi::find()->where(['fungsiantrian_id'=>$const,'is_default' => true])->one();
        $modelAntrian = new Antrian;
        $modelAntrian->pendaftaran_id = $modelResep->pendaftaran_id;
        $modelAntrian->ruangan_id = $modelResep->ruangan_id;
        $modelAntrian->tgl_antrian = date('Y-m-d H:i:s');
        $modelAntrian->jenisantrian_id = DocoConstants::VAR_JA_F;
        $modelAntrian->racikan_id = $list_racikan[$racikanType];
        $fungsiantrian_id = ($data_konfigantrianfarmasi->fungsiantrian_id) ? $data_konfigantrianfarmasi->fungsiantrian_id : null;

        $modelAntrian->fungsiantrian_id = $fungsiantrian_id;
        $modelAntrian->save(false);
        $antrian_id = $modelAntrian->antrian_id;

        return $antrian_id;
    }

    /**
    * @controller actionPrintResepRs
    * @attribute #data_obat# => print

    **/
    public function actionPrintResepRs()
    {
        $id = $_GET['id'];
        $noresep = @$_GET['noresep'];
        $query =  InformasiResepturView::find()->where(['reseptur_id' => $id]);
        $query = InformasiResepturView::find()->select([
            'reseptur_id',
            'noresep',
            'tglreseptur',
            'nama_pasien',
            'instalasi_reseptur',
            'ruangan_reseptur',
            'no_pendaftaran',
            'nama_pegawai',
            'pendaftaran_id',
            'carabayar_id',
            'penjamin_id',
            'pasien_id',
            'pasienadmisi_id',
            'penjualanresep_id',
            // 'kelaspelayanan_id',
            // 'instalasi_nama',
            // 'ruangan_nama',
            'carabayar_nama',
            'penjamin_nama'
        ]);
        $data_detail = $query->asArray()->one();

        $model = new InfoResepturDetailView;
        $query_obat = $model::find(true);
        $query_obat->select(['obatalkes_nama', 'hargajual_satuan', 'totalharga_jual', 'qty_reseptur', 'obatalkes_id', 'signa_nama']);
        $query_obat->where(['reseptur_id' => $id]);
        $data_obat = $query_obat->asArray()->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#data_obat#' => $this->renderPartial('index',
                [
                    'data' => $data_detail,
                    'data_obat' => $data_obat,
                ]
        ),
        ];
        $print->Output();
    }

    /**
    * @controller actionCetakPdf
    * @attribute #dataTable# => Menampilkan data table Informasi Obat Pasien
    * @attribute #noresep# => Menampilkan data noresep
    * @attribute #tanggal_resep# => Menampilkan data Tanggal Resep
    * @attribute #nama_pasien# => Menampilkan data Nama Pasien
    * @attribute #nama_pegawai# => Menampilkan data Nama Pegawai Dokter Resep
    * @attribute #caraBayar# => Menampilkan data Cara Bayar
    * @attribute #penjamin# => Menampilkan data Nama Penjamin
    * @attribute #instalasi_nama# => Menampilkan data Nama Instalasi
    * @attribute #ruangan_nama# => Menampilkan data Nama Ruangan
    * @attribute #iter# => Menampilkan data Iter
    * @attribute #diagnosa# => Menampilkan data diagnosa
    * @attribute #alergi# => Menampilkan data alergi
    * @attribute #catatan# => Menampilkan data Catatan
    **/
    public function actionCetakPdf()
    {
        $model = new PenjualanResep;
        $request = Yii::$app->request;
        $get = $request->get();
        if (isset($get['id']) && isset($get['type'])) {
            $id = $get['id'];
            $type = $get['type'];
            $penjualan_obat = $model->getDataPenjualanById($id, $type);
            $penjualan_obat_detail = $model->getDetailPenjualanById($id, $type);
            $arr_PenjualanObatDetail = [];
            $reseptur_id = null;
            foreach ($penjualan_obat_detail as $key => $value) {
                $data['penjualanresep_id']    = $value['penjualanresep_id'];
                $data['resepturdetail_id']    = $value['resepturdetail_id'];
                $data['racikan_id']           = $value['racikan_id'];
                $data['reseptur_id']          = $value['reseptur_id'];
                $data['rke']                  = $value['rke'];
                $data['obatalkes_nama']       = $value['obatalkes_nama'];
                $data['signa_oa']             = $value['signa_oa'];
                $data['qty_oa']               = $value['qty_oa'];
                $data['hargajual_oa']         = $value['hargajual_oa'];
                $data['hargasatuan_oa']       = $value['hargasatuan_oa'];
                $data['posisi']               = $value['posisi'];
                $data['satuan_input']         = $value['satuan_input'];
                $data['hargasatuan_reseptur'] = $value['hargasatuan_reseptur'];
                $data['hargajual_reseptur']   = $value['hargajual_reseptur'];
                $data['qty_reseptur']         = $value['qty_reseptur'];
                $data['etiket_reseptur']      = $value['etiket_reseptur'];
                $arr_PenjualanObatDetail[] = $data;
                $reseptur_id = $value['reseptur_id'];
            }

            //Aris ToDo
            $data_alergi = RiwayatAlergiView::find()->where([
                'pasien_id' => $penjualan_obat['pasien_id']
            ])->all();

            $no = 1;
            $string = '<ul>';
            foreach ($data_alergi as $key => $value) {
                $valAlergi = $value['riwayat_alergi'];
                $replace = str_replace("-",",",$valAlergi);
                $replace = preg_replace('/["\[\]]/i', "", $replace);
                $list = explode(",",$replace);
                if (is_array($list)) {
                    foreach ($list as $val) {
                        if($val != ""){
                            $string .= '<li> '. $no .'. '. $val .'</li>';
                            $no++;
                        }
                    }
                }
            }
            $string .= '</ul>';
            //Aris ToDo

            //Aris ToDo
            $data_diagnosa = InformasiResepturView::find()->where([
                'reseptur_id' => $reseptur_id
            ])->one();
            //$diagnosa = $data_diagnosa['diagnosa_nama'];
            $diagnosa = !empty($data_diagnosa['diagnosa_id']) ? $data_diagnosa['diagnosa_nama'] : $data_diagnosa['diagnosa_text'];
            //Aris ToDo

            if ($penjualan_obat['jenispenjualan'] == DocoConstants::PENJUALAN_RESEP_BEBAS) {
                $nama_pasien = !empty($penjualan_obat['nama_pasien'])
                    ? $penjualan_obat['nama_pasien']
                    : $penjualan_obat['nama_pembeli'];
            } elseif ($penjualan_obat['jenispenjualan'] == DocoConstants::PENJUALAN_RESEP_KARYAWAN) {
                $nama_pasien = $penjualan_obat['nama_karyawan'];
            } else {
                $nama_pasien = $penjualan_obat['nama_pasien'];
            }

            if ($type == 344) {
                $title = 'Rincian Tagihan Penjualan Resep Pasien Rumah Sakit';
            }
            elseif ($type == 343 ) {
                $title = 'Rincian Tagihan Penjualan Resep Bebas';

            }elseif ($type == 345) {
                $title = 'Rincian Tagihan Penjualan Resep karyawan';
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#dataTable#' => $this->renderPartial('cetak_resep', [
                    'detail' => $arr_PenjualanObatDetail,
                    'biayaadministrasi' => !empty($penjualan_obat['biayaadministrasi']) ? $penjualan_obat['biayaadministrasi'] : 0,
                    'totaltarifservice' => !empty($penjualan_obat['totaltarifservice']) ? $penjualan_obat['totaltarifservice'] : 0,
                    'pembulatanharga' => !empty($penjualan_obat['pembulatanharga']) ? $penjualan_obat['pembulatanharga'] : 0,
                    'totalhargajual' => !empty($penjualan_obat['totalhargajual']) ? $penjualan_obat['totalhargajual'] : 0,
                    'type' => !empty($type) ? $type : 0,
                ]),
                '#catatan#' => !empty($penjualan_obat['catatan']) ? $penjualan_obat['catatan'] : '-',
                '#noresep#' => !empty($penjualan_obat['noresep']) ? $penjualan_obat['noresep'] : '-',
                '#tanggal_resep#' => !empty($penjualan_obat['tglresep']) ? date('d M Y', strtotime($penjualan_obat['tglresep'])) : '-',
                '#nama_pasien#' => !empty($nama_pasien) ? $nama_pasien : '-',
                '#nama_pegawai#' => !empty($penjualan_obat['nama_pegawai']) ? $penjualan_obat['nama_pegawai'] : '-',
                '#caraBayar#'=>!empty($penjualan_obat['carabayar_nama']) ? $penjualan_obat['carabayar_nama'] : '-',
                '#penjamin#'=>!empty($penjualan_obat['penjamin_nama']) ? $penjualan_obat['penjamin_nama'] : '-',
                '#instalasi_nama#'=>!empty($penjualan_obat['instalasi_nama']) ? $penjualan_obat['instalasi_nama'] : '-',
                '#ruangan_nama#'=>!empty($penjualan_obat['ruangan_nama']) ? $penjualan_obat['ruangan_nama'] : '-',
                '#iter#'=>!empty($penjualan_obat['iter']) ? $penjualan_obat['iter'] : 0,
                '#diagnosa#'=> !empty($diagnosa) ? $diagnosa : '-',
                '#alergi#'=> !empty($string) ? $string : '-',
                '#title#'=>!empty($title) ? $title : '-',

            ];
            $print->Output();
        }

    }

    public function integrate($noresep)
    {
        try{
            $resep = SyncPengeluaranobat::find()->where(['nomor' => $noresep])->all();
            $config = KonfigSystem::find()->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

            if($config->is_akunting != null) {
                foreach($resep as $key => $value){
                    $data[] = [
                        'xtransaction_type' => $value->jenis_transaksi,
                        'xcompany_id' => 1,
                        'xinstalasi_id' => $value->instalasi_id,
                        'xruangan_id' => $value->ruangan_id,
                        'xref_number_id' => $value->id,
                        'xref_number' => $value->nomor,
                        'xvoucher_type' => DocoConstants::VOUCHER_TYPE,
                        'xcategori_code' => $value->jenisobatalkes_kode,
                        'xtransaction_at' => $value->tgl_transaksi,
                        'xamount' => $value->harga,
                        'xdiscount_amount' => $value->discount,
                        'xamount_netto' => $value->harga_netto,
                        'xamount_ppn' => ($value->jmlppn != null) ? $value->jmlppn : 0,
                        'xmedical_number' => $value->no_rekam_medik,
                        'xnotes' => $value->uraian,
                        'xis_billing' => $value->is_ditagihkan,
                    ];
                }
                $var = DocoAkunting::api('POST', 'integrations', $data);
            }else{
                return true;
                throw new \Exception("This application cannot be integrated to Akunting", 1);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    public function actiondoIntegrate($noresep)
    {
        return $this->integrate($noresep);
    }

    public function actionListAmpuls($obatalkes_id)
    {
        $items = SatuanKonversiView::getDb()->cache(function($db)use($obatalkes_id){
                $data = SatuanKonversiView::find()
                ->where(['is_active' => 't','jenis' => 'obat','obatalkes_id' => $obatalkes_id])
                ->orderBy('satuan_besar');
                return $data->asArray()->all();
            },60*5);

        return ['data'=>$items];
    }

    public function getKonfigFarmasi()
    {
        try {
            return KonfigFarmasi::find()
                ->select("konfigfarmasi_k.*, penjamin_m.carabayar_id")
                ->join('JOIN', 'penjamin_m', "konfigfarmasi_k.penjaminkaryawan_id = penjamin_m.penjamin_id")
                ->asArray()->one();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionGetPenjaminPerusahaan()
    {
        $konfig_farmasi = $this->getKonfigFarmasi();
        $penjamin = [];
        if (isset($konfig_farmasi['penjaminkaryawan_id'])) {
            $penjamin = PenjaminV::find()
            ->select("carabayar_id, carabayar_nama, penjamin_id, penjamin_nama")
            ->where(['penjamin_id' => $konfig_farmasi['penjaminkaryawan_id']])
            ->asArray()->one();

            $penjamin['penjamin_id'] = DocoHelpers::encrypt($penjamin['penjamin_id']);
            $penjamin['carabayar_id'] = DocoHelpers::encrypt($penjamin['carabayar_id']);

        }

        return $penjamin;
    }

    public function actionGetHargaObat()
    {
        $req = Yii::$app->request;
        $resep_id = $req->get('resep_id', null);
        $reseptur_id = $req->get('reseptur_id', null);
        
        if (!empty($resep_id)) {
            $data_resep = self::getInfoResepData($resep_id);
            $kelaspelayananId = isset($data_resep['kelaspelayanan_id']) ? $data_resep['kelaspelayanan_id'] : 0;
            $penjaminId = isset($data_resep['penjamin_id']) ? $data_resep['penjamin_id'] : 0;
        } else {
            $data_resep = self::getInfoResepturData($reseptur_id);
            $kelaspelayananId = isset($data_resep['kelaspelayanan_id']) ? $data_resep['kelaspelayanan_id'] : 0;
            $penjaminId = isset($data_resep['penjamin_id']) ? $data_resep['penjamin_id'] : 0;
        }

        $detail_resep = self::getDetailResepData(!empty($reseptur_id) ? $reseptur_id : $resep_id, !empty($reseptur_id));
        $obatalkes_ids = ArrayHelper::getColumn($detail_resep, 'obatalkes_id');
        $hargaObat = self::listInfoObatR($obatalkes_ids, $penjaminId, $kelaspelayananId);

        return $hargaObat;
    }

    // public function actionGetKonversi() {
    //     try {
    //         $request        = Yii::$app->request;
    //         $obatalkes_id   = $request->get('obatalkes_id', null);
    //         $satuanbesar_id = $request->get('satuanbesar_id', null);
    //         $data           = SatuanKonversiView::find()
    //             ->where([
    //                 'jenis'          => 'obat',
    //                 'obatalkes_id'   => $obatalkes_id,
    //                 'satuanbesar_id' => $satuanbesar_id
    //             ]);

    //         $items = $data->asArray()->one();

    //         return $items;
    //     } catch (\yii\db\Exception $e) {
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     } catch (\Exception $e) {
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    protected static function getInfoPendaftaranPasien($pendaftaranId, $admisiId = null)
    {
        $column = 'pendaftaran_id';
        if (!empty($admisiId)) {
            $column = 'pasienadmisi_id';
            $pendaftaranId = $admisiId;
        }

        return Pendaftaran::find()->select([
            'pendaftaran_id',
            'instalasi_id',
            'pasienadmisi_id',
        ])->where([$column => $pendaftaranId])->asArray()->one();
    }
}
