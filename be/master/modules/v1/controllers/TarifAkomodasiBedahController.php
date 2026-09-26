<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;

use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;


use app\modules\v1\models\PerdaTarif;
use app\modules\v1\models\MasterTarifAkomodasiBedahView;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\KegiatanOperasi;
use app\modules\v1\models\TarifAkomodasiBedah;


class TarifAkomodasiBedahController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TarifAkomodasiBedah';
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["save-tarif"] = ["POST"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["update-tarif"] = ["PUT"];
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

    public function data()
    {
       $model = new TarifTindakanView;
       return $model;
    }

    public function masterData()
    {
       $model = new MasterTarifAkomodasiBedahView;
       return $model;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = $this->masterData();
            $query = $model::find();
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                // if (isset($advancedFilters['is_active']) ) {
                //     $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
                // }
                // if (isset($advancedFilters['perdanama_sk']) ) {
                //     $query->andFilterWhere(['perdanama_sk' =>  $advancedFilters['perdanama_sk'] ]);
                // }
                // if (isset($advancedFilters['kelaspelayanan_nama']) ) {
                //     $query->andFilterWhere(['kelaspelayanan_nama' =>  $advancedFilters['kelaspelayanan_nama'] ]);
                // }
                // if (isset($advancedFilters['kegiatanoperasi_nama']) ) {
                //     $query->andFilterWhere(['kegiatanoperasi_nama' =>  $advancedFilters['kegiatanoperasi_nama'] ]);
                // }
            }

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

    private function getTarifDataView($tariftindakan_id ,$komponentarif_id = false)
    {
        $model = $this->masterData();
        $query = $model::find();
        $query->where(['tariftindakan_id' => $tariftindakan_id ]);

        if ($komponentarif_id) {
        $query->andWhere(['komponentarif_id'=>DocoConstants::KOMPONEN_TARIF]);
        }
        return $query;
    }

    public function actionDelete($id)
    {
        try {
            $result = (new TarifAkomodasiBedah)->delete($id);
            return DocoHelpers::callBack(DocoMessages::KEY_DELETED);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSaveTarif()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        if($request->post()){
          try {
            $model = new TarifAkomodasiBedah;
            $model->attributes = $request->post();
            if ($model->validate() && $model->save()) {
              return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => $model->errors['message']
                ]);
            }
              return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
          } catch (\yii\db\Exception $e) {
              Yii::$app->response->statusCode = 500;
              return ['message' => $e->getMessage()];
          } catch (\Exception $e) {
              Yii::$app->response->statusCode = 500;
              return ['message' => $e->getMessage()];
          }

        }else{
          return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
        }

    }

    public function actionUpdateTarif($id)
    {
      $request = Yii::$app->request;
      $connection = Yii::$app->db;
      $post = $request->post()['TarifAkomodasiBedahForm'];
      if($request->post()){
        try {
          $model = TarifAkomodasiBedah::find()->where(['tarifbedah_id' => $id])->one();
          $model->attributes = $post;
          if ($model->validate() && $model->save()) {
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
          } else {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
              'text' => $model->errors['message']
            ]);
          }
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }

      }else{
        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM);
      }
    }
    
    public function actionDeleteTarif($id)
    {
      $request = Yii::$app->request;
      $connection = Yii::$app->db;
        try {
          $model = TarifAkomodasiBedah::find()->where(['tarifbedah_id' => $id])->one();
          $model->is_deleted = true;
          if ($model->validate() && $model->save()) {
            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
          } else {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
              'data' => $model->errors
            ]);
          }
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
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

            $searchJenisTindakan = '';
            $searchNamaTindakan = '';
            $searchKelasPelayanan = '';
            $searchCaraBayar = '';
            $searchPenjamin = '';
            $searchPerdaSK = '';

            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['jenis_tindakan_paket'])) {
                    $searchJenisTindakan = $advancedFilters['jenis_tindakan_paket'];
                }

                if (!empty($advancedFilters['nama_tindakan_paket'])) {
                    $searchNamaTindakan = $advancedFilters['nama_tindakan_paket'];
                }
                // if (!empty($advancedFilters['kelaspelayanan_nama'])) {
                //     $getKelasPelayanan = KelasPelayanan::find($advancedFilters['kelaspelayanan_nama'])->one();
                //     $searchKelasPelayanan = $getKelasPelayanan->kelaspelayanan_nama;
                // }

                // if (!empty($advancedFilters['carabayar_id'])) {
                //     $getCaraBayar = CaraBayar::find($advancedFilters['carabayar_id'])->one();
                //     $searchCaraBayar = $getCaraBayar->carabayar_nama;
                // }

                // if (!empty($advancedFilters['penjamin_id'])) {
                //     $getPenjamin = Penjamin::find($advancedFilters['penjamin_id'])->one();
                //     $searchPenjamin = $getPenjamin->penjamin_nama;
                // }

                // if (!empty($advancedFilters['perdatarif_id'])) {
                //     $getPerdatarif = PerdaTarif::find($advancedFilters['perdatarif_id'])->one();
                //     $searchPerdaSK = $getPerdatarif->perdanama_sk;
                // }

                if (!empty($advancedFilters['is_active'])) {
                    $searchStatus = $advancedFilters['is_active'];
                }
            }

            $model = $this->masterData();
            $query = $model::find();
            $query->where(['komponentarif_id'=>DocoConstants::KOMPONEN_TARIF]);
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
                    $data[$counter][\Yii::t('app', 'Jenis Tindakan / Paket')] = $value['jenis_tindakan_paket'];
                    $data[$counter][\Yii::t('app', 'Nama Tindakan / Paket')] = $value['nama_tindakan_paket'];
                    $data[$counter][\Yii::t('app', 'Kelas Pelayanan')] = $value['kelaspelayanan_nama'];
                    $data[$counter][\Yii::t('app', 'Cara bayar')] = $value['carabayar_nama'];
                    $data[$counter][\Yii::t('app', 'Penjamin')] = $value['penjamin_nama'];
                    $data[$counter][\Yii::t('app', 'Perda / SK')] = $value['perdanama_sk'];
                    $data[$counter][\Yii::t('app', 'Persen Cyto (%)')] = "'".$value['persencyto_tindakan']."'";
                    $data[$counter][\Yii::t('app', 'Persen Diskon (%)')] = "'".$value['persendiskon_tindakan']."'";
                    $data[$counter][\Yii::t('app', 'Harga (Rp)')] = !empty($value['harga_tariftindakan']) ? DocoHelpers::formatNumber($value['harga_tariftindakan']) : 0 ;
                    $data[$counter][\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
                    if (!empty($advancedFilters['kelaspelayanan_nama'])) {
                        $searchKelasPelayanan = $value['kelaspelayanan_nama'];
                    }
                    if (!empty($advancedFilters['carabayar_id'])) {
                        $searchCaraBayar = $value['carabayar_nama'];
                    }
                    if (!empty($advancedFilters['penjamin_id'])) {
                        $searchPenjamin = $value['penjamin_nama'];
                    }

                    if (!empty($advancedFilters['perdatarif_id'])) {
                        $searchPerdaSK = $value['perdanama_sk'];
                    }
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
                'Jenis Paket/Tindakan' => $searchJenisTindakan,
                'Nama Paket/Tindakan' => $searchNamaTindakan,
                'Kelas Pelayanan' => $searchKelasPelayanan,
                'Cara Bayar' => $searchCaraBayar,
                'Penjamin' => $searchPenjamin,
                'Perda/SK' => $searchPerdaSK,
                'Status' => $searchStatused,
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
            $footer = count($data) > 0 ? $footer : [] ;
            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;

        $model = $this->masterData();
        $query = $model::find();
        $query->where(['komponentarif_id'=>DocoConstants::KOMPONEN_TARIF]);

        $searchJenisTindakan = '';
        $searchNamaTindakan = '';
        $searchKelasPelayanan = '';
        $searchCaraBayar = '';
        $searchPenjamin = '';
        $searchPerdaSK = '';

        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (!empty($advancedFilters['jenis_tindakan_paket'])) {
                $searchJenisTindakan = $advancedFilters['jenis_tindakan_paket'];
            }

            if (!empty($advancedFilters['nama_tindakan_paket'])) {
                $searchNamaTindakan = $advancedFilters['nama_tindakan_paket'];
            }
            if (!empty($advancedFilters['kelaspelayanan_id'])) {
                $getKelasPelayanan = KelasPelayanan::find($advancedFilters['kelaspelayanan_id'])->one();
                $searchKelasPelayanan = $getKelasPelayanan->kelaspelayanan_nama;
            }

            if (!empty($advancedFilters['carabayar_id'])) {
                $getCaraBayar = CaraBayar::find($advancedFilters['carabayar_id'])->one();
                $searchCaraBayar = $getCaraBayar->carabayar_nama;
            }

            if (!empty($advancedFilters['penjamin_id'])) {
                $getPenjamin = Penjamin::find($advancedFilters['penjamin_id'])->one();
                $searchPenjamin = $getPenjamin->penjamin_nama;
            }

            if (!empty($advancedFilters['perdatarif_id'])) {
                $getPerdatarif = PerdaTarif::find($advancedFilters['perdatarif_id'])->one();
                $searchPerdaSK = $getPerdatarif->perdanama_sk;
            }

            if (!empty($advancedFilters['is_active'])) {
                $searchStatus = $advancedFilters['is_active'];
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
                '#searchJenisTindakan#' => $searchJenisTindakan,
                '#searchNamaTindakan#' => $searchNamaTindakan,
                '#searchKelasPelayanan#' => $searchKelasPelayanan,
                '#searchCaraBayar#' => $searchCaraBayar,
                '#searchPenjamin#' => $searchPenjamin,
                '#searchPerdaSK#' => $searchPerdaSK,
                '#searchStatused#' => $searchStatused,
            ];
        $print->Output();
    }

    public function actionGetDataTarif($id = null){
      $data = MasterTarifAkomodasiBedahView::find();
        if(!empty($id)){
          $result = $data->where(['tarifbedah_id' => $id])->one();
        }else{
          $result = $data->where(['is_active' => true])
              ->orderBy(['kegiatanoperasi_nama' => SORT_ASC]);
        }
        return $result;
    }

    public function actionPackTarif()
    {
        $kelaspelayanan = $this->getKelasPelayanan();
        $perdatarif = $this->getPerdaTarif();
        $kegiatanoperasi =  $this->getKegiatanOperasi();
        return [
            'kegiatanoperasi' => $kegiatanoperasi,
            'kelaspelayanan' => $kelaspelayanan,
            'perdatarif' => $perdatarif,
        ];
    }

    private function getKelasPelayanan()
    {
        $result = KelasPelayanan::find()->where(['is_active' => true])
            ->orderBy(['kelaspelayanan_nama' => 'ASC'])->asArray()->All();
        return $result;
    }

    private function getPerdaTarif()
    {
        $result = PerdaTarif::find()
        ->where(['is_active' => true])
        ->orderBy([
            'is_active' => true,
            'perdanama_sk' => 'ASC',
        ])->asArray()->All();
        return $result;
    }

    private function getKegiatanOperasi()
    {
        $result = KegiatanOperasi::find()->where(['is_active' => true])
            ->orderBy(['kegiatanoperasi_nama' => 'ASC'])->asArray()->All();
        return $result;
    }

}
