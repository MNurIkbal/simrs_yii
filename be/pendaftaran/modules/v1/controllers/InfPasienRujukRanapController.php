<?php

/**
 * @Author: Fajar [fajar.supriadi@sirs.co.id]
 * @Date:   2021-09-06 10:39:52
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\KetersediaanKamar;
use app\modules\v1\models\InfoPasienRujukRanapView;

use app\modules\v1\payload\KetersediaanKamarForm;

class InfPasienRujukRanapController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPasienRujukRanapView';

    const STATUS_BTL_RANAP = 1058;
    const STATUS_RUJUK_RANAP = 433;
    const FILTER_STATUS = [433, 486, 441, 487, 1058];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
    * @author Fajar
    * @since 2021-09-06 10:41:50
    * @param 
    * @return json list data
    * @desc 
    */
    public function actionIndex()
    {
        $model = new InfoPasienRujukRanapView;
        $query = $model::find();

        $request = Yii::$app->request;
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
        }
        if (isset($advancedFilters['status_periksa'])) {
            $status_periksa_id = $advancedFilters['status_periksa'];
            if($status_periksa_id != self::STATUS_RUJUK_RANAP && $status_periksa_id != self::STATUS_BTL_RANAP) {
                $query->andWhere(['or', 
                    ['status_periksa_id'=> $status_periksa_id], 
                    ['prev_status_periksa_id'=> $status_periksa_id]
                ]);
            } else {
                $query->andWhere(['status_periksa_id' => $status_periksa_id]);
            }
            unset($advancedFilters['status_periksa']);
        }
        
        $query->andWhere(['between', 'tglrujukranap', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionPackInformasiPasien(){
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $data['carabayar'] = $this->actionListCaraBayar();
            $data['ruangan'] = $this->actionListRuangan();
            $condition = [
                'lookup_id' => self::FILTER_STATUS
            ];
            $data['status_periksa'] = $this->getLookupByType(null, $condition);
        } catch (\Exception $e) {
            $data = [];
            // return $e->getMessage();
        }

        return $data;
    }

    public function actionBatalRanap()
    {
        $request =  Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id',null);
        if(empty($pendaftaran_id)){
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                'text' => 'Data tidak di temukan'
            ]);
        }

        try {
            $model = Pendaftaran::find()->where([
                'pendaftaran_id' => $pendaftaran_id
            ])->one();
            if (!empty($model)) {
                $model->status_periksa = self::STATUS_BTL_RANAP;
                if (!$model->save()) {
                    $errors = DocoHelpers::parseError($model->errors,'Pendaftaran');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            } else {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Data pendaftaran tidak di temukan'
                ]);    
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                'text' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function actionSaveKetersediaanKamar()
    {
        $request =  Yii::$app->request;
        try {
            $model = new KetersediaanKamarForm;
            $model->attributes = $request->post('kamar');
            if ($model->validate()) {
                $ketersediaanKamar = new KetersediaanKamar;
                $ketersediaanKamar->attributes = $model->attributes;
                if (!$ketersediaanKamar->save()) {
                    $errors = DocoHelpers::parseError($model->errors,'ketersediaanKamar');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors,'KetersediaanKamarForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
            return DocoHelpers::callBack(DocoMessages::KEY_UPDATED);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                'text' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM,[
                'text' => $e->getMessage(),
            ]);
        }
    }

    private function actionListCaraBayar()
    {
        $model = CaraBayar::find();
        $query = $model->select(['carabayar_id', 'carabayar_nama'])
            ->where(['is_active' => true, 'is_deleted' => false])
            ->orderBy(['carabayar_nama'=> SORT_ASC ]);
            
        return $query->asArray()->all();
    }

    private function actionListRuangan()
    {
        $model = Ruangan::find();
        $query = $model->select(['ruangan_id', 'ruangan_nama'])
            ->where(['is_active' => true, 'is_deleted' => false])
            ->andWhere(['instalasi_id' => DocoConstants::VAR_I_RANAP])
            ->orderBy(['ruangan_nama' => SORT_ASC ]);
            
        return $query->asArray()->all();
    }

    public function getLookupByType($type = null, $condition = null)
    {
        $model = Lookup::find();
        $query = $model->select(['lookup_id', 'lookup_name'])
            ->where(['is_active' => true, 'is_deleted' => false]);

        if ($type) {
            $query->andWhere(['lookup_type' => $type]);
        }
        if($condition) {
            foreach($condition as $key => $value) {
                if(is_array($value)) {
                    $query->andWhere(['IN', $key, $value]);
                } else {
                    $query->andWhere([$key => $value]);
                }
            }
        }

        $query->orderBy([
            'lookup_urutan' => SORT_ASC,
            'lookup_name' => SORT_ASC,
        ]);

        return $query->asArray()->all();
    }
}
