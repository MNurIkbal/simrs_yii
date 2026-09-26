<?php

/**
 * @author yaya
 * @todo Trasaksi All Gudang
 * @copyright 22 March 2018

 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\ConfigTrait;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\SatuanKonversiBarang;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\InfoStokBarang;
use app\modules\v1\models\SubKelompokBarang;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanPemakaianBarangView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PenjaminView;
use app\modules\v1\models\PemakaianBarang;
use app\modules\v1\models\Barang;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\BarangView;

class AllowController extends DocoActiveController
{
    use ConfigTrait;
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-ruangan"] = ["GET"];
        return $verbs;
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        $actions['get-alert-harga'] = 'app\modules\v1\actions\Allow\GetAlertHargaAction';
        $actions['get-list-rak'] = 'app\modules\v1\actions\Allow\GetListRakAction';
        $actions['get-list-instalasi-ruangan'] = 'app\modules\v1\actions\Allow\GetListInstalasiRuanganAction';
        $actions['update-base-price'] = 'app\modules\v1\actions\Allow\UpdateBasePriceAction';
        return $actions;
    }

    public function actionGetInstalasi()
    {
        $model = new Instalasi;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetRuangan($state = true)
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $model = new Ruangan;
        $query = $model::find();
        if(isset($get['instalasi_id'])) {
            $query->where('instalasi_id = :instalasi_id', ['instalasi_id' => $get['instalasi_id']]);
            $query->andWhere(['is_active' => true, 'is_deleted'=>false]);
        }

        if(isset($get['id'])) {
            $query->where('instalasi_id = :instalasi_id', ['instalasi_id' => $get['id']]);
            $query->andWhere(['is_active' => true, 'is_deleted'=>false]);
        }
        if($state){
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
                'pagination' => false
            ]);
        }else{
            return ['data' => $query->asArray()->all()];
        }
    }

    // get ruangan sesuai dengan halaman yang diakses
    public function actionGetRuanganByRoom()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new Ruangan;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $query = $model::find();
        if (isset($get['instalasi_id'])) {
            $query->andWhere('instalasi_id = :instalasi_id', ['instalasi_id' => $get['instalasi_id']]);
        }
        if ($ruangan_id) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @author yaya
    * Set cache buat konversi satuan
    * @return array
    * @example data <id-satuan-besar> [
    * ..
    * <id-satuan-kecil> => <nilai-konversi>
    *]
    **/

    public function actionSetCacheKonvertSatuan()
    {
        $cacheSatuan = Yii::$app->cache->get(DocoConstants::KONV_SATUAN);
        if ($cacheSatuan === false) {
            $satuanKonv = SatuanKonversi::find()->all();
            $konvSatuan = [];
            foreach ($satuanKonv as $value) {
                $konvSatuan[$value['obatalkes_id']][$value['satuanbesar_id']] = $value['nilai_konversi'];
            }
            return $konvSatuan;
            Yii::$app->cache->set(DocoConstants::KONV_SATUAN,$konvSatuan,DocoConstants::DURATION);
            $cacheSatuan = $konvSatuan;
        }
        return $cacheSatuan;
    }

    public function actionSetCacheKonvertSatuanBarang()
    {
        $satuanKonv = SatuanKonversiView::find()->andWhere(['jenis'=>'barang'])->all();
        $konvSatuan = [];
        foreach ($satuanKonv as $value) {
            $konvSatuan[$value['obatalkes_id']][$value['satuanbesar_id']] = $value['nilai_konversi'];
        }
        return $konvSatuan;
}

    public function actionGetBarang()
    {
        $query = BarangView::find();
        $request = Yii::$app->request;
        $get = $request->get();
        $term = isset($get['term']) ? $get['term'] : null;
        $isActive = isset($get['is_active']) ? $get['is_active'] : null;

        if ($term) {
            $query->andFilterWhere(['ILIKE', 'barang_nama', $term]);
        }

        if (isset($isActive)) {
            $query->andWhere(['is_active' => filter_var($isActive, FILTER_VALIDATE_BOOLEAN)]);
        }
        $dataBarang = $query->asArray()->limit(10)->all();
        $keyBarang = [];
        foreach ($dataBarang as $value) {
            $keyBarang[] = $value['barang_id'];
        }

        $satuanKonversi = $this->getSatuanKonversi($keyBarang);

        $result['data'] = [];
        foreach ($dataBarang as $value) {
            $value['satuan'] = isset($satuanKonversi[$value['barang_id']])
                                    ? $satuanKonversi[$value['barang_id']] : [];
            $result['data'][] = $value;
        }

        return $result;
    }

    public function actionListStokBarang()
    {
        $model = new InfoStokBarang;
        $request = Yii::$app->request;
        $get = $request->get();
        $instalasi_id = isset($get['instalasi_id']) ? $get['instalasi_id'] : null;
        $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : null;

        $query = $model::find();

        if ($instalasi_id) {
            $query->andWhere(['instalasi_id' => $instalasi_id]);
        }

        if ($ruangan_id) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }

        $term = isset($get['term']) ? $get['term'] : null;
        if (isset($_GET['advanced-filter']['barang_nama'])) {
            $obat_alkes_nama = $request->get('advanced-filter')['barang_nama'];
            $query->andFilterWhere(['ILIKE', 'barang_nama', $obat_alkes_nama]);
        }

        if ($term) {
            $query->andFilterWhere(['ILIKE', 'barang_nama', $term]);
        }

        $dataBarang = $query->asArray()->all();
        $keyBarang = [];
        foreach ($dataBarang as $value) {
            $keyBarang[] = $value['barang_id'];
        }

        $satuanKonversi = $this->getSatuanKonversi($keyBarang);

        $result['data'] = [];
        foreach ($dataBarang as $value) {
            $value['satuan'] = isset($satuanKonversi[$value['barang_id']])
                                    ? $satuanKonversi[$value['barang_id']] : [];
            $result['data'][] = $value;
        }

        return $result;
    }

    public function getSatuanKonversi($id)
    {
        $result = [];
        $satuan = SatuanKonversiBarang::find()->select([
            'satuankonversibrg_m.satuankonversibrg_id',
            'satuankonversibrg_m.satuanbesar_id',
            'satuankonversibrg_m.satuankecil_id',
            'satuankonversibrg_m.nilai_konversi',
            'satuankonversibrg_m.barang_id',
            'satuanunit_m.satuanunit_nama'
        ])->joinWith(['satuan' => function ($query) {
            $query->select([
                'satuanunit_id'
            ]);
        }])->where([
            'satuankonversibrg_m.barang_id' => $id,
            'satuankonversibrg_m.is_active' => 1
        ])->asArray()->all();

        foreach ($satuan as $key => $value) :
            $result[$value['barang_id']][] = $value;
        endforeach;

        return $result;
    }

    public function actionGetNoPemakaian()
    {
        $model = new PemakaianBarang;
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : null;

        $query = $model::find();

        if ($ruangan_id) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }

        $term = isset($get['term']) ? $get['term'] : null;
        if ($term) {
            $query->andFilterWhere(['ILIKE', 'no_pemakaianbarang', $term]);
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetInstalasiActive()
    {
        return Instalasi::find()->where([
            'is_active' => true
        ])->all();
    }

    public function actionGetInstalasiActiveSelected()
    {
        return Instalasi::find()
            ->select([
                'instalasi_id',
                'instalasi_nama',
                'instalasi_namalainnya',
                'instalasi_singkatan',
                'instalasi_adakamar',
                'is_active',
                'is_sync',
            ])
            ->where([
            'is_active' => true
            ])->all();
    }

    // mengambil instalasi sesuai dengan ruangan yang sedang dibuka
    public function actionGetInstalasiRoom()
    {
        $instalasi_id = Yii::$app->jwt->instalasi_id;
        return Instalasi::find()
            ->select([
                'instalasi_id',
                'instalasi_nama',
                'instalasi_namalainnya',
                'instalasi_singkatan',
                'instalasi_adakamar',
                'is_active',
                'is_sync',
            ])
            ->where([
            'is_active' => true,
            'instalasi_id' => $instalasi_id
            ])->all();
    }

    public function actionListSubKelompok()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $parent_label = $get['parent_label'];
        $result = SubKelompokBarang::find()
            ->where(['is_active' => true, 'kelompokbarang_id' => $parent_label]);

        return $result->limit(10)->asArray()->all();
    }

    public function actionListKelompokBarang(){
        $data = $this->getKelompokBarang();
        return $data;
    }

    public function actionListSubKelompokBarang(){
        $data = $this->getSubKelompokBarang();
        return $data;
    }

    public function getKelompokBarang()
    {
        $data = KelompokBarang::find()->where(['is_active' => true])->all();

        return $data;
    }

    public function getSubKelompokBarang()
    {
        $data = SubKelompokBarang::find()->where(['is_active' => true])->all();

        return $data;
    }

    public function getBarang()
    {
        $model = Barang::find();
        return $model;
    }

    public function actionAutoBarang()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $result = $this->getBarang();
            $result->select(['barang_nama', 'barang_nama']);
            if (!empty($post['term'])) {
                $term = $post['term'];
                $result->where(['ILIKE', 'LOWER(barang_nama)', $term]);
            }
            return $result->asArray()->all();

        } catch (\yii \db \Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionAmbilBarang()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $result = $this->getBarang();
            $result->select(['barang_id', 'barang_nama']);
            if (!empty($post['term'])) {
                $term = $post['term'];
                $result->where(['ILIKE', 'LOWER(barang_nama)', $term]);
            }
            return $result->asArray()->all();

        } catch (\yii \db \Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetPegawai()
    {
        $request = Yii::$app->request;
        $ruangan = Yii::$app->jwt->ruangan_id;
        $get = $request->get();
        $result = PegawaiView::find()->select([
            'pegawai_id',
            'nama_pegawai',
            'nomorindukpegawai'
        ]);
        $result->groupBy([
            'pegawai_id',
            'nama_pegawai',
            'nomorindukpegawai'
        ]);
        $result->orderBy(['nama_pegawai'=>SORT_ASC]);
        if (!empty($ruangan)) {
            $result->where([
                'ruangan_id' => $ruangan
            ]);
        }
        if (isset($get['term'])) {
           $result->andFilterWhere(['OR',
               ['ILIKE', 'nama_pegawai', $get['term']],
               ['ILIKE', 'nomorindukpegawai', $get['term']]
           ]);
           return $result->limit(30)->asArray()->all();
        }
        return $result->limit(30)->asArray()->all();
    }

    public function actionListSupplier()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        if(!empty($get['term'])) {
            $result = Supplier::find()->select([
                'supplier_id',
                'supplier_nama'
            ]);
            $term = $get['term'];
            $result->andWhere(['ILIKE', 'LOWER(supplier_nama)', strtolower($term)]);
            $result->andWhere(['is_active' => true]);
            return $result->limit(10)->asArray()->all();
        }
    }

    public function actionGetListPegawai()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        if(!empty($get['term'])) {
            $result = Pegawai::find()->select([
                'pegawai_id',
                'nama_pegawai'
            ]);
            $term = $get['term'];
            $result->andWhere(['ILIKE', 'LOWER(nama_pegawai)', strtolower($term)]);
            return $result->limit(10)->asArray()->all();
        }
    }

    public function actionListItem()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $instalasi_id = $get['instalasi_id'];
            $result = ($instalasi_id == DocoConstants::INSTALASI_GUDANG_UMUM) ? Barang::find() : ObatAlkes::find();
            if (!empty($get['term'])) {
                $term = $get['term'];
                if($instalasi_id == DocoConstants::INSTALASI_GUDANG_UMUM) {
                    $result->where(['ILIKE', 'LOWER(barang_nama)', $term]);
                }
                else {
                    $result->where(['ILIKE', 'LOWER(obatalkes_nama)', $term]);
                }
            }
            return $result->asArray()->all();

        } catch (\yii \db \Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSearchCaraBayarPenjamin($term = NULL) {
        $request = Yii::$app->request;
        $page = $request->get('page', 1);
        $_GET['expand'] = $request->get('expand', 'carabayar_m');

        $model = new PenjaminView;
        $query = $model::find()
            ->where(['like', 'LOWER(penjamin_nama)', strtolower($term)])
            ->orWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)])
            ->andWhere('is_active=true');
        $query->offset(($page-1)*10)->limit(10);
        $query->orderBy(['carabayar_nama'=>SORT_ASC]);

        return ['data' => $query->all()];
    }
}
