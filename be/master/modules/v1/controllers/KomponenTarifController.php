<?php

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

use app\modules\v1\models\KomponenTarif;
use app\modules\v1\models\TarifTindakan;
use app\modules\v1\models\Ruangan;

class KomponenTarifController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KomponenTarif';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["delete"] = ["POST", "GET", "DELETE"];
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

    private function Model(){
        $model = new KomponenTarif;
        return $model::find();
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new KomponenTarif;
            
            $query = KomponenTarif::find();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['is_active']) ) {
                    $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['created_date' => SORT_DESC]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $getKomponenTarif = KomponenTarif::find()
            ->where(['komponentarif_id' => $id])->all();
        
        $valKomponenTarif_id = [];
        foreach ($getKomponenTarif as $key => $value) {
            $valKomponenTarif_id[] = $value['komponentarif_id'];
        }

        $check = TarifTindakan::find()
            ->where([
                'IN', 'komponentarif_id', $valKomponenTarif_id
            ])->all();

        if (empty($check)) {
            $delete = (new KomponenTarif)->delete($id);
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
        } 
        return [
            'status' => 422,
            'title' => 'Proses Hapus Gagal !',
            'text' => 'Data sudah digunakan di Master lain.'
        ];
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $jenisKomponen = $request->post('jenis_komponen');
            $model = new KomponenTarif;
            $model->attributes = $request->post();
            $model->persen_delegasi = DocoHelpers::convertCommaToPoint( $request->post('persen_delegasi') );
            if ($model->persen_delegasi < 0) {
                $title = '<strong>'.'Persentase Delegasi'.'</strong>'.' tidak boleh kurang minus.';
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => $title,
                            'status' => 422
                       ];
            }

            $model->is_dokter = ($jenisKomponen == 'is_dokter') ? true : false;
            $model->is_perawat = ($jenisKomponen == 'is_perawat') ? true : false;
            $model->is_fisioterapis = ($jenisKomponen == 'is_fisioterapis') ? true : false;
            $model->is_dietisien = ($jenisKomponen == 'is_dietisien') ? true : false;
            $model->is_radiografer = ($jenisKomponen == 'is_radiografer') ? true : false;
            if($model->validate()){
                if ($model->save()) {
                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($model->errors, 'KomponenTarifForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }else{
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($model->errors,'KomponenTarifForm');
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

    public function actionUpdate()
    {
        try {
            $request = Yii::$app->request;
            $jenisKomponen = $request->post('jenis_komponen');
            $id = $request->get('id');
            $model = KomponenTarif::findOne($id);
            $model->attributes = $request->post();
            $model->persen_delegasi = DocoHelpers::convertCommaToPoint( $request->post('persen_delegasi') );
            $model->is_dokter = ($jenisKomponen == 'is_dokter') ? true : false;
            $model->is_perawat = ($jenisKomponen == 'is_perawat') ? true : false;
            $model->is_fisioterapis = ($jenisKomponen == 'is_fisioterapis') ? true : false;
            $model->is_dietisien = ($jenisKomponen == 'is_dietisien') ? true : false;
            $model->is_radiografer = ($jenisKomponen == 'is_radiografer') ? true : false;
            if ($model->update()) {
                return [
                    'message' => 'Data Berhasil di ubah',
                ];
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'KomponenTarifForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id = null)
    {
      return $this->getData($id)->asArray()->one();
    }

    public function getData($id = null)
    {
      $data = KomponenTarif::find();
      if ($id) {
          $data->where(['komponentarif_m.komponentarif_id' => $id]);
      }

      return $data;
    }

    /**
    * @controller actionPrintKomponen
    * @attribute #table_detail# => menampilkan data master komponen tarif tindakan
    **/

    public function actionPrintKomponen()
    {
      $model = new KomponenTarif;
      $query = $model::find();

      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      $data = $query->asArray()->all();

      $result = ['data'=>$data];

      $print = new DocoPrint();
      $print->attributes = [
          '#table_detail#' => $this->renderPartial('index',$result),
      ];
      $print->Output();
    }

    public function actionExportExcel()
    {
        $title = 'Master Komponen';
        try {
            $request = Yii::$app->request;
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();

            $searchKode = '';
            $searchNama = '';
            $searchNamaLainnya = '';
            // $searchStatus = '';
            $searchCatatan = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['komponentarif_kode'])) {
                    $searchKode = $advancedFilters['komponentarif_kode'];
                }

                if (!empty($advancedFilters['komponentarif_nama'])) {
                    $searchNama = $advancedFilters['komponentarif_nama'];
                }

                if (!empty($advancedFilters['komponentarif_namalainnya'])) {
                    $searchNamaLainnya = $advancedFilters['komponentarif_namalainnya'];
                }

                if (!empty($advancedFilters['is_active'])) {
                    $searchStatus = $advancedFilters['is_active'];
                }

                if (!empty($advancedFilters['catatan'])) {
                    $searchCatatan = $advancedFilters['catatan'];
                }
            }

            $model = new KomponenTarif;
            
            $query = KomponenTarif::find();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['is_active']) ) {
                    $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['created_date' => SORT_DESC]);
            $result = $query->asArray()->all();

            $data = [];
            if (!empty($result)) {
                $counter = 0;
                foreach ($result as $index => $value) {
                    $data[$counter][\Yii::t('app', 'Kode Komponen')] = $value['komponentarif_kode'];
                    $data[$counter][\Yii::t('app', 'Nama Komponen')] = $value['komponentarif_nama'];
                    $data[$counter][\Yii::t('app', 'Nama Lainnya')] = $value['komponentarif_namalainnya'];
                    $data[$counter][\Yii::t('app', 'Presentasi Delegasi')] = $value['persen_delegasi'];
                    $data[$counter][\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
                    $data[$counter][\Yii::t('app', 'Catatan')] = $value['catatan'];
                    $counter++;
                }
            }
            
            if (empty($searchStatus)) {
               $searchStatused = '';
            }else{
                $searchStatused = ($searchStatus == 1 ) ? 'Aktif' : 'Tidak Aktif';
            }


            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Kode Komponen' => $searchKode,
                'Nama Komponen' => $searchNama,
                // 'Nama Lainnya' => $searchNamaLainnya,
                'Status' => $searchStatused,
                // 'Catatan' => $searchCatatan,
            ];

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Nama' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s'),
                    // 'Diunduh Oleh' => $ruangan->ruangan_nama,
                ]
            ];

            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data di table 
    * @attribute #searchKode# => Untuk menampilkan data pencarian Kode komponene 
    * @attribute #searchNama# => Untuk menampilkan data pencarian nama komponene 
    * @attribute #searchNamaLainnya# => Untuk menampilkan data pencarian nama lainnya 
    * @attribute #searchStatused# => Untuk menampilkan data pencarian status 
    * @attribute #searchCatatan# => Untuk menampilkan data pencarian catatan 
   */
   
    public function actionExportPdf()
    {
        $request = Yii::$app->request;

        $model = new KomponenTarif;
        $query = KomponenTarif::find();

        $searchKode = '';
        $searchNama = '';
        $searchNamaLainnya = '';
        // $searchStatus = '';
        $searchCatatan = '';
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (!empty($advancedFilters['komponentarif_kode'])) {
                $searchKode = $advancedFilters['komponentarif_kode'];
            }

            if (!empty($advancedFilters['komponentarif_nama'])) {
                $searchNama = $advancedFilters['komponentarif_nama'];
            }

            if (!empty($advancedFilters['komponentarif_namalainnya'])) {
                $searchNamaLainnya = $advancedFilters['komponentarif_namalainnya'];
            }

            if (!empty($advancedFilters['is_active'])) {
                $searchStatus = $advancedFilters['is_active'];
            }

            if (!empty($advancedFilters['catatan'])) {
                $searchCatatan = $advancedFilters['catatan'];
            }
        }

        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (isset($advancedFilters['is_active']) ) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
            }
        }

        if (empty($searchStatus)) {
           $searchStatused = '';
        }else{
            $searchStatused = ($searchStatus == 1 ) ? 'Aktif' : 'Tidak Aktif';
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['created_date' => SORT_DESC]);
        $getData = $query->asArray()->all();

        $result = [];
        if (!empty($getData)) {
            $counter = 0;
            foreach ($getData as $index => $value) {
                $counter++;
                $result[] = $value ;
            }
        }
        $print = new DocoPrint();
        $print->attributes = [
                '#datatable#' => $this->renderPartial('_dataTable_pdf', [
                    'result' => $result,
                ]),
                '#searchKode#' => $searchKode,
                '#searchNama#' => $searchNama,
                '#searchNamaLainnya#' => $searchNamaLainnya,
                '#searchStatused#' => $searchStatused,
                '#searchCatatan#' => $searchCatatan,
            ];

        $print->Output();
    }


}
