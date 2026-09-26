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
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Barang;

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

    public function actionObat()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoRekomendasiObatView;
            $query = $model::find();
            $query->where(['status_po' => 316]);

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if (isset($advancedFilter['tgl_rekomendasiobat_awal']) && 
                    isset($advancedFilter['tgl_rekomendasiobat_akhir'])) {
                    $start = $advancedFilter['tgl_rekomendasiobat_awal'];
                    $end = $advancedFilter['tgl_rekomendasiobat_akhir'];
                }  
                if(isset($advancedFilter['nama_pegawai'])) {
                    $pegawai_id = $advancedFilter['nama_pegawai'];
                    $query->andWhere(['pegawai_id' => $pegawai_id]);
                    unset($_GET['advanced-filter']['nama_pegawai']);
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

    public function actionBarang()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoRekomendasiBarangView;
            $query = $model::find();
            $query->where(['status_po' => 316]);

            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if (isset($advancedFilter['tgl_rekomendasibarang_awal']) && 
                    isset($advancedFilter['tgl_rekomendasibarang_akhir'])) {
                    $start = $advancedFilter['tgl_rekomendasibarang_awal'];
                    $end = $advancedFilter['tgl_rekomendasibarang_akhir'];
                }  
                if(isset($advancedFilter['nama_pegawai'])) {
                    $pegawai_id = $advancedFilter['nama_pegawai'];
                    $query->andWhere(['pegawai_id' => $pegawai_id]);
                    unset($_GET['advanced-filter']['nama_pegawai']);
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

    public function actionSave($id)
    {
        $request = Yii::$app->request;
        $data = $request->post('data',[]);
        $ruangan_id = $request->post('ruangan_id');
        $pegawai_id = $request->post('pegawai_id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $dataObat = [];
            $rekomendasi = InfoRekomendasiObatDetailView::find()
                        ->where(['rekomendasiobat_id' => $id])
                        ->all();

            $datarekomendasi = [];
            foreach ($rekomendasi as $k => $v) {
                $datarekomendasi[$v['obatalkes_id']] = $v;
            }
            foreach ($data as $key => $value) {
                $model = new ValidasiPoObat;
                    // $model->tgl_validasi = date('Y-m-d H:i:s');
                    $model->ruangan_id = $ruangan_id;
                    $model->pegawai_id = $pegawai_id;
                    $model->supplier_id = $key;
                    $model->save();
                    $listObatAlkes = [];
                    foreach ($value as $k => $v) {
                        if (isset($datarekomendasi[$k])) {
                            $listObatAlkes[] = $k;
                            $dataObat[] = [
                                'rekomendasiobatdetail_id' => $datarekomendasi[$k]->rekomendasiobatdetail_id,
                                'validasipoobat_id' => $model->validasipoobat_id,
                                'obatalkes_id' => $k,
                                'nilai_ro' => $datarekomendasi[$k]->nilai_ro,
                                'ro_stok' => $datarekomendasi[$k]->ro_stok,
                                'rekomendasi' => $datarekomendasi[$k]->rekomendasi,
                                'qty_tersedia' => $datarekomendasi[$k]->qty_tersedia,
                                'qty_po' => $v,
                                'qty_input' => $v,
                            ];
                        }
                    }
                    ObatAlkes::updateAll([
                        'supplier_id' => $key
                    ],[
                        'obatalkes_id' => $listObatAlkes
                    ]);
            }

            $connection->createCommand("
                UPDATE obatalkes_m SET on_ro = 0
                FROM rekomendasiobatdetail_t
                WHERE obatalkes_m.obatalkes_id = rekomendasiobatdetail_t.obatalkes_id
                AND rekomendasiobatdetail_t.rekomendasiobat_id = {$id}
            ")->execute();

            if (!empty($dataObat)) {
                ValidasiPoObatDetail::batchInsert($dataObat);
            }
            $updateStatus = RekomendasiObat::findOne($id);
            $updateStatus->status_po = 317;
            $updateStatus->save();

            $transaction->commit();

            return true;
            
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

    public function actionSaveBarang($id)
    {
        $request = Yii::$app->request;
        $data = $request->post('data',[]);
        $ruangan_id = $request->post('ruangan_id');
        $pegawai_id = $request->post('pegawai_id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $dataObat = [];
            $rekomendasi = InfoRekomendasiBarangDetailView::find()
                        ->where(['rekomendasibarang_id' => $id])
                        ->all();

            $datarekomendasi = [];
            foreach ($rekomendasi as $k => $v) {
                $datarekomendasi[$v['barang_id']] = $v;
            }

            foreach ($data as $key => $value) {
                $model = new ValidasiPoBarang;
                // $model->tgl_validasi = date('Y-m-d H:i:s');
                $model->ruangan_id = $ruangan_id;
                $model->pegawai_id = $pegawai_id;
                $model->supplier_id = $key;
                $model->save();
                $listBarang = [];
                foreach ($value as $k => $v) {
                    if(isset($datarekomendasi[$k])) {
                        $listBarang[] = $k;
                        $dataBarang[] = [
                            'rekomendasibarangdetail_id' => $datarekomendasi[$k]->rekomendasibarangdetail_id,
                            'validasipobarang_id' => $model->validasipobarang_id,
                            'barang_id' => $k,
                            'nilai_ro' => $datarekomendasi[$k]->nilai_ro,
                            'ro_stok' => $datarekomendasi[$k]->ro_stok,
                            'rekomendasi' => $datarekomendasi[$k]->rekomendasi,
                            'qty_tersedia' => $datarekomendasi[$k]->qty_tersedia,
                            'qty_po' => $v,
                            'qty_input' => $v,
                        ];
                    }
                }
                Barang::updateAll([
                    'supplier_id' => $key
                ],[
                    'barang_id' => $listBarang
                ]);
            }

            $data = $connection->createCommand("
                UPDATE barang_m SET on_ro = 0
                FROM rekomendasibarangdetail_t
                WHERE barang_m.barang_id = rekomendasibarangdetail_t.barang_id
                AND rekomendasibarangdetail_t.rekomendasibarang_id = {$id}
            ")->execute();

            if (!empty($dataBarang)) {
                ValidasiPoBarangDetail::batchInsert($dataBarang);
            }

            $updateStatus = RekomendasiBarang::findOne($id);
            $updateStatus->status_po = 317;
            $updateStatus->save();

            $transaction->commit();

            return true;
            
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

    public function actionViewObat($id) 
    {
        $query = InfoRekomendasiObatView::find()
                ->where(['rekomendasiobat_id' => $id])
                ->all();

        return $query;
    }

    public function actionDetailObat($id) 
    {
        $query = InfoRekomendasiObatDetailView::find()
                ->where(['rekomendasiobat_id' => $id])
                ->all();

        return $query;
    }

    public function actionViewBarang($id) 
    {
        $query = InfoRekomendasiBarangView::find()
                ->where(['rekomendasibarang_id' => $id])
                ->all();

        return $query;
    }

    public function actionDetailBarang($id) 
    {
        $query = InfoRekomendasiBarangDetailView::find()
                ->where(['rekomendasibarang_id' => $id])
                ->all();

        return $query;
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
        $query = $model::find()->where(['status_po' => 316]);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $nama_pegawai = '';
        $no_rekomendasiobat = '';
        $ruangan_nama = '';
        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['tgl_rekomendasiobat'])) {
                $explode = explode(" - ", $advancedFilter['tgl_rekomendasiobat']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }

                $advancedFilter['tgl_rekomendasiobat_awal'] = $start;
                $advancedFilter['tgl_rekomendasiobat_akhir'] = $end;
            }
            if(isset($advancedFilter['nama_pegawai'])) {
                $pegawai_id = $advancedFilter['nama_pegawai'];
                $query->andWhere(['pegawai_id' => $pegawai_id]);
                $nama_pegawai = $advancedFilter['nama_pegawai'];
                unset($_GET['advanced-filter']['nama_pegawai']);
            }
            if(isset($advancedFilter['ruangan_nama'])) {
                $ruangan_id = $advancedFilter['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                $ruangan_nama = $advancedFilter['ruangan_nama'];
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
            if(isset($advancedFilter['no_rekomendasiobat'])) {
                $no_rekomendasiobat = $advancedFilter['no_rekomendasiobat'];
                $query->andWhere(['ILIKE', 'no_rekomendasiobat', $no_rekomendasiobat]);
            }
        }

        $query->andWhere(['between', 'tgl_rekomendasiobat', $start, $end]);
        $data = $query->asArray()->all();
        
        $ruangan_id = 33;
        $jabatan_id = 3;

        $pegawai = Yii::$app->db->createCommand("
            SELECT * FROM pegawai_v WHERE {$ruangan_id} = 33 AND {$jabatan_id} = 3
        ")->queryOne();

        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekomendasiobat#' => $no_rekomendasiobat,
            '#periode#' => ($start." - ".$end),
            '#nama_pegawai#' => $pegawai['nama_pegawai'],
            '#ruangan_nama#' => $ruangan_nama,
            '#title#' => $title,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $data,
            ]),
        ];
        $print->Output();
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
        $query = $model::find()->where(['status_po' => 316]);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $nama_pegawai = '';
        $no_rekomendasibarang = '';
        $ruangan_nama = '';

        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['tgl_rekomendasibarang'])) {
                $explode = explode(" - ", $advancedFilter['tgl_rekomendasibarang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }

                $advancedFilter['tgl_rekomendasibarang_awal'] = $start;
                $advancedFilter['tgl_rekomendasibarang_akhir'] = $end;
            }
            if(isset($advancedFilter['nama_pegawai'])) {
                $pegawai_id = $advancedFilter['nama_pegawai'];
                $query->andWhere(['pegawai_id' => $pegawai_id]);
                $nama_pegawai = $advancedFilter['nama_pegawai'];
                unset($_GET['advanced-filter']['nama_pegawai']);
            }
            if(isset($advancedFilter['ruangan_nama'])) {
                $ruangan_id = $advancedFilter['ruangan_nama'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                $ruangan_nama = $advancedFilter['ruangan_nama'];
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
            if(isset($advancedFilter['no_rekomendasibarang'])) {
                $no_rekomendasibarang = $advancedFilter['no_rekomendasibarang'];
                $query->andWhere(['ILIKE', 'no_rekomendasibarang', $no_rekomendasibarang]);
            }
        }

        $query->andWhere(['between', 'tgl_rekomendasibarang', $start, $end]);
        $data = $query->asArray()->all();

        $ruangan_id = 33;
        $jabatan_id = 3;

        $pegawai = Yii::$app->db->createCommand("
        SELECT * FROM pegawai_v WHERE {$ruangan_id} = 33 AND {$jabatan_id} = 3
        ")->queryOne();
        $print = new DocoPrint();
        $print->attributes = [
            '#no_rekomendasibarang#' => $no_rekomendasibarang,
            '#periode#' => ($start." - ".$end),
            '#nama_pegawai#' => $pegawai['nama_pegawai'],
            '#ruangan_nama#' => $ruangan_nama,
            '#title#' => $title,
            '#datatable#' => $this->renderPartial('_cetak_barang', [
                'data' => $data,
            ]),
        ];
        $print->Output();
    }

    public function actionBatal($id)
    {
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $data = RekomendasiObat::findOne($id);
        $connection->createCommand("
            UPDATE obatalkes_m SET on_ro = 0
            FROM rekomendasiobatdetail_t
            WHERE obatalkes_m.obatalkes_id = rekomendasiobatdetail_t.obatalkes_id
            AND rekomendasiobatdetail_t.rekomendasiobat_id = {$id}
        ")->execute();
        if($data) {
            $data->catatan = $request->post('catatan');
            $data->status_po = 322;
            $data->save(false);

            return true;
        }
    }

    public function actionBatalBarang($id)
    {
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $data = RekomendasiBarang::findOne($id);
        $connection->createCommand("
            UPDATE barang_m SET on_ro = 0
            FROM rekomendasibarangdetail_t
            WHERE barang_m.barang_id = rekomendasibarangdetail_t.barang_id
            AND rekomendasibarangdetail_t.rekomendasibarang_id = {$id}
        ")->execute();
        if($data) {
            $data->catatan = $request->post('catatan');
            $data->status_po = 322;
            $data->save(false);

            return true;
        }
    }

    public function actionExportExcel()
    {
        $title = 'List Recomended Order';
        $request = Yii::$app->request;
        $model = new InfoRekomendasiObatView;
        $query = $model::find(true)->where(['status_po' => 316]);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $no_rekomendasiobat = '';
        $nama_pegawai = '';
        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['tgl_rekomendasiobat'])) {
                $explode = explode(" - ", $advancedFilter['tgl_rekomendasiobat']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d H:i:s', strtotime($explode[0]));
                    $end = date('Y-m-d H:i:s', strtotime($explode[1]));
                }

                $advancedFilter['tgl_rekomendasiobat_awal'] = $start;
                $advancedFilter['tgl_rekomendasiobat_akhir'] = $end;
            }

            if(isset($advancedFilter['nama_pegawai'])) {
                $pegawai_id = $advancedFilter['nama_pegawai'];
                $pegawai = Pegawai::findOne($pegawai_id);
                $nama_pegawai = $pegawai->nama_pegawai;
                $query->andWhere(['pegawai_id' => $pegawai_id]);
            }
            if(isset($advancedFilter['no_rekomendasiobat'])) {
                $no_rekomendasiobat = $advancedFilter['no_rekomendasiobat'];
                $query->andWhere(['LIKE', 'no_rekomendasiobat', $no_rekomendasiobat]);
            }
        }

        // return $start;exit;
        $query->andWhere(['between', 'tgl_rekomendasiobat', $start, $end]);
        $data = $query->asArray()->all();
        $result = [];
        foreach ($data as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Rekomendasi')] = date('d M Y', strtotime($value['tgl_rekomendasiobat']));
            $newValue[\Yii::t('app', 'Nomor Rekomendasi')] = $value['no_rekomendasiobat'];
            $newValue[\Yii::t('app', 'Nama Pegawai')] = $value['nama_pegawai'];
            $result[$key] = $newValue;
        }

        $header = [];
        $header['Periode Recomended Order'] = (($start." - ".$end));
        if(!empty($no_rekomendasiobat)) {
            $header['Nomor Rekomendasi'] = $no_rekomendasiobat;
        }
        if(!empty($nama_pegawai)) {
            $header['Nama Pegawai'] = $nama_pegawai;
        }
        $filePath = DocoHelpers::exportExcel($title, $result, $header, array("uploadPath" => "./uploads"),[],[],true);

        $filePath->save('php://output');
        die;
    }

    public function actionExportExcelBarang()
    {
        $title = 'List Recomended Order Barang';
        $request = Yii::$app->request;
        $model = new InfoRekomendasiBarangView;
        $query = $model::find(true)->where(['status_po' => 316]);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $no_rekomendasibarang = '';
        $nama_pegawai = '';
        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            if(isset($advancedFilter['tgl_rekomendasibarang'])) {
                $explode = explode(" - ", $advancedFilter['tgl_rekomendasibarang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d H:i:s', strtotime($explode[0]));
                    $end = date('Y-m-d H:i:s', strtotime($explode[1]));
                }

                $advancedFilter['tgl_rekomendasibarang_awal'] = $start;
                $advancedFilter['tgl_rekomendasibarang_akhir'] = $end;
                unset($_GET['advanced-filter']['tgl_rekomendasibarang']);
            }
            if(isset($advancedFilter['nama_pegawai'])) {
                $pegawai_id = $advancedFilter['nama_pegawai'];
                $pegawai = Pegawai::findOne($pegawai_id);
                $nama_pegawai = $pegawai->nama_pegawai;
                $query->andWhere(['pegawai_id' => $pegawai_id]);
                unset($_GET['advanced-filter']['nama_pegawai']);
            }
            if(isset($advancedFilter['no_rekomendasibarang'])) {
                $no_rekomendasibarang = $advancedFilter['no_rekomendasibarang'];
                $query->andWhere(['LIKE', 'no_rekomendasibarang', $no_rekomendasibarang]);
            }
        }

        $query->andWhere(['between', 'tgl_rekomendasibarang', $start, $end]);
        $data = $query->asArray()->all();
        $result = [];
        foreach ($data as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Rekomendasi')] = date('d M Y', strtotime($value['tgl_rekomendasibarang']));
            $newValue[\Yii::t('app', 'Nomor Rekomendasi')] = $value['no_rekomendasibarang'];
            $newValue[\Yii::t('app', 'Nama Pegawai')] = $value['nama_pegawai'];
            $result[$key] = $newValue;
        }

        $header = [];
        $header['Periode Recomended Order'] = (($start." - ".$end));
        if(!empty($no_rekomendasibarang)) {
            $header['Nomor Rekomendasi'] = $no_rekomendasibarang;
        }
        if(!empty($nama_pegawai)) {
            $header['Nama Pegawai'] = $nama_pegawai;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
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
}