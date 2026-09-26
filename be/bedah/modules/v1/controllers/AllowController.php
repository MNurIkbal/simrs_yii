<?php

/**
 * @author Randy Vianda Putra
 * @todo Allow all Laboratorium
 * @copyright 09 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\ConfigTrait;
use Doco\components\DocoConstansId;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\KelompokPemeriksaanLab;
use app\modules\v1\models\JenisPemeriksaanLab;
use app\modules\v1\models\PemeriksaanLab;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\InfoRencanaOperasi;
use app\modules\v1\models\SampleLab;
use app\modules\v1\models\TarifPenunjangView;
use app\modules\v1\models\OperasiView;
use app\modules\v1\models\JenisAnastesi;
use app\modules\v1\models\InfoObatAlkesView;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\DaftarTindakanV;
use app\modules\v1\models\InfoTarifRsView;
use app\modules\v1\models\PaketBmhpView;
use app\modules\v1\models\TimOperasi;
use app\modules\v1\models\KegiatanOperasi;
use app\modules\v1\models\GolonganOperasi;
use app\modules\v1\models\KonfigSystem;
use Doco\models\TarifTotalFn;
use Doco\models\ObatAlkesFn;
use Doco\models\TindakanBmhpView;

use Doco\Traits\Select2Trait;

class AllowController extends DocoActiveController
{
    use ConfigTrait;
    use Select2Trait;

    public $modelClass = 'app\modules\v1\models\CaraBayar';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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

    public function getRuangan($instalasi_id = null)
    {
        $return = [];
        try {
            $model = Ruangan::find()->where(['is_deleted' => false, 'is_active' => true]);
            if ($instalasi_id) {
                $model->andWhere(['instalasi_id' => $instalasi_id]);
            }
            $return = $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            $return = [];
        }

        return $return;
    }

    public function getInstalasi($id = null)
    {
        $return = [];
        try {
            $model = Instalasi::find()->where(['is_deleted' => false, 'is_active' => true]);
            $return = $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            $return = [];
        }
        return $return;
    }

    public function actionGetRuanganInstalasi()
    {
        $ruangan = $this->getRuangan();
        $instalasi = $this->getInstalasi();

        return [
            'ruangan' => $ruangan,
            'instalasi' => $instalasi
        ];
    }

    public function actionGetRuangan($id)
    {
        $ruangan = $this->getRuangan($id);
        return ['ruangan' => $ruangan];
    }

    public function actionPackInfoPasienOperasi()
    {
        $data_statusoperasi = DocoHelpers::getLookUpByInstalasi('status_periksa_penunjang', 12);
        $ruanganinstalasi = $this->actionGetRuanganInstalasi();
        $result = ['data_statusoperasi' => $data_statusoperasi];
        $result = array_merge($result, $ruanganinstalasi);
        return $result;
    }

    public function getLookupByType($type = null)
    {
        $model = Lookup::find();
        if ($type) {
            $model->andWhere(['lookup_type' => $type]);
        }
        $model->orderBy(['lookup_urutan' => SORT_ASC]);
        return $model;
    }

    public function actionGetPegawai()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $find = PegawaiView::find();
        if (!empty($post['term'])) {
            $find->andWhere(['ILIKE', 'nama_pegawai', $post['term']]);
        }
        if (isset($post['ruangan_id'])) {
            $find->andWhere(['ruangan_id' => $post['ruangan_id']]);
        }
        return $find->asArray()->all();
    }

    public function actionGetNoRequest()
    {
        $request = Yii::$app->request;
        $model = InfoRencanaOperasi::find();

        if ($no_req = $request->get('q')) {
            $model->andWhere(['ILIKE', 'no_orderkeunitlain', $no_req]);
        }

        $filter_date = date('Y-m-d');
        if ($tanggal_operasi = $request->get('tanggal')) {
            $tanggal_operasi = date('Y-m-d', strtotime($tanggal_operasi));
            $filter_date = $tanggal_operasi;
        }

        $model->andWhere([
            'DATE(tgl_permintaan)' => $filter_date
        ]);

        return [
            'data' => $model->asArray()->limit(10)->all()
        ];
    }

    public function actionGetDokterOperator($q = null)
    {
        $request = Yii::$app->request;
        $jwt = Yii::$app->jwt->instalasi_id;
        $model = DokterView::find();

        if ($q) {
            $model->andWhere(['ILIKE', 'nama_pegawai', $q]);
        }

        if ($jwt) {
            $model->andWhere(['instalasi_id' => $jwt]);
        }

        return [
            'data' => $model->asArray()->all()
        ];
    }

    public function actionGetTimOperasi()
    {
        try {
            return Lookup::find()
                ->select([
                    'lookup_id as id',
                    'lookup_type',
                    'lookup_name as text',
                    'lookup_value as slug',
                    new \yii\db\Expression('CASE WHEN tindakanoperasi_mp.prosentase=null THEN 0 ELSE tindakanoperasi_mp.prosentase END AS prosentase'),
                ])
                ->join('LEFT JOIN', 'tindakanoperasi_mp', 'lookup_m.lookup_id=tindakanoperasi_mp.timoperasi_id')
                ->andWhere([
                    'lookup_type' => 'tim_operasi'
                ])
                ->orderBy([
                    'lookup_name' => SORT_ASC
                  ])
                ->groupBy([
                    'lookup_id',
                    'lookup_type',
                    'lookup_name',
                    'lookup_value',
                    'tindakanoperasi_mp.prosentase'
                ])
                ->asArray()
                ->all();
        } catch (Exception $e) {
            return [];
        }
    }
    public function actionGetTindakan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $find = TarifPenunjangView::find();
        if (isset($post['term'])) {
            $find->andWhere(['ILIKE', 'daftartindakan_nama', $post['term']]);
        }
        if (isset($post['ruangan_id'])) {
            $find->andWhere(['ruangan_id' => $post['ruangan_id']]);
        }
        if (isset($post['penjamin_id'])) {
            $find->andWhere(['penjamin_id' => $post['penjamin_id']]);
        }
        if (isset($post['kelaspelayanan_id'])) {
            $find->andWhere(['kelaspelayanan_id' => $post['kelaspelayanan_id']]);
        }
        $find->andWhere(['komponentarif_id' => 6]);
        $record = [];
        if (isset($post['typeReq']) && $post['typeReq'] == 'lab') {
            $find->andWhere(['jenis_tindakan' => 'IBS'])
                ->select([
                    'daftartindakan_id',
                    'daftartindakan_nama',
                    'ruangan_id',
                    'kelaspelayanan_id',
                    'penjamin_id',
                    'komponentarif_id',
                    'harga_tariftindakan',
                    'persencyto_tindakan',
                    'pemeriksaanlab_id',
                    'pemeriksaanlab_nama',
                    'kelompokpemeriksaanlab_id',
                    'nama_kelompok',
                    'daftartindakan_id',
                    'daftartindakan_nama',
                ]);
            $record = $find->asArray()->all();

            // commented because the condition klasifikasi and jenis only one to one to operation name
            // foreach ($result as $item) {
            //     if (isset($record['key-' . $item['pemeriksaanlab_id']])) {
            //         array_push($record['key-' . $item['pemeriksaanlab_id']]['jenis_operasi'], [
            //             'id' => $item['kelompokpemeriksaanlab_id'],
            //             'text' => $item['nama_kelompok'],
            //         ]);
            //         array_push($record['key-' . $item['pemeriksaanlab_id']]['klasifikasi_operasi'], [
            //             'id' => $item['daftartindakan_id'],
            //             'text' => $item['daftartindakan_nama'],
            //         ]);
            //     } else {
            //         $record['key-' . $item['pemeriksaanlab_id']] = [
            //             'ruangan_id' => $item['ruangan_id'],
            //             'kelaspelayanan_id' => $item['kelaspelayanan_id'],
            //             'penjamin_id' => $item['penjamin_id'],
            //             'komponentarif_id' => $item['komponentarif_id'],
            //             'harga_tariftindakan' => $item['harga_tariftindakan'],
            //             'persencyto_tindakan' => $item['persencyto_tindakan'],
            //             'pemeriksaanlab_id' => $item['pemeriksaanlab_id'],
            //             'pemeriksaanlab_nama' => $item['pemeriksaanlab_nama'],
            //             'jenis_operasi' => [
            //                 [
            //                     'id' => $item['kelompokpemeriksaanlab_id'],
            //                     'text' => $item['nama_kelompok'],
            //                 ]
            //             ],
            //             'klasifikasi_operasi' => [
            //                 [
            //                     'id' => $item['daftartindakan_id'],
            //                     'text' => $item['daftartindakan_nama'],
            //                 ]
            //             ]
            //         ];
            //     }
            // }
            // $record = array_values($record);
        } else {
            $find->select(['daftartindakan_id', 'daftartindakan_nama', 'ruangan_id', 'kelaspelayanan_id', 'penjamin_id', 'komponentarif_id', 'harga_tariftindakan', 'persencyto_tindakan']);
            $find->groupBy(['daftartindakan_id', 'daftartindakan_nama', 'ruangan_id', 'kelaspelayanan_id', 'penjamin_id', 'komponentarif_id', 'harga_tariftindakan', 'persencyto_tindakan']);
            $record = $find->asArray()->all();
        }
        return $record;
    }
    public function actionListJenisOperasi()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $model = OperasiView::find();
        $model->select(['golonganoperasi_id', 'golonganoperasi_nama', 'daftartindakan_id', 'operasi_id']);
        if (isset($get['daftartindakan_id'])) {
            $model->andWhere(['daftartindakan_id' => $get['daftartindakan_id']]);
        }
        return $model->asArray()->all();
    }
    public function actionGetPackItemOperasi()
    {
        $luka = $this->getLookupByType('jenis_luka')->asArray()->all();
        $jenisAnastesi = $this->getJenisAnastesi();
        $result = ['jenisluka' => $luka, 'jenisanastesi' => $jenisAnastesi];
        return $result;
    }
    public function getJenisAnastesi()
    {
        try {
            $model = JenisAnastesi::find()->where(['is_deleted' => 'false', 'is_active' => 'true']);
            return $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetJenisAlat()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = InfoObatAlkesView::find();
            // $model->andWhere(['ILIKE', 'jenisobatalkes_nama', 'alkes']);
            if (isset($post['obatalkes_nama'])) {
                $model->andWhere(['ILIKE', 'obatalkes_nama', $post['obatalkes_nama']]);
            }
            return $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetDaftarTindakan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = DaftarTindakanV::find();
            if (isset($post['daftartindakan_nama'])) {
                $model->andWhere(['ILIKE', 'daftartindakan_nama', $post['daftartindakan_nama']]);
            }
            $model->limit(50);
            return $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetTindakanTarif()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $find = InfoTarifRsView::find();
            if (isset($post['term'])) {
                $find->andWhere(['ILIKE', 'daftartindakan_nama', $post['term']]);
            }
            if (isset($post['ruangan_id'])) {
                $find->andWhere(['ruangan_id' => $post['ruangan_id']]);
            }
            if (isset($post['penjamin_id'])) {
                $find->andWhere(['penjamin_id' => $post['penjamin_id']]);
            }
            if (isset($post['kelaspelayanan_id'])) {
                $find->andWhere(['kelaspelayanan_id' => $post['kelaspelayanan_id']]);
            }
            $find->andWhere(['komponentarif_id' => 6]);
            $find->limit(50);
            return $find->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetDokter()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = DokterView::find();
        $model->select(['pegawai_id', 'nama_pegawai']);
        if (isset($post['nama_pegawai'])) {
            $model->andWhere(['ILIKE', 'nama_pegawai', $post['nama_pegawai']]);
        }
        $model->groupBy(['pegawai_id', 'nama_pegawai']);
        return $model->asArray()->all();
    }
    public function getMultiLookup($listRequest)
    {
        $data = [];
        foreach ($listRequest as $key => $value) {
            $data[$value] = $this->getLookupByType($value)->asArray()->all();
        }
        return $data;
    }
    public function actionPackIntra()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $get = $request->get();
        try {
            $lookup = $this->getMultiLookup($post);
            $data = Yii::$app->runAction('v1/intra-operasi/view', ['id' => $get['id']]);
            $infopasien = Yii::$app->runAction('v1/inf-pasien-operasi/view', ['id' => $get['id']]);
            $timoperasi = TimOperasi::find()
                ->select([
                    "timoperasi_t.pasienmasukpenunjang_id",
                    "timoperasi_t.inpostoperasi_id",
                    "timoperasi_t.posisi_tim",
                    "timoperasi_t.daftartindakan_id",
                    "timoperasi_t.golonganoperasi_id",
                    "timoperasi_t.persentase as prosentase",
                    "lookup_name as posisi_tim_nama",
                    "lookup_value as slug_posisi",
                    "timoperasi_t.pegawai_id",
                    "pegawai_m.nama_pegawai as pegawai_nama",
					"pegawailogin.nama_pegawai as pegawai_input",
                    "operasi_m.kegiatanoperasi_id",
                    "kegiatanoperasi_m.kegiatanoperasi_nama"
                    
                ])
                ->where(['pasienmasukpenunjang_id' => $get['id']])
                ->leftJoin('inpostoperasidetail_t', 'inpostoperasidetail_t.inpostoperasi_id = timoperasi_t.inpostoperasi_id AND inpostoperasidetail_t.daftartindakan_id = timoperasi_t.daftartindakan_id')
                ->leftJoin('operasi_m', 'operasi_m.operasi_id = inpostoperasidetail_t.operasi_id')
                ->leftJoin('kegiatanoperasi_m', 'kegiatanoperasi_m.kegiatanoperasi_id = operasi_m.kegiatanoperasi_id')
                ->rightJoin('pegawai_m', 'pegawai_m.pegawai_id = timoperasi_t.pegawai_id')
                ->rightJoin('loginpemakai_k', 'timoperasi_t.created_by = loginpemakai_k.loginpemakai_id')
                ->rightJoin('pegawai_m pegawailogin', 'loginpemakai_k.pegawai_id = pegawailogin.pegawai_id')
                ->join('JOIN', 'lookup_m', 'lookup_m.lookup_id=timoperasi_t.posisi_tim');

            $timoperasi->asArray()->all();
            $timoperasi = $timoperasi->asArray()->all();
            $tmpData = [];
            foreach($timoperasi as $row){
                if(!empty($row['daftartindakan_id'])){
                    $tmpData[$row['daftartindakan_id']][]= $row;
                }
            }
            
            $timoperasi = $tmpData;
            return [
                'lookup' => $lookup,
                'data' => $data['response']['data'],
                'infopasien' => $infopasien['response'],
                'timoperasi' => $timoperasi
            ];
        } catch (\yii\db\Exception $e) {
            return ['lookup' => [], 'data' => []];
        }
    }
    public function actionPackPost()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $get = $request->get();
        $instalasi = DocoConstants::INST_ID_RI;
        try {
            $lookup = $this->getMultiLookup($post);
            $data = Yii::$app->runAction('v1/intra-operasi/view', ['id' => $get['id']]);
            $ruangan = $this->getRuangan($instalasi);
            return ['lookup' => $lookup, 'data' => $data['response']['data'], 'ruangan' => $ruangan];
        } catch (\yii\db\Exception $e) {
            return ['lookup' => [], 'data' => []];
        }
    }
    public function actionGetBmhp()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = InfoStokObatAlkesView::find();
            if (isset($post['obatalkes_nama'])) {
                $model->andWhere(['ILIKE', 'obatalkes_nama', $post['obatalkes_nama']]);
            }
            if (isset($post['ruangan_id'])) {
                $model->andWhere(['ruangan_id' => $post['ruangan_id']]);
            }
            return $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionGetPaketBmhp()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = PaketBmhpView::find();
            if (isset($post['daftartindakan_id'])) {
                $model->andWhere(['daftartindakan_id' => $post['daftartindakan_id']]);
            }
            return $model->asArray()->all();
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }
    public function actionCompareBmhpRuangan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model = TindakanBmhpView::find();
            if (isset($post['daftartindakan_id'])) {
                $model->andWhere(['daftartindakan_id' => $post['daftartindakan_id']]);
            }
            $obatalkesid = [];
            foreach ($model->asArray()->all() as $key => $value) {
                $obatalkesid[] = $value['obatalkes_id'];
            }
            $obatalkesRuangan = InfoStokObatAlkesView::find();
            if (count($obatalkesid)) {
                $obatalkesRuangan->andWhere(['obatalkes_id' => $obatalkesid]);
            }
            if (isset($post['ruangan_id'])) {
                $obatalkesRuangan->andWhere(['ruangan_id' => $post['ruangan_id']]);
            }
            $newObatRuangan = [];
            foreach ($obatalkesRuangan->asArray()->all() as $key => $value) {
                $newObatRuangan[$value['obatalkes_id']] = $value;
            }
            $result = [];
            foreach ($model->asArray()->all() as $key => $value) {
                $is_available = 1;
                $msg = '';
                $persediaan = 0;
                $obatalkes_namalain = '';
                $qty_tersedia = 0;
                $qty_input = !empty($value['qty_input']) ? $value['qty_input'] : 0;
                if (!isset($newObatRuangan[$value['obatalkes_id']])) {
                    $is_available = 0;
                    $msg = 'Obat tidak tersedia';
                }
                if ($is_available) {
                    if ($qty_input > $newObatRuangan[$value['obatalkes_id']]['qty_tersedia']) {
                        $msg = 'Stok obat tidak mencukupi';
                        $is_available = 0;
                    }
                    $qty_tersedia = !empty($newObatRuangan[$value['obatalkes_id']]['qty_tersedia']) ? $newObatRuangan[$value['obatalkes_id']]['qty_tersedia'] : 0;
                    $persediaan = $qty_tersedia - $qty_input;
                    $obatalkes_namalain = !empty($newObatRuangan[$value['obatalkes_id']]['obatalkes_namalain']) ? $newObatRuangan[$value['obatalkes_id']]['obatalkes_namalain'] : '';
                }
                $value['is_available'] = $is_available;
                $value['persediaan'] = $persediaan;
                $value['obatalkes_namalain'] = $obatalkes_namalain;
                $value['qty_tersedia'] = $qty_tersedia;
                $value['qty_terpakai'] = $qty_input;
                $value['msg'] = $msg;
                $result[] = $value;
            }
            return $result;
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    /**
     * Retrieve data tindakan operasi
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionTindakanOperasi()
    {
        $term = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $query = DaftarTindakanV::find()
            ->select([
                'daftartindakan_id as id',
                'daftartindakan_nama as text'
            ]);
            /* ->andWhere([
                'kelompoktindakan_id' => 6
            ]) */
        if (!empty($term)) {
            $query->andWhere([
                'ilike',
                'daftartindakan_nama',
                $term
            ]);
        }
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        return $query->limit($limit + 1)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    /**
     * Retrieve data tindakan
     * 
     * @param String $term
     * @param String $penjamin_id
     * @param String $kelaspelayanan_id
     * @param Integer $page
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionTindakan()
    {
      $term = Yii::$app->request->get('term', null);
      $penjamin_id = Yii::$app->request->get('penjamin_id', null);
      $kelaspelayanan_id = Yii::$app->request->get('kelaspelayanan_id', null);
      $kegiatanoperasi_id = Yii::$app->request->get('kegiatanoperasi_id', null); 
      $type = Yii::$app->request->get('type', 'penunjang');
      $ruangan_id = Yii::$app->request->get('ruangan_id', Yii::$app->jwt->ruangan_id); 
      
      $page = Yii::$app->request->get('page', 1);
      $result = [];
      if ($type == "penunjang"){
        $query = (new TarifTotalFn([
            'extParam' => [
                $ruangan_id,
                $penjamin_id,
                $kelaspelayanan_id,
                $type
            ]
            ]))
            ->find()
            ->innerJoin('operasi_m', 'tariftotalrs_fn.daftartindakan_id = operasi_m.daftartindakan_id')
            ->innerJoin('kegiatanoperasi_m', 'operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id')
            ->select([
                'DISTINCT ON (tariftotalrs_fn.kode) tariftotalrs_fn.kode',
                'tariftotalrs_fn.daftartindakan_id as id',
                'concat(tariftotalrs_fn.kode,\' - \',tariftotalrs_fn.daftartindakan_nama) AS text',
                'tariftotalrs_fn.daftartindakan_id',
                'tariftotalrs_fn.daftartindakan_nama',
                'tariftotalrs_fn.harga_tariftindakan',
                'tariftotalrs_fn.persencyto_tindakan',
                'tariftotalrs_fn.pemeriksaanlab_id as operasi_id',
                'tariftotalrs_fn.jenispemeriksaanlab_nama as operasi_nama',
                'tariftotalrs_fn.kelompokpemeriksaanlab_id as golonganoperasi_id',
                'tariftotalrs_fn.nama_kelompok as golonganoperasi_nama',
                'tariftotalrs_fn.persen_penyulit',
                'operasi_m.kegiatanoperasi_id',
                'kegiatanoperasi_m.kegiatanoperasi_nama'
            ]);
          
      }
      else if($type == "pelayanan"){
        $query = (new TarifTotalFn([
            'extParam' => [
                $ruangan_id,
                $penjamin_id,
                $kelaspelayanan_id,
                $type
            ]
            ]))
            ->find()
           ->select([
                'daftartindakan_id as id',
                'concat(kode,\' - \',daftartindakan_nama) AS text',
                'daftartindakan_id',
                'daftartindakan_nama',
                'harga_tariftindakan',
                'persencyto_tindakan',
                'pemeriksaanlab_id as operasi_id',
                'jenispemeriksaanlab_nama as operasi_nama',
                'kelompokpemeriksaanlab_id as golonganoperasi_id',
                'nama_kelompok as golonganoperasi_nama',
                'persen_penyulit',
            ]);
      }
      
      
      if (!empty($term)) {
        $query->andWhere([
            'ilike',
            'tariftotalrs_fn.daftartindakan_nama',
            $term
        ]);
      }
      if(!empty($kegiatanoperasi_id)) {
        $query->andWhere(['operasi_m.kegiatanoperasi_id' => $kegiatanoperasi_id]);
      }
      $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
      return $query->limit($limit + 1)
        ->offset(($page - 1) * $limit)
        ->asArray()
        ->all();
    }

    public function actionTindakanKegiatanOperasi()
    {
      $term = Yii::$app->request->get('term', null);
      $penjamin_id = Yii::$app->request->get('penjamin_id', null);
      $kelaspelayanan_id = Yii::$app->request->get('kelaspelayanan_id', null);
      $kegiatanoperasi_id = Yii::$app->request->get('kegiatanoperasi_id', null); 
      $golonganoperasi_id = Yii::$app->request->get('golonganoperasi_id', null); 
      $ruangan_id = Yii::$app->request->get('ruangan_id', Yii::$app->jwt->ruangan_id); 

      if (empty($golonganoperasi_id)) return [];

      $type = Yii::$app->request->get('type', 'penunjang');
      $page = Yii::$app->request->get('page', 1);
      $result = [];
      $query = (new TarifTotalFn([
        'extParam' => [
            $ruangan_id,
            $penjamin_id,
            $kelaspelayanan_id,
            $type
        ]
        ]))
        ->find()
        ->select([
            'DISTINCT ON (tariftotalrs_fn.kode) tariftotalrs_fn.kode',
            'tariftotalrs_fn.daftartindakan_id as id',
            'concat(tariftotalrs_fn.kode,\' - \',tariftotalrs_fn.daftartindakan_nama) AS text',
            'tariftotalrs_fn.daftartindakan_id',
            'tariftotalrs_fn.daftartindakan_nama',
            'tariftotalrs_fn.harga_tariftindakan',
            'tariftotalrs_fn.persencyto_tindakan',
            'tariftotalrs_fn.kelompokpemeriksaanlab_id as golonganoperasi_id',
            'tariftotalrs_fn.nama_kelompok as golonganoperasi_nama',
            'tariftotalrs_fn.persen_penyulit'
        ]);
      
      if (!empty($term)) {
        $query->andWhere([
            'ilike',
            'tariftotalrs_fn.daftartindakan_nama',
            $term
        ]);
      }

      if (!empty($golonganoperasi_id)) {
        $query->andWhere(['tariftotalrs_fn.kelompokpemeriksaanlab_id' => $golonganoperasi_id]);
      }

      $query->groupBy([
            'tariftotalrs_fn.daftartindakan_id',
            'tariftotalrs_fn.kode',
            'tariftotalrs_fn.daftartindakan_nama',
            'tariftotalrs_fn.harga_tariftindakan',
            'tariftotalrs_fn.persencyto_tindakan',
            'tariftotalrs_fn.kelompokpemeriksaanlab_id',
            'tariftotalrs_fn.nama_kelompok',
            'tariftotalrs_fn.persen_penyulit'
      ]);

      $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
      return $query->limit($limit + 1)
        ->offset(($page - 1) * $limit)
        ->asArray()
        ->all();
    }

    /*
     * get alkes from function for infinity select
     * 
     * @param String var
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetInstrumen()
    {
        $request = Yii::$app->request;
        $q = $request->get('term', null);
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset =  ($page - 1) * $limit;
        $model = (new ObatAlkesFn([
                    'extParam' => [
                        1,
                        2
                    ]
                ]))
                ->find()
                ->select([
                    'obatalkes_id as id',
                    'obatalkes_nama as text',
                    'satuankecil_id',
                    'qty_stok'
                ])
                ->where(['ruangan_id' => Yii::$app->jwt->ruangan_id])
                ->andWhere(['group_jenisobat' => DocoConstants::GROUP_JENISOBAT_ALKES])
                ->limit($limit + 1)
                ->offset($offset);
        if( !is_null($q) || !empty($q) ){
            $model->andWhere(['ILIKE', 'obatalkes_nama', $q]);
        }
        return $model->asArray()->all();
    }

    public function actionGetRuanganPelengkap()
    {
        $instLab = $this->constans->actionGetId('LAB');
        $listLab = Instalasi::find()
            ->selectAttr()
            ->findByInstalasi($instLab)
            ->getDataArray();

        $listRuangan = Ruangan::find()
            ->selectAttr()
            ->findByInstalasi($instLab)
            ->getDataArray();

        return [
            'instalasi' => $listLab,
            'ruangan' => $listRuangan,
            'default_instalasi' => $instLab
        ];
    }

    /**
     * Retrieve data tindakan
     * 
     * @param String $term
     * @param String $penjamin_id
     * @param String $kelaspelayanan_id
     * @param Integer $page
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionKegiatan()
    {
        $term = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $golonganoperasi_id = Yii::$app->request->get('golonganoperasi_id', 1);
        $daftartindakan_id = Yii::$app->request->get('daftartindakan_id', 1);
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        $result = [];
        if (!empty($golonganoperasi_id) && !empty($daftartindakan_id)) {
            $query = OperasiView::find()->select([
                'kegiatanoperasi_id as id',
                'kegiatanoperasi_nama as text',
                'operasi_id',
            ]);

            if (!empty($term)) {
                $query->andWhere([
                    'ilike',
                    'kegiatanoperasi_nama',
                    $term
                ]);
            }

            $query->andWhere([
                'golonganoperasi_id' => $golonganoperasi_id,
                'daftartindakan_id' => $daftartindakan_id,
            ])->groupBy([
                'kegiatanoperasi_id',
                'kegiatanoperasi_nama',
                'operasi_id',
            ]);

            $result = $query->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }
        return $result;
    }

    public function actionGolongan()
    {
        $term = Yii::$app->request->get('term', null);
        $page = Yii::$app->request->get('page', 1);
        $query = GolonganOperasi::find()->selectAttr();
        if (!empty($term)) {
            $query->findByName($term);
        }
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        return $query->limit($limit + 1)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    public function actionGetTindakanOperasi()
    {
        $request = Yii::$app->request;
        $daftarTindakanId = $request->get('daftartindakan_id', null);
        $result = [];
        if(!empty($daftarTindakanId)) {
            $result = OperasiView::find()
            ->select(['kegiatanoperasi_id', 'kegiatanoperasi_nama', 'golonganoperasi_id', 'golonganoperasi_nama'])
            ->where(['daftartindakan_id' => $daftarTindakanId])
            ->asArray()->one();
        }
    }
    
    public function actionCaraBayar()
    {
        try {
            return $this->getCaraBayar()->asArray()->all();
        } catch (Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    private function getCaraBayar()
    {
        $result = Carabayar::find();
        $result->andWhere(['is_active' => TRUE]);
        
        return $result;
    }

    public function actionLookupPostOperativeAnestesi()
    {
        $aldreteScoreListTemp = [];
        $groups = Lookup::find()
            ->andWhere(['lookup_type' => 'aldretescore'])
            ->orderBy(['lookup_urutan' => SORT_ASC])
            ->asArray()
            ->all();

        $aldreteScoreArrived = Lookup::find()
            ->andWhere(['lookup_type' => 'aldretescore_arrived'])
            ->orderBy(['lookup_urutan' => SORT_ASC])
            ->asArray()
            ->all();

        $childTypes = [];
        foreach ($groups as $group) {
            $aldreteScoreListTemp['aldretescore_item' . $group['lookup_value']] = array_merge($group, ['items' => []]);
            $childTypes[] = 'aldretescore_item' . $group['lookup_value'];
        }
        if ($childTypes) {
            $items = Lookup::find()
                ->andWhere(['lookup_type' => $childTypes])
                ->orderBy(['lookup_urutan' => SORT_ASC])
                ->asArray()
                ->all();

            foreach ($items as $item) {
                $aldreteScoreListTemp[$item['lookup_type']]['items'][] = $item;
            }
        }
        $aldreteScoreList = array_values($aldreteScoreListTemp);
        return compact('aldreteScoreList', 'aldreteScoreArrived');
    }

    public function actionLookupIntraOperativeAnestesi()
    {
        $patientMonitoring = Lookup::find()
            ->andWhere(['lookup_type' => 'patient_monitor'])
            ->orderBy(['lookup_urutan' => SORT_ASC])
            ->asArray()
            ->all();
        return compact('patientMonitoring');
    }

    public function actionGetKonfigKegiatanGolonganOperasi()
    {
        return (new DocoConstansId)->actionGetId('show_kegiatan_golongan_operasi');
    }

    public function actionGetKonfigDefaultInputBmhp()
    {
        return KonfigSystem::find()->select([
            'default_bmhpbedah_ditagihkan'
        ])
        ->where(['konfigsystem_id' => DocoConstants::KONFIG_ID])->one();

    }
}
