<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\RekomendasiObat;
use app\modules\v1\models\RekomendasiBarang;
use app\modules\v1\models\RekomendasiBarangDetail;
use app\modules\v1\models\RekomendasiObatDetail;
use app\modules\v1\models\RekomendasiOrderObatView;
use app\modules\v1\models\RekomendasiOrderBarangView;

class RecomendedOrderController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\RekomendasiObat';
    
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionObat()
    {
        try {
            $request = Yii::$app->request;
            $model = new RekomendasiOrderObatView;
            $query = $model::find();
            
            if($request->get('status_generate') == false) {
                $query->where('1=0');
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionBarang()
    {
        try {
            $request = Yii::$app->request;
            $model = new RekomendasiOrderBarangView;
            $query = $model::find();
            
            if($request->get('status_generate') == false) {
                $query->where('1=0');
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSave()
    {
        $data = new RekomendasiOrderObatView;
        $query = $data::find()->all();
        $request = Yii::$app->request;
        if(!empty($query)) {
            $model = new RekomendasiObat;
            $model->tgl_rekomendasiobat = date('Y-m-d H:i:s');
            $model->ruangan_id = Yii::$app->jwt->ruangan_id;
            $model->pegawai_id = Yii::$app->jwt->user->pegawai_id;
            $arrInsert = [];
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                if($model->validate() && $model->save()) {
                    $idParent = $model->rekomendasiobat_id;
                    foreach ($query as $key => $value) {
                        $arrInsert[] = [
                            'rekomendasiobat_id' => $idParent,
                            'obatalkes_id' => $value['obatalkes_id'],
                            'nilai_ro' => $value['nilai_ro'],
                            'qty_tersedia' => $value['sisa_stok'],
                            'ro_stok' => $value['ro_stok'],
                            'min_order' => $value['min_order'],
                            'max_order' => $value['max_order'],
                            'rekomendasi' => $value['rekomendasi'],
                        ];
                    }

                    RekomendasiObatDetail::batchInsert($arrInsert);
                    $transaction->commit();
                    $rekomendasiobat = RekomendasiObat::find()
                        ->select(['MAX(rekomendasiobat_id)'])
                        ->scalar();

                    $rekomendasiobat = RekomendasiObat::find()
                        ->select(['no_rekomendasiobat'])
                        ->where(['rekomendasiobat_id' => $rekomendasiobat])
                        ->one();

                    $response = [
                        'text' => 'Berhasil Membuat RO Obat',
                        'title' => 'Proses berhasil !',
                        'no_rekomendasiobat' => $rekomendasiobat->no_rekomendasiobat
                    ];
                    
                    return $response;
                }
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            } catch (\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            }
        }
    }

    public function actionSaveBarang()
    {
        $data = new RekomendasiOrderBarangView;
        $query = $data::find()->all();
        $request = Yii::$app->request;
        if(!empty($query)) {
            $model = new RekomendasiBarang;
            $model->tgl_rekomendasibarang = date('Y-m-d H:i:s');
            $model->ruangan_id = Yii::$app->jwt->ruangan_id;
            $model->pegawai_id = Yii::$app->jwt->user->pegawai_id;
            $arrInsert = [];
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                if($model->validate() && $model->save()) {
                    $idParent = $model->rekomendasibarang_id;
                    foreach ($query as $key => $value) {
                        $arrInsert[] = [
                            'rekomendasibarang_id' => $idParent,
                            'barang_id' => $value['barang_id'],
                            'nilai_ro' => $value['nilai_ro'],
                            'qty_tersedia' => $value['sisa_stok'],
                            'ro_stok' => $value['ro_stok'],
                            'min_order' => $value['min_order'],
                            'max_order' => $value['max_order'],
                            'rekomendasi' => $value['rekomendasi'],
                        ];
                    }

                    RekomendasiBarangDetail::batchInsert($arrInsert);
                    $transaction->commit();
                    $rekomendasibarang = RekomendasiBarang::find()
                        ->select(['MAX(rekomendasibarang_id)'])
                        ->scalar();

                    $rekomendasibarang = RekomendasiBarang::find()
                        ->select(['no_rekomendasibarang'])
                        ->where(['rekomendasibarang_id' => $rekomendasibarang])
                        ->one();

                    $response = [
                        // 'text' => 'Berhasil Membuat RO Barang',
                        // 'title' => 'Proses berhasil !',
                        'no_rekomendasibarang' => $rekomendasibarang->no_rekomendasibarang
                    ];
                    
                    return $response;
                }
            } catch (\yii\db\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            } catch (\Exception $e) {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            }
        }
    }

    private function getDetail($rekomendasiobat_id)
    {
        $query = RekomendasiObatDetail::find()
                ->select(['obat.obatalkes_nama', 'rekomendasiobatdetail_t.*'])
                ->leftJoin('obatalkes_m obat', 'obat.obatalkes_id = rekomendasiobatdetail_t.obatalkes_id')
                ->where(['rekomendasiobat_id' => $rekomendasiobat_id]);

        return $query;
    }

    private function getDetailBarang($rekomendasibarang_id)
    {
        $query = RekomendasiBarangDetail::find()
                ->select(['barang.barang_nama', 'rekomendasibarangdetail_t.*'])
                ->leftJoin('barang_m barang', 'barang.barang_id = rekomendasibarangdetail_t.barang_id')
                ->where(['rekomendasibarang_id' => $rekomendasibarang_id]);

        return $query;
    }

     /**
    * @controller actionCetakObat
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tgl_rekomendasiobat# => tanggal RO
    * @attribute #no_rekomendasiobat# => nomor RO
    * @attribute #title# => title adjustment
    * @attribute #pegawai_id# => pegawai 
    */
    public function actionCetakObat()
    {
        $request = Yii::$app->request;
        $no_rekomendasiobat = $request->get('no_rekomendasiobat');
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::findOne($ruangan_id);
        $ruangan_nama = ($ruangan) ? $ruangan->ruangan_nama : '';
        $title = 'Recomended Order Obat <br/> '.$ruangan_nama;
        $model = new RekomendasiObat;
        $header = $model::find()->where(['no_rekomendasiobat' => $no_rekomendasiobat])->one();

        $pegawai_id = Pegawai::findOne($header->pegawai_id);
        $query = $this->getDetail($header->rekomendasiobat_id);
        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekomendasiobat#' => $no_rekomendasiobat,
            '#tgl_rekomendasiobat#' => date('d M Y', strtotime($header->tgl_rekomendasiobat)),
            '#title#' => $title,
            '#pegawai_id#' => ($pegawai_id) ? $pegawai_id->nama_pegawai : '',
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
            ]),
        ];

        $print->Output();
    }

    /**
    * @controller actionCetakBarang
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tgl_rekomendasibarang# => tanggal RO
    * @attribute #no_rekomendasibarang# => nomor RO
    * @attribute #title# => title adjustment
    * @attribute #pegawai_id# => pegawai 
    */
    public function actionCetakBarang()
    {
        $request = Yii::$app->request;
        $no_rekomendasibarang = $request->get('no_rekomendasibarang');
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::findOne($ruangan_id);
        $ruangan_nama = ($ruangan) ? $ruangan->ruangan_nama : '';
        $title = 'Recomended Order Barang <br/> '.$ruangan_nama;
        $model = new RekomendasiBarang;
        $header = $model::find()->where(['no_rekomendasibarang' => $no_rekomendasibarang])->one();

        $pegawai_id = Pegawai::findOne($header->pegawai_id);
        $query = $this->getDetailBarang($header->rekomendasibarang_id);
        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekomendasibarang#' => $no_rekomendasibarang,
            '#tgl_rekomendasibarang#' => date('d M Y', strtotime($header->tgl_rekomendasibarang)),
            '#title#' => $title,
            '#pegawai_id#' => ($pegawai_id) ? $pegawai_id->nama_pegawai : '',
            '#datatable#' => $this->renderPartial('_cetak_barang', [
                'data' => $query->asArray()->all(),
            ]),
        ];

        $print->Output();
    }
}