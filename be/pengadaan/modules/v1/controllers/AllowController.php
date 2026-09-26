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
use app\modules\v1\models\InfoStokBarang;
use Doco\components\DocoHelpers;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoAccessRule;
use app\modules\v1\models\LaporanPemakaianBarangView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\PemakaianBarang;
use app\modules\v1\models\Barang;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\Payterm;

class AllowController extends DocoActiveController
{
    use ConfigTrait;
    public $modelClass = '';


    public function actions()
    {
        $actions = [
            'detail-pr' => 'app\modules\v1\actions\Allow\DetailPRFillerAction',
            'get-data-obat' => 'app\modules\v1\actions\Allow\GetDataObatAction',
            'set-po-expired'    => 'app\modules\v1\actions\Allow\SetPoExpiredAction',
            'list-supplier' => 'app\modules\v1\actions\Allow\ListSupplierAction'
        ];
        return $actions;
    }

    public function behaviors(){
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['set-po-expired'],
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['set-po-expired'],
        ];

        return $behaviors;
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

    public function actionGetRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new Ruangan;
        $query = $model::find();

        if (isset($get['instalasi_id'])) {
            $query->where('instalasi_id = :instalasi_id', ['instalasi_id' => $get['instalasi_id']]);
        }

        if (isset($get['term'])) {
            $query->andWhere(['ILIKE','ruangan_nama',$get['term']]);
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
                $konvSatuan[$value['satuanbesar_id']][$value['satuankecil_id']] = $value['nilai_konversi'];
            }
            return $konvSatuan;
            Yii::$app->cache->set(DocoConstants::KONV_SATUAN,$konvSatuan,DocoConstants::DURATION);
            $cacheSatuan = $konvSatuan;
        }
        return $cacheSatuan;
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
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

        return new ActiveDataProvider([
            'query' => $query,
        ]);
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

    public function actionGetPegawai()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        if(!empty($get['term'])) {
            $result = PegawaiView::find()->select([
                'pegawai_id',
                'nama_pegawai'
            ]);
            $term = $get['term'];
            $result->andWhere(['ILIKE', 'LOWER(nama_pegawai)', strtolower($term)]);
            $result->andWhere(['ruangan_id' => $ruangan_id]);
            return $result->limit(10)->asArray()->all();
        }
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

    public function actionGetListSupplier()
    {
        return Supplier::find()->select(['supplier_id', 'supplier_nama'])->where(['is_active' => true])->all();
    }

    public function actionGetListPayterm()
    {
        return Payterm::find()->select(['payterm_id', 'payterm_nama'])->where(['is_active' => true])->all();
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
            // $result->andWhere(['ruangan_id' => $ruangan_id]);
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
                    $result->andWhere(['is_active' => true]);
                }
                else {
                    $result->where(['ILIKE', 'LOWER(obatalkes_nama)', $term]);
                    $result->andWhere(['is_active' => true]);
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

    public function actionGetKonfigFarmasi()
    {
        $request = Yii::$app->request->get();
        $model = KonfigFarmasi::find();

        if(isset($request['column'])) {
            $model->select([$request['column']]);
            return $model->asArray()->one();
        }

        return $model->asArray()->all();
    }
}
