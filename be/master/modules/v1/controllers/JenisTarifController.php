<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\JenisTarif;
use app\modules\v1\models\JenisTarifPenjamin;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;

class JenisTarifController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JenisTarif';

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
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'penjamin_m,jenistarif_m');

        $model = new JenisTarifPenjamin;
        $query = $model::find()->joinWith(
                  array(
                        'penjamin' => function($query){
                          $query->select(array('penjamin_id','penjamin_nama'));
                        },
                        'jenistarif' => function($query){
                          $query->select(array('jenistarif_id','jenistarif_kode','jenistarif_nama','jenistarif_namalainnya','is_active','catatan'));
                        }
                  )
        );

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDelete($id)
    {
        try {
            $result = (new JenisTarif)->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new JenisTarif;

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            if ($request->post()) {
                $model->attributes = $request->post();
                $model->data_mapping = $request->post()['data_mapping'];
                if ($model->validate()) {
                    if ($model->save(false)) {
                        if ($model->data_mapping) {
                            $listTarif = json_decode($model->data_mapping);

                            foreach ($listTarif as $penjamin_id) {
                              $model_mp = new JenisTarifPenjamin;
                              $model_mp->jenistarif_id = $model->jenistarif_id;
                              $model_mp->penjamin_id = $penjamin_id;
                              $model_mp->save();
                            }
                        }
                        $transaction->commit();
                        return ['message'=>'data berhasil di simpan'];
                    }
                } else {
                    return $model->getErrors();
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionView($id = null)
    {
      $result['data_mapping'] = $this->getDataPenjamin($id)->asArray()->all();
      $result['data'] = $this->getData($id)->asArray()->one();
      return $result;
    }

    public function getDataPenjamin($id = null)
    {
      $data = JenisTarifPenjamin::find()
                      ->select([
                              'jenistarifpenjamin_mp.penjamin_id'
                      ]);
      if ($id) {
          $data->where(['jenistarifpenjamin_mp.jenistarif_id' => $id]);
      }
      return $data;
    }

    public function getData($id = null)
    {
      $data = JenisTarif::find()
                      ->select([
                              'jenistarif_m.jenistarif_id',
                              'jenistarif_m.jenistarif_nama',
                              'jenistarif_m.jenistarif_namalainnya',
                              'jenistarif_m.jenistarif_kode',
                              'jenistarif_m.is_active',
                              'jenistarif_m.catatan'
                      ]);
      if ($id) {
          $data->where(['jenistarif_m.jenistarif_id' => $id]);
      }
      return $data;
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = new JenisTarif;

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            if ($request->post()) {
                $model->attributes = $request->post();

                $model->data_mapping = $request->post()['data_mapping'];
                $result_mapping = $this->getDataPenjamin($id)->asArray()->all();
                foreach ($result_mapping as $value) {
                  $data_array[] = $value['penjamin_id'];
                }

                if ($model->validate()) {
                    if ($model->save(false)) {
                        if ($model->data_mapping) {
                            $listTarif = json_decode($model->data_mapping);
                            $listGabung = array_merge($listTarif,$data_array);

                            foreach ($listGabung as $value) {
                              $data_exist = JenisTarifPenjamin::find()->where(['jenistarif_id'=>$id,'penjamin_id'=>$value])->one();
                              if ($data_exist) {
                                if (!in_array($value, $listTarif)) {
                                  $data_exist->is_deleted = true;

                                  if (!$data_exist->validate()) {
                                    throw new \yii\db\Exception("Data Tidak Di validate",$model_mp->getErrors());
                                  }
                                  if (!$data_exist->update()) {
                                    throw new \yii\db\Exception("Data Tidak Di Temukan",$model_mp->getErrors());
                                  }
                                }
                              }else{
                                if (in_array($value, $listTarif)) {
                                  $model_mp = new JenisTarifPenjamin;
                                  $model_mp->jenistarif_id = $id;
                                  $model_mp->penjamin_id = (int)$value;

                                  if (!$model_mp->validate()) {
                                    throw new \yii\db\Exception("Data Tidak Di validate",$model_mp->getErrors());
                                  }
                                  if (!$model_mp->save()) {
                                    throw new \yii\db\Exception("Data Tidak Di Temukan",$model_mp->getErrors());
                                  }

                                }
                              }
                            }
                        }
                        $transaction->commit();
                        return ['message'=>'data berhasil di simpan'];
                    }
                } else {
                    return $model->getErrors();
                }
            }
            throw new \yii\db\Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
          $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'errorInfo'=>$e->errorInfo
            ];
        } catch (\Exception $e) {
          $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionPrintJenisTarif
    * @attribute #table_detail# => menampilkan data master Jenis tarif
    **/

    public function actionPrintJenisTarif()
    {
      $model = new JenisTarifPenjamin;
      $query = $model::find()->joinWith(
                array(
                      'penjamin' => function($query){
                        $query->select(array('penjamin_id','penjamin_nama'));
                      },
                      'jenistarif' => function($query){
                        $query->select(array('jenistarif_id','jenistarif_kode','jenistarif_nama','jenistarif_namalainnya','is_active','catatan'));
                      }
                )
      );

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
        $request = Yii::$app->request;
        $type = $request->get('type');
        $title = 'MASTER JENIS TARIF';

        $model = new JenisTarifPenjamin;
        $query = $model::find()->joinWith(
                  array(
                        'penjamin' => function($query){
                          $query->select(array('penjamin_id','penjamin_nama'));
                        },
                        'jenistarif' => function($query){
                          $query->select(array('jenistarif_id','jenistarif_kode','jenistarif_nama','jenistarif_namalainnya','is_active','catatan'));
                        }
                  )
        )
        ->asArray();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];

                $newValue[\Yii::t('app', 'Kode Jenis Tarif')] = $value['jenistarif']['jenistarif_kode'];
                $newValue[\Yii::t('app', 'Nama Jenis Tarif')] = $value['jenistarif']['jenistarif_nama'];
                $newValue[\Yii::t('app', 'Nama lainnya')] = $value['jenistarif']['jenistarif_namalainnya'];
                $newValue[\Yii::t('app', 'Penjamin')] = $value['penjamin']['penjamin_nama'];
                $newValue[\Yii::t('app', 'Status')] = ($value['jenistarif']['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
                $newValue[\Yii::t('app', 'Catatan')] = $value['jenistarif']['catatan'];

            $result[$key] = $newValue;
        }

        $header = array();

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));

        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }


}
