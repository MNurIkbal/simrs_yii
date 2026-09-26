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

use app\modules\v1\models\PerdaTarif;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\TarifTindakan;
use \DateTime;

class PerdaTarifController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PerdaTarif';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["delete"] = ["POST", "GET"];
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

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new PerdaTarif;
            $query = $model::find();
            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:00');
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['perda_tgl'])) {
                    $explode = explode(' - ', $advancedFilters['perda_tgl']);
                    $start_date = date('Y-m-d', strtotime($explode[0]));
                    $end_date = date('Y-m-d', strtotime($explode[1]));
                    unset($advancedFilters['perda_tgl']);
                    $query->andWhere(['between', 'perda_tgl', $start_date, $end_date]);
                }

                if (isset($advancedFilters['is_active']) ) {
                    $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
                }
            }

            // $query->andWhere(['between', 'perda_tgl', $tgl_awal, $tgl_akhir]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['is_active' => SORT_DESC,
                            'created_date' => SORT_DESC
                            ]);
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
        $getData = PerdaTarif::find()
            ->where(['perdatarif_id' => $id])->all();
        
        $getValueKeys = [];
        foreach ($getData as $key => $value) {
            $getValueKeys[] = $value['perdatarif_id'];
        }

        $check = TarifTindakan::find()
            ->where([
                'IN', 'perdatarif_id', $getValueKeys
            ])->all();

        if (empty($check)) {
            $delete = (new PerdaTarif)->delete($id);
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
        } 
        return [
            'status' => 422,
            'title' => 'Proses Hapus Gagal !',
            'text' => 'Data sudah ini di pakai'
        ];
    }

    private function checkIsActive($model)
    {
        if ($model->is_active == 1) {
            $getData = PerdaTarif::find()
                        ->where(['is_active' => true])
                        ->andWhere(['is_deleted' => false])
                        ->one();
            if (!empty($model)) {
                return $getData;
            }
        }

    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $isValid = true;
            $model = new PerdaTarif;
            $model->attributes = $request->post();
            // $check = $this->checkIsActive($model);
            // if (!empty($check)) {
            //     $model->is_active = 0;
            // }
            $cookies = Yii::$app->response->cookies;
            $tglPerda = $request->post('perda_tgl');
            $tglPerda = date("Y-m-d", strtotime( $model->perda_tgl ));
            $model->perda_tgl = $tglPerda;
            $getData = PerdaTarif::find()
                    ->where(['is_active' => true])
                    ->andWhere(['is_deleted' => false])
                    ->all();
                    
            if(!empty($getData)) {
                foreach ($getData as $key => $value) {
                    $tanggalPerda = isset($value['perda_tgl']) ? $value['perda_tgl'] : '';
                    if($tanggalPerda == $tglPerda) {
                        $isValid = false;
                    }
                }
                if(!$isValid) {
                    $title = 'Tanggal Berlaku Perda Sudah Ada. Mohon isi Tanggal Berlaku Yang Berbeda.';
                    return $response['response'] = [
                        'title' => 'Proses Gagal !',
                        'text' => $title,
                        'status' => 422
                    ];
                }
            }
            if($model->validate()){
                if ($model->save()) {
                    $transaction->commit();
                    // delete cache untuk dropdown Perda / SK
                    Yii::$app->cache->delete('cache_perda_tarif');

                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($model->errors, 'PerdaTarifForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }else{
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($model->errors,'PerdaTarifForm');
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

    public function actionView($id = null)
    {
      return $this->getData($id)->asArray()->one();
    }

    public function getData($id = null)
    {
      $data = [];
      $data = PerdaTarif::find()
                      ->select([
                              'perdatarif_m.perdatarif_id',
                              'perdatarif_m.perdanama_sk',
                              'perdatarif_m.perda_no',
                              'perdatarif_m.perda_tgl',
                              'perdatarif_m.perda_tentang',
                              'perdatarif_m.nama_lainnya',
                              'perdatarif_m.ditetapkan_oleh',
                              'perdatarif_m.tempat_ditetapkan',
                              'perdatarif_m.is_active'
                      ]);
      if ($id) {
          $data->where(['perdatarif_m.perdatarif_id' => $id]);
      }

      return $data;
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = PerdaTarif::findOne($id);
            $cookies = Yii::$app->response->cookies;
            if ($request->post() && !empty($model)) {
                $isValid = true;
                $model->attributes = $request->post();
                $tglPerda = $request->post('perda_tgl');
                $tglPerda = date("Y-m-d", strtotime( $tglPerda ));
                $getData = PerdaTarif::find()
                        ->where(['is_active' => true])
                        ->andWhere(['is_deleted' => false])
                        ->all();
                
                if (isset($getData)) {
                    foreach ($getData as $key => $value) {
                        $tanggalPerda = isset($value['perda_tgl']) ? $value['perda_tgl'] : '';
                        if($tanggalPerda == $tglPerda && $value['perdatarif_id'] != $id) {
                            $isValid = false;
                        }
                    }
                    if(!$isValid) {
                        $title = 'Tanggal Berlaku Perda Sudah Ada. Mohon isi Tanggal Berlaku Yang Berbeda.';
                        return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => $title,
                            'status' => 422
                        ];
                    }
                }
                $model->perda_tgl = $tglPerda;
                if ($model->update()) {
                    // delete cache untuk dropdown Perda / SK
                    Yii::$app->cache->delete('cache_perda_tarif');
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PerdaTarifForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdateActived()
    {
        try {
            $request = Yii::$app->request;
            $perdatarif_id_before = $request->get('perdatarif_id_before');
            $perdatarif_id_now = $request->get('perdatarif_id_now');

            $dataBefore = PerdaTarif::findOne($perdatarif_id_before);
            $dataBefore->is_active = 0;
            $dataBefore->update();

            $dataNow = PerdaTarif::findOne($perdatarif_id_now);
            $dataNow->is_active = 1;
            $dataNow->update();

            return ['message' => 'Data Berhasil di simpan',
                    ];
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionPrintPerda
    * @attribute #table_detail# => menampilkan data master perda tarif tindakan
    **/

    public function actionPrintPerda()
    {
      $model = new PerdaTarif;
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
        $title = 'Master Perda';
        try {
            $request = Yii::$app->request;
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();

            $searchNoSK = '';
            $searchNamaPerda = '';
            $searchNamaLainnya = '';
            // $searchStatus = '';
            $searchTglBerlaku = '';
            $searchDitetapkanOleh = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['perda_no'])) {
                    $searchNoSK = $advancedFilters['perda_no'];
                }
                if (!empty($advancedFilters['perdanama_sk'])) {
                    $searchNamaPerda = $advancedFilters['perdanama_sk'];
                }

                if (isset($advancedFilters['perda_tgl'])) {
                    $explode = explode(' - ', $advancedFilters['perda_tgl']);
                    $start_date = date('Y-m-d', strtotime($explode[0]));
                    $end_date = date('Y-m-d', strtotime($explode[1]));
                    $searchTglBerlaku = date('d M Y', strtotime($start_date)).' - '.date('d M Y', strtotime($end_date));
                }

                if (!empty($advancedFilters['ditetapkan_oleh'])) {
                    $searchDitetapkanOleh = $advancedFilters['ditetapkan_oleh'];
                }
                if (!empty($advancedFilters['is_active'])) {
                    $searchStatus = $advancedFilters['is_active'];
                }
            }

            $model = new PerdaTarif;
            
            $query = PerdaTarif::find();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['perda_tgl'])) {
                    $explode = explode(' - ', $advancedFilters['perda_tgl']);
                    $start_date = date('Y-m-d', strtotime($explode[0]));
                    $end_date = date('Y-m-d', strtotime($explode[1]));
                    unset($advancedFilters['perda_tgl']);
                    $query->andWhere(['between', 'perda_tgl', $start_date, $end_date]);
                }

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
                    $data[$counter][\Yii::t('app', 'Nomor SK')] = $value['perda_no'];
                    $data[$counter][\Yii::t('app', 'Nama Perda')] = $value['perdanama_sk'];
                    $data[$counter][\Yii::t('app', 'Tanggal Berlaku')] = $value['nama_lainnya'];
                    $data[$counter][\Yii::t('app', 'Detail')] = $value['perda_tentang'];
                    $data[$counter][\Yii::t('app', 'Ditetapkan Oleh')] = $value['ditetapkan_oleh'];
                    $data[$counter][\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
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
                'Nomor SK' => $searchNoSK,
                'Nama Perda' => $searchNamaPerda,
                'Tanggal Berlaku' => $searchTglBerlaku,
                'Ditetapkan Oleh' => $searchDitetapkanOleh,
                'Status' => $searchStatused,
            ];

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Nama' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s'),
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
    * @attribute #searchNoSK# => Untuk menampilkan data pencarian Nomor SK 
    * @attribute #searchNama# => Untuk menampilkan data pencarian nama perda 
    * @attribute #searchTanggalBerlaku# => Untuk menampilkan data tanggal berlaku 
    * @attribute #searchDitetapkanOleh# => Untuk menampilkan data pencarian ditetapkan oleh 
    * @attribute #searchStatused# => Untuk menampilkan data pencarian status 
   */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $model = new PerdaTarif;
        $query = PerdaTarif::find();
        $searchNoSK = '';
        $searchNamaPerda = '';
        $searchNamaLainnya = '';
        $searchTglBerlaku = '';
        $searchDitetapkanOleh = '';
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (!empty($advancedFilters['perda_no'])) {
                $searchNoSK = $advancedFilters['perda_no'];
            }
            if (!empty($advancedFilters['perdanama_sk'])) {
                $searchNamaPerda = $advancedFilters['perdanama_sk'];
            }

            if (isset($advancedFilters['perda_tgl'])) {
                $explode = explode(' - ', $advancedFilters['perda_tgl']);
                $start_date = date('Y-m-d', strtotime($explode[0]));
                $end_date = date('Y-m-d', strtotime($explode[1]));
                $searchTglBerlaku = date('d M Y', strtotime($start_date)).' - '.date('d M Y', strtotime($end_date));
                $query->andWhere(['between', 'perda_tgl', $start_date, $end_date]);
                unset($advancedFilters['perda_tgl']);
            }

            if (!empty($advancedFilters['ditetapkan_oleh'])) {
                $searchDitetapkanOleh = $advancedFilters['ditetapkan_oleh'];
            }
            if (!empty($advancedFilters['is_active'])) {
                $searchStatus = $advancedFilters['is_active'];
                $query->andFilterWhere(['is_active' =>  $searchStatus]);
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
        $print = new DocoPrint('perda-tarif');
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('_dataTable_pdf', [
                'result' => $result,
            ]),
            '#searchNoSK#' => $searchNoSK,
            '#searchNama#' => $searchNamaPerda,
            '#searchTanggalBerlaku#' => $searchTglBerlaku,
            '#searchDitetapkanOleh#' => $searchDitetapkanOleh,
            '#searchStatused#' => $searchStatused,
        ];

        $print->Output();
    }

}
