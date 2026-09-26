<?php
    /**
    * @author iqbal@docotel.com
    * @since 2019-02-27 10:11:20 
    * @desc 
    */
namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

use app\modules\v1\models\ObatAlkesView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\ObatAlkesBasePrice;
use app\modules\v1\models\ObatHistoryR;
use app\modules\v1\models\ObatHistoryView;
use app\modules\v1\models\Ruangan;

class BasePriceController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ObatAlkesView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    private function Model(){
        $model = new ObatAlkesView;
        return $model::find();
    }


    public function actionIndex()
    {
        $cacheDuration = 60 * 5;

        try {
            $request = Yii::$app->request;
            $model = new ObatAlkesView;
            $dep = new \yii\caching\DbDependency();
            $dep->sql = "SELECT COUNT(*) FROM obatalkes_m";
            $query = ObatAlkesView::getDb()->cache(function($db) {
                return ObatAlkesView::find();
            }, $cacheDuration, $dep);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetObat($id)
    {
        $request = Yii::$app->request;
        if (empty($id)) {
            $id = $request->get('id');
        }
        try {
            $result = ObatAlkesView::find()->where(['obatalkes_id' => $id ])->one();
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    public function getPegawaiView()
    {
        $model = PegawaiView::find()->where(['is_active' => true]);
        return $model;
    }

    public function actionDataPegawai()
    {
        $request = Yii::$app->request;
        $pegawai_id = $request->get('pegawai_id');
        $ruangan_id = $request->get('ruangan_id');
        $ruangan_nama = $request->get('ruangan_nama');
        try {
            $query = $this->getPegawaiView();
            $query->where(['pegawai_id' => $pegawai_id]);
            $query->andWhere(['ruangan_id' => $ruangan_id]);
            
            if (!empty($ruangan_nama)) {
                $query->andWhere(['ILIKE', 'LOWER(ruangan_nama)', strtolower($ruangan_nama)]);
            }
            $result = $query->asArray()->one();
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    public function actionUbahBasePrice()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $obatalkes_id = $request->post('obatalkes_id');
            $getObatAlkes = ObatAlkesBasePrice::findOne($obatalkes_id);
            $getObatAlkes->scenario = 'baseprice';
            $getObatAlkes->attributes = $request->post();
            $getObatAlkes->harganetto = $request->post('harganetto');
            $getObatAlkes->ket_ubah_harga = $request->post('ket_ubah_harga');
            $getObatAlkes->catatan = $request->post('catatan');
            if($getObatAlkes->validate()){
                if ($getObatAlkes->update()) {
                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($getObatAlkes->errors, 'BasePrice');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }else{
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($getObatAlkes->errors,'BasePrice');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUploadBasePrice()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $data_obat = $request->post('data_obat');
            // $data_obat = json_decode($data_obat,true);
            // $data_obat = $data_obat['data_obat'];

            foreach($data_obat as $key => $value) {
                if ($value['status'] == true) {
                    $getObatAlkes = ObatAlkesBasePrice::findOne($value['obatalkes_id']);
                    $getObatAlkes->harganetto = DocoHelpers::convertToNumber($value['harganetto']);
                    $getObatAlkes->ket_ubah_harga = $value['ket_ubah_harga'];
                    $result = $getObatAlkes->update();
                }
            }
            // return $result;
            if(isset($result)){
                $transaction->commit();
                return [
                    'message' => 'Data Berhasil di simpan',
                ];
            } else {
                $transaction->rollBack();
                return $response['response'] = [
                    'title' => 'Proses Gagal !',
                    'text' => 'Semua Data Gagal di simpan',
                    'status' => 422
               ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDetailHistoryObat()
    {
        $request = Yii::$app->request;
        $obatalkes_id = $request->get('obatalkes_id');
        try {
            $model = new ObatHistoryView;
            $query = $model->find();
            $query->where(['obatalkes_id'=>$obatalkes_id]);
            $result = $query->all();

            return $result;
        } catch (Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetDataObat()
    {
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id');
            
            $model = new ObatHistoryView;
            $query = $model->find();
            $query->where(['obatalkes_id'=>$obatalkes_id]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['tgl_obathistory' => SORT_DESC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        $title = 'Master Base Price Obat';
        try {
            $request = Yii::$app->request;
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();

            $searchObat = '';
            $searchJenis = '';
            $searchKodeObat = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['obatalkes_nama'])) {
                    $searchObat = $advancedFilters['obatalkes_nama'];
                }

                if (!empty($advancedFilters['jenisobatalkes_nama'])) {
                    $searchJenis = $advancedFilters['jenisobatalkes_nama'];
                }

                if (!empty($advancedFilters['obatalkes_kode'])) {
                    $searchKodeObat = $advancedFilters['obatalkes_kode'];
                }
            }

            $model = new ObatAlkesView;
            $query = $this->model();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby([
                        'jenisobatalkes_nama' => SORT_ASC,
                        'obatalkes_nama' => SORT_ASC
                        ]);
            $query = $query->all();

            $data = [];
            if (!empty($query)) {
                $counter = 0;
                foreach ($query as $index => $value) {
                    $data[$counter]['Jenis Obat'] = $value->jenisobatalkes_nama;
                    $data[$counter]['Kode Obat'] = $value->obatalkes_kode;
                    $data[$counter]['Nama Obat'] = $value->obatalkes_nama;
                    $data[$counter]['Harga Dasar Yang Digunakan (Rp.)'] = !empty($value->harganetto_ygdipakai) ? $value->harganetto_ygdipakai : 0;
                    // $data[$counter]['Harga Jual (Rp.)'] = !empty($value->hargaygdipakai) ? $value->hargaygdipakai : 0;
                    $data[$counter]['Satuan Kecil'] = ArrayHelper::getValue($value,'satuankecil_nama','-');
                    $counter++;
                }
            }
            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Jenis Obat' => $searchJenis,
                'Kode Obat' => $searchKodeObat,
                'Nama Obat' => $searchObat,
            ];

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                    2 => '',
                ],
                'data' => [
                    'Nama' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s'),
                    'Diunduh Oleh' => $ruangan->ruangan_nama,
                ]
            ];

            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDownloadExcel()
    {
        $title = '';
        try {
            $request = Yii::$app->request;

            $model = new ObatAlkesView;
            $query = $this->model();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['selisih' => SORT_DESC,
                            'obatalkes_nama' => SORT_ASC
                            ]);
            $resData = $query->all();

            $data = [];
            if (!empty($resData)) {
                $counter = 0;
                foreach ($resData as $index => $value) {
                    $data[$counter]['ID Obat'] = $value->obatalkes_id;
                    $data[$counter]['Nama Obat'] = $value->obatalkes_nama;
                    $data[$counter]['Harga Netto Terakhir (Rp.)'] = !empty($value->hn_last) ? $value->hn_last : 0;
                    $data[$counter]['Suggestion System (Rp.)'] = !empty($value->harga_sugesstion) ? $value->harga_sugesstion : 0;
                    $data[$counter]['Harga Dasar Saat ini (Rp.)'] = !empty($value->harganetto_ygdipakai) ? $value->harganetto_ygdipakai : 0;
                    $data[$counter]['Harga Dasar Yang Akan Digunakan (Rp.)'] = '';
                    $counter++;
                }
            }
            $header = ['Perhatian' =>'Kolom "Harga Dasar Yang Akan Digunakan (Rp.)" yang hanya bisa di ubah.'];
            $footer = [];

            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionExportExcelDetail()
    {
        $title = 'Detail Base Price Obat';
        try {
            $request = Yii::$app->request;
            $obatalkes_id = $request->get('obatalkes_id');
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();

            $searchObat = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['obatalkes_nama'])) {
                    $searchObat = $advancedFilters['obatalkes_nama'];
                }
            }
            $getObatAlkes = $this->actionGetObat($obatalkes_id);


            $model = new ObatHistoryView;
            $query = $model->find();
            $query->where(['obatalkes_id'=>$obatalkes_id]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby(['tgl_obathistory' => SORT_DESC]);
            $getObatHistory = $query->all();

            $data = [];
            if (!empty($getObatHistory)) {
                $counter = 0;
                foreach ($getObatHistory as $index => $value) {
                    $data[$counter]['Tanggal'] = date('d F Y H:i:s', strtotime($value->tgl_obathistory));
                    $data[$counter]['Harga Dasar Yang Digunakan (Rp.)'] = !empty($value->harga_dasar) ?$value->harga_dasar : 0;
                    $data[$counter]['Dibuat Oleh'] = $value->nama_pegawai;
                    $data[$counter]['Keterangan'] = $value->keterangan;
                    $counter++;
                }
            }else{
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Tidak ada data yang diproses.'
                ];
            }
            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Nama Obat' => !empty($getObatAlkes['obatalkes_nama']) ? $getObatAlkes['obatalkes_nama'] : '' ,
                'Harga Netto Terakhir' => !empty($getObatAlkes['hn_last']) ? DocoHelpers::formatNumber($getObatAlkes['hn_last']) : 0 ,
                'Harga Yang Digunakan Saat ini' => !empty($getObatAlkes['harganetto_ygdipakai']) ? DocoHelpers::formatNumber($getObatAlkes['harganetto_ygdipakai']) : 0 ,
            ];
            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Diunduh Oleh' => $ruangan->ruangan_nama,
                ]
            ];
            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #tableObat# => Untuk mengganti data di table Obat
    */
   
    public function actionExportPdf()
    {
        $request = Yii::$app->request;

        $model = new ObatAlkesView;
        $query = $this->model();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderby([
                        'jenisobatalkes_nama' => SORT_ASC,
                        'obatalkes_nama' => SORT_ASC
                        ]);
        $query = $query->asArray()->all();

        $dataObat = [];
        if (!empty($query)) {
            $counter = 0;
            foreach ($query as $index => $value) {
                $counter++;
                $dataObat[] = $value ;
            }
        }
        $print = new DocoPrint();
        $print->attributes = [
                '#tableObat#' => $this->renderPartial('_table_obat', [
                    'dataObat' => $dataObat,
                ]),
            ];

        $print->Output();
    }

    /**
    * @controller actionCetakDetailPdf
    * @attribute #tableObat# => Untuk mengganti data di table Obat
    */
    public function actionCetakDetailPdf()
    {
        $request = Yii::$app->request;
        $obatalkes_id = $request->get('obatalkes_id');

        $model = new ObatAlkesView;
        $query = $this->model();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderby(['obatalkes_nama' => SORT_ASC]);
        $query = $query->asArray()->all();

        $dataObat = [];
        if (!empty($query)) {
            $counter = 0;
            foreach ($query as $index => $value) {
                $counter++;
                $dataObat[] = $value ;
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
                '#tableObat#' => $this->renderPartial('_table_obat', [
                    'dataObat' => $dataObat,
                ]),
            ];

        $print->Output();
    }

}
