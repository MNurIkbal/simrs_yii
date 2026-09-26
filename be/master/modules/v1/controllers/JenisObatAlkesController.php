<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\JenisObatAlkes;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\JenisObatAlkesView;
use app\modules\v1\cache\Cache;

class JenisObatAlkesController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\JenisObatAlkes';

    public $messageBroker = [
        'save' => [
            'services' => [
                'Odoo' => [
                    'KelompokObat' => [
                        'last_insert' => true
                    ]
                ]
            ]
        ],
        'update' => [
            'services' => [
                'Odoo' => [
                    'KelompokObat' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ],
        'delete' => [
            'services' => [
                'Odoo' => [
                    'KelompokObat' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ]
    ];

    public function init()
    {
        parent::init();
        Yii::$app->cache->delete(DocoConstants::VAR_J_OA);
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["generate-api"] = ["GET"];
        $verbs["delete"] = ["DELETE", "POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionSave()
    {
        try {
            $request = Yii::$app->request;
            $model = new JenisObatAlkes;
            $post = $request->post();
            $model->attributes = $post;
            $model->servicegroup_id = $post['service_group'];
            $model->servicecategory_id = $post['service_category'];
            if($model->validate()){
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'JenisObatAlkesForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }else{
                $errors = DocoHelpers::parseError($model->errors,'JenisObatAlkesForm');
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

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new JenisObatAlkesView;
            $query = $model::find();
            $advancedFilters = $request->get('advanced-filter', []);

            if(isset($advancedFilters)){
                if (isset($advancedFilters['group_jenisobat']) ) {
                    $query->andFilterWhere(['group_jenisobat' => $advancedFilters['group_jenisobat'] ]);
                }
                if (isset($advancedFilters['servicegroup_id']) ) {
                    $query->andFilterWhere(['servicegroup_id' => $advancedFilters['servicegroup_id'] ]);
                }
                if (isset($advancedFilters['servicecategory_id']) ) {
                    $query->andFilterWhere(['servicecategory_id' => $advancedFilters['servicecategory_id'] ]);
                }
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['jenisobatalkes_nama' => SORT_ASC ]);

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

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $model = ObatAlkes::find()
                ->where(['jenisobatalkes_id' => $id])
                ->count();

            if($model > 0) {
                $result['status'] = 500;
                $result['text'] = "Tidak bisa menghapus Jenis Obat Alkes, data sudah digunakan di master lain.";
            }
            else {
                $model = JenisObatAlkes::findOne($id);
                if ($model) {
                    $model->is_deleted = true;
                    $model->deleted_date = date('Y-m-d H:i:s');
                    $model->save(false);
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Jenis Obat Alkes Berhasil',
                    ];
                } else {
                    $transaction->rollBack();
                    $result['status'] = 422;
                    $result['text'] = "Gagal Menghapus Data";
                }
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetAttributes()
    {
        $group_jenisobat = Cache::getLookUp('group_jenisobat');
        $service_group = Cache::getServiceGroup();
        $service_category = Cache::getServiceCategory();

        return [
            'group_jenisobat' => $group_jenisobat,
            'service_group' => $service_group,
            'service_category' => $service_category
        ];
    }

    public function actionGetDataSelect2()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', null);
        $payload = $request->get('payload', []);
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $term = isset($payload['term']) ? $payload['term'] : null;

        $result = JenisObatAlkes::find()
            ->select(['jenisobatalkes_id AS id', 'jenisobatalkes_nama AS text'])
            ->where(['is_active' => true]);

        if(!empty($term)) {
            $result->andWhere(['like', 'LOWER(jenisobatalkes_nama)', strtolower($term)]);
        }

        $result->orderBy(['jenisobatalkes_id' => SORT_ASC]);

        if(!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }
}