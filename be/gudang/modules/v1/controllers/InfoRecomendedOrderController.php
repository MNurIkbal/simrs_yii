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
use app\modules\v1\models\InfoRekomendasiObatView;
use app\modules\v1\models\InfoRekomendasiBarangView;
use app\modules\v1\models\InfoRekomendasiObatDetailView;
use app\modules\v1\models\InfoRekomendasiBarangDetailView;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\ValidasiPoBarangDetail;
use app\modules\v1\models\RekomendasiObat;
use app\modules\v1\models\RekomendasiBarang;
use app\modules\v1\models\Lookup;

class InfoRecomendedOrderController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoRekomendasiObatView';
    
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

    public function actionGetAttributes()
    {
        $status = Lookup::find()->where([
            'lookup_type' => 'status_po'
        ])->all();
        return [
            'data_status' => $status
        ];
    }

    public function actionBarang()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoRekomendasiBarangView;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if (isset($advancedFilter['tgl_rekomendasibarang_awal']) && 
                    isset($advancedFilter['tgl_rekomendasibarang_akhir'])) {
                    $start = $advancedFilter['tgl_rekomendasibarang_awal'];
                    $end = $advancedFilter['tgl_rekomendasibarang_akhir'];
                }  
            }

            $query->andWhere(['between', 'tgl_rekomendasibarang', $start, $end]);
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

    public function actionObat()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoRekomendasiObatView;
            $query = $model::find();

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if (isset($advancedFilter['tgl_rekomendasiobat_awal']) && 
                    isset($advancedFilter['tgl_rekomendasiobat_akhir'])) {
                    $start = $advancedFilter['tgl_rekomendasiobat_awal'];
                    $end = $advancedFilter['tgl_rekomendasiobat_akhir'];
                }  
            }

            $query->andWhere(['between', 'tgl_rekomendasiobat', $start, $end]);
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

    /**
    * @controller actionCetakBarang
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode RO
    * @attribute #no_rekomendasibarang# => nomor RO
    * @attribute #title# => title adjustment
    * @attribute #nama_pegawai# => pegawai
    * @attribute #ruangan_nama# => ruangan 
    */
    public function actionCetakBarang()
    {
        $request = Yii::$app->request;
        $title = 'List Recomended Order Barang';
        $model = new InfoRekomendasiBarangView;
        $query = $model::find();
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $nama_pegawai = '';
        $no_rekomendasibarang = '';
        $ruangan_nama = '';

        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if (isset($advancedFilter['tgl_rekomendasibarang_awal']) && 
                isset($advancedFilter['tgl_rekomendasibarang_akhir'])) {
                $start = $advancedFilter['tgl_rekomendasibarang_awal'];
                $end = $advancedFilter['tgl_rekomendasibarang_akhir'];
            }  
        }

        $query->andWhere(['between', 'tgl_rekomendasibarang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $jabatan_id = 3;

        $pegawai = Yii::$app->db->createCommand("
            SELECT * FROM pegawai_v WHERE ruangan_id = {$ruangan_id} AND jabatan_id = {$jabatan_id}
        ")->queryOne();
        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekomendasibarang#' => $no_rekomendasibarang,
            '#periode#' => (date('d-M-Y',strtotime($start))." - ".date('d-M-Y',strtotime($end))),
            '#nama_pegawai#' => $pegawai['nama_pegawai'],
            '#ruangan_nama#' => "Gudang Umum",
            '#title#' => $title,
            '#datatable#' => $this->renderPartial('_cetak_barang', [
                'data' => $data,
            ]),
        ];
        $print->Output();
    }

    public function actionExportExcelBarang()
    {
        $title = 'List Recomended Order Barang Gudang Umum';
        $request = Yii::$app->request;
        $model = new InfoRekomendasiBarangView;
        $query = $model::find(true);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $no_rekomendasibarang = '';
        $nama_pegawai = '';
        $status = '';
        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if (isset($advancedFilter['tgl_rekomendasibarang_awal']) && 
                isset($advancedFilter['tgl_rekomendasibarang_akhir'])) {
                $start = $advancedFilter['tgl_rekomendasibarang_awal'];
                $end = $advancedFilter['tgl_rekomendasibarang_akhir'];
            }  
        }

        $query->andWhere(['between', 'tgl_rekomendasibarang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $result = [];
        foreach ($data as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Rekomendasi')] = date('d M Y', strtotime($value['tgl_rekomendasibarang']));
            $newValue[\Yii::t('app', 'Nomor Rekomendasi')] = $value['no_rekomendasibarang'];
            $newValue[\Yii::t('app', 'Status PO')] = $value['status'];
            $newValue[\Yii::t('app', 'Catatan')] = $value['catatan'];
            $result[$key] = $newValue;
        }

        $header = [];
        $header['Periode Recomended Order'] = ((date('d M Y', strtotime($start))." - ". date('d M Y', strtotime($end))));
        if(!empty($no_rekomendasibarang)) {
            $header['Nomor Rekomendasi'] = $no_rekomendasibarang;
        }
        if(!empty($status)) {
            $header['Status'] = $status;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }

    public function actionViewBarang($id) 
    {
        $query = InfoRekomendasiBarangView::find()
                ->where(['rekomendasibarang_id' => $id])
                ->all();

        return $query;
    }

    public function actionViewObat($id) 
    {
        $query = InfoRekomendasiObatView::find()
                ->where(['rekomendasiobat_id' => $id])
                ->all();

        return $query;
    }

    public function actionGetDetailBarang()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $model = new InfoRekomendasiBarangDetailView;
            $query = $model::find();
            $query->where(['rekomendasibarang_id' => $id]);

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

    public function actionGetDetailObat()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $model = new InfoRekomendasiObatDetailView;
            $query = $model::find();
            $query->where(['rekomendasiobat_id' => $id]);

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

    /**
    * @controller actionCetakDetailBarang
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tanggal_rekomendasi# => tanggal rekomendasi order
    * @attribute #no_rekomendasi# => nomor rekomendasi order
    * @attribute #nama_pegawai# => pegawai
    * @attribute #ruangan_nama# => ruangan 
    */
    public function actionCetakDetailBarang($id)
    {
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $query = InfoRekomendasiBarangView::find()
                ->where([
                    'rekomendasibarang_id' => $id
                ])->one();
        $detail = InfoRekomendasiBarangDetailView::find()
                ->where([
                    'rekomendasibarang_id' => $id
                ])->orderBy('barang_nama')->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekomendasi#' => isset($query['no_rekomendasibarang']) ? $query['no_rekomendasibarang'] : null,
            '#tanggal_rekomendasi#' => isset($query['tgl_rekomendasibarang']) 
                ? date('d M Y',strtotime($query['tgl_rekomendasibarang'])) : null,
            '#nama_pegawai#' => isset($query['nama_pegawai']) ? $query['nama_pegawai'] : null,
            '#ruangan_nama#' => isset($query['ruangan_nama']) ? $query['ruangan_nama'] : null,
            '#status_po#' => $query['status_po'] == 322 ? 'Transaksi Telah Dibatalkan/Ditolak' : null,
            '#catatan#' => $query['status_po'] == 322 ? $query['catatan'] : null,
            '#label_alasan#' => $query['status_po'] == 322 ? 'Alasan' : null,
            '#datatable#' => $this->renderPartial('_cetak_detail_barang', [
                'data' => $detail,
            ]),
        ];
        $print->Output();
    }

    /**
    * @controller actionCetakDetailObat
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tanggal_rekomendasi# => tanggal rekomendasi order
    * @attribute #no_rekomendasi# => nomor rekomendasi order
    * @attribute #nama_pegawai# => pegawai
    * @attribute #ruangan_nama# => ruangan 
    * @attribute #catatan# => catatan 
    */
    public function actionCetakDetailObat($id)
    {
        $pegawai_id = Yii::$app->jwt->user->pegawai_id;
        $query = InfoRekomendasiObatView::find()
                ->where([
                    'rekomendasiobat_id' => $id
                ])->one();
        $detail = InfoRekomendasiObatDetailView::find()
                ->where([
                    'rekomendasiobat_id' => $id
                ])->orderBy('obatalkes_nama')->all();

        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekomendasi#' => isset($query['no_rekomendasiobat']) ? $query['no_rekomendasiobat'] : null,
            '#tanggal_rekomendasi#' => isset($query['tgl_rekomendasiobat']) 
                ? date('d M Y',strtotime($query['tgl_rekomendasiobat'])) : null,
            '#nama_pegawai#' => isset($query['nama_pegawai']) ? $query['nama_pegawai'] : null,
            '#ruangan_nama#' => isset($query['ruangan_nama']) ? $query['ruangan_nama'] : null,
            '#status_po#' => $query['status_po'] == 322 ? 'Transaksi Telah Dibatalkan/Ditolak' : null,
            '#catatan#' => $query['status_po'] == 322 ? $query['catatan'] : null,
            '#label_alasan#' => $query['status_po'] == 322 ? 'Alasan' : null,
            '#datatable#' => $this->renderPartial('_cetak_detail_obat', [
                'data' => $detail,
            ]),
        ];
        $print->Output();
    }

     /**
    * @controller actionCetakObat
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode RO
    * @attribute #no_rekomendasiobat# => nomor RO
    * @attribute #title# => title adjustment
    * @attribute #nama_pegawai# => pegawai
    * @attribute #ruangan_nama# => ruangan 
    */
    public function actionCetakObat()
    {
        $request = Yii::$app->request;
        $title = 'List Recomended Order';
        $model = new InfoRekomendasiObatView;
        $query = $model::find();
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $nama_pegawai = '';
        $no_rekomendasiobat = '';
        $ruangan_nama = '';
        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if (isset($advancedFilter['tgl_rekomendasiobat_awal']) && 
                isset($advancedFilter['tgl_rekomendasiobat_akhir'])) {
                $start = $advancedFilter['tgl_rekomendasiobat_awal'];
                $end = $advancedFilter['tgl_rekomendasiobat_akhir'];
            }  
        }

        $query->andWhere(['between', 'tgl_rekomendasiobat', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $jabatan_id = 3;

        $pegawai = Yii::$app->db->createCommand("
            SELECT * FROM pegawai_v WHERE ruangan_id = {$ruangan_id} AND jabatan_id = {$jabatan_id}
        ")->queryOne();

        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekomendasiobat#' => $no_rekomendasiobat,
            '#periode#' => (date('d-M-Y',strtotime($start))." - ".date('d-M-Y',strtotime($end))),
            '#nama_pegawai#' => $pegawai['nama_pegawai'],
            '#ruangan_nama#' => "Gudang Farmasi",
            '#title#' => $title,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $data,
            ]),
        ];
        $print->Output();
    }

    public function actionExportExcel()
    {
        $title = 'List Recomended Order Gudang Farmasi';
        $request = Yii::$app->request;
        $model = new InfoRekomendasiObatView;
        $query = $model::find(true);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $no_rekomendasiobat = '';
        $nama_pegawai = '';
        if (isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if (isset($advancedFilter['tgl_rekomendasiobat_awal']) && 
                isset($advancedFilter['tgl_rekomendasiobat_akhir'])) {
                $start = $advancedFilter['tgl_rekomendasiobat_awal'];
                $end = $advancedFilter['tgl_rekomendasiobat_akhir'];
            }  
        }

        $query->andWhere(['between', 'tgl_rekomendasiobat', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $result = [];
        foreach ($data as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Rekomendasi')] = date('d M Y', strtotime($value['tgl_rekomendasiobat']));
            $newValue[\Yii::t('app', 'Nomor Rekomendasi')] = $value['no_rekomendasiobat'];
            $newValue[\Yii::t('app', 'Status PO')] = $value['status'];
            $newValue[\Yii::t('app', 'Catatan')] = $value['catatan'];
            $result[$key] = $newValue;
        }

        $header = [];
        $header['Periode Recomended Order'] = ((date('d M Y', strtotime($start))." - ". date('d M Y', strtotime($end))));
        if(!empty($no_rekomendasibarang)) {
            $header['Nomor Rekomendasi'] = $no_rekomendasibarang;
        }
        if(!empty($status)) {
            $header['Status'] = $status;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [],[],[],true);

        $filePath->save('php://output');
        die;
    }
}