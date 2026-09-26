<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\PlafonBpjs;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\PlafonBpjsView;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoConstansId;

class PlafonBpjsController extends \Doco\components\DocoActiveController
{
    const MESSAGE_KOMBINASI = 'Kombinasi Instalasi/Ruangan/Kelas Pelayanan yang dipilih sudah ada. Silakan ubah inputan Anda terlebih dahulu.';
    const MESSAGE_NOMINAL = 'Terdapat perbedaan nominal plafon untuk instalasi dan kelas yang sama. Nonaktifkan plafon lama terlebih dahulu sebelum mengaktifkan yang baru.';

    public $modelClass = 'app\modules\v1\models\PlafonBpjs';
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["create"] = ["POST"];
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
            $model = new PlafonBpjsView;
            $query = $model->find();
            if (isset($_GET['advanced-filter']['list_ruangan_id'])) {
                $ruanganId = $_GET['advanced-filter']['list_ruangan_id'];
                if ($ruanganId === '' || $ruanganId === '- Semua -') {
                    unset($_GET['advanced-filter']['list_ruangan_id']);
                }
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query->asArray(),
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

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $types = (array)$request->get('type', []);
        $term = $request->get('term');
        $page = $request->get('page', 1);
        $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        $instalasiId = $request->get('instalasi_id');
        if($request->get('payload', [])) {
            $payload = $request->get('payload', []);
            $term = ArrayHelper::getValue($payload, 'term');
            $page = ArrayHelper::getValue($payload, 'page', 1);
            $limit = ArrayHelper::getValue($payload, 'limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        }
        $additionalPayload = $request->get('additionalPayload', []);
        if(isset($additionalPayload['instalasi_id'])) {
            $instalasiId = ArrayHelper::getValue($additionalPayload, 'instalasi_id');
        }
        $resultData = [];
        foreach ($types as $type) {
            $queryConfig = $this->buildFilterQuery($type, $term, $additionalPayload, $instalasiId);
            $resultData[$type] = $this->executeFilterQuery($queryConfig, $page, $limit);
        }

        return $resultData;
    }

    private function buildFilterQuery($type, $term, $additionalPayload, $instalasiId)
    {
        $query = null;
        $infinity = false;
        $docoConstantsId = new DocoConstansId;
        $instalasiRajal = $docoConstantsId->actionGetId('instalasi_rj');
        $instalasiIgd = $docoConstantsId->actionGetId('instalasi_rd');
        $instalasiFisio = $docoConstantsId->actionGetId('instalasi_fisio');

        switch ($type) {
            case 'instalasi':
                $query = Instalasi::find()
                    ->select(['instalasi_id AS id', 'instalasi_nama AS text'])
                    ->where(['is_active' => true])
                    ->andWhere(['IN', 'instalasi_id', [$instalasiRajal, $instalasiIgd, $instalasiFisio]]);

                if (!empty($term)) {
                    $query->andWhere(['like', 'LOWER(instalasi_nama)', $term]);
                }

                $query = $query->orderBy(['instalasi_nama' => SORT_ASC]);
                $infinity = true;
                break;

            case 'ruangan':
                $query = Ruangan::find()
                    ->select(['instalasi_id', 'ruangan_id AS id', 'ruangan_nama AS text'])
                    ->where(['is_active' => true]);

                if (isset($additionalPayload['instalasi_id']) && !empty($additionalPayload['instalasi_id'])) {
                    $query->andWhere(['instalasi_id' => $additionalPayload['instalasi_id']]);
                }

                if (!empty($term)) {
                    $query->andWhere(['like', 'LOWER(ruangan_nama)', $term]);
                }

                $query = $query->orderBy(['ruangan_nama' => SORT_ASC]);
                // $infinity = true;
                break;
            case 'ruangan_nama':
                $query = Ruangan::find()
                    ->select(['instalasi_id', 'ruangan_nama AS id', 'ruangan_nama AS text'])
                    ->where(['is_active' => true]);

                if ($instalasiId) {
                    $query->andWhere(['instalasi_id' => $instalasiId]);
                }

                if (!empty($term)) {
                    $query->andWhere(['like', 'LOWER(ruangan_nama)', $term]);
                }

                $query = $query->orderBy(['ruangan_nama' => SORT_ASC]);
                $infinity = true;
                break;
            case 'kelas':
                $query = KelasPelayanan::find()
                    ->select(['kelaspelayanan_id AS id', 'kelaspelayanan_nama AS text'])
                    ->where(['is_active' => true]);

                if (!empty($term)) {
                    $query->andWhere(['like', 'LOWER(kelaspelayanan_nama)', $term]);
                }

                $query = $query->orderBy(['kelaspelayanan_nama' => SORT_ASC]);
                $infinity = true;
                break;

            default:
                break;
        }

        return [
            'query' => $query,
            'infinity' => $infinity,
        ];
    }

    private function executeFilterQuery($config, $page, $limit)
    {
        $query = $config['query'];
        if ($query === null) {
            return [];
        }

        if (!empty($config['infinity'])) {
            $query->limit($limit + 1)->offset($limit * ($page - 1));
        }

        return $query->asArray()->all();
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $response = [];
        $hasConflictNominal = false;

        try {
            $ruanganId = ArrayHelper::getValue($post, 'list_ruangan_id');
            $instalasiId = ArrayHelper::getValue($post, 'instalasi_id');
            $kelasPelayananId = ArrayHelper::getValue($post, 'kelaspelayanan_id');
            $plafonBpjsId = ArrayHelper::getValue($post, 'plafonbpjs_id');
            if($plafonBpjsId) {
                $model = $this->findPlafonModel($plafonBpjsId);
                if (!$model) {
                    $response = $this->errorResult('Data Plafon tidak ditemukan.');
                }
                $instalasiId = $model->instalasi_id;
                $kelasPelayananId = $model->kelaspelayanan_id;
                $plafonFromPost = ArrayHelper::getValue($post, 'plafon', 0);

                $hasConflictNominal = $this->hasConflictNominal($instalasiId, $kelasPelayananId, $plafonBpjsId, $plafonFromPost);
                if($hasConflictNominal) {
                    $response = $this->errorResult(self::MESSAGE_NOMINAL);
                }
            }

            $hasConflict = $this->hasConflict($instalasiId, $kelasPelayananId, $ruanganId, $plafonBpjsId);
            if($hasConflict) {
                $response = $this->errorResult(self::MESSAGE_KOMBINASI);
            }

            if(!$hasConflictNominal && !$hasConflict) {
                $response = $this->handleInsert($post);
            }
            
            return $response;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        try {
            $model = PlafonBpjsView::find()
                ->where(['plafonbpjs_id' => $id])
                ->one();
            
            if ($model) {
                $results = ['data' => $model];
            } else {
                $results = ['message' => 'Data Tidak Ditemukan'];
            }
            return $results;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionChangeStatus()
    {
        $request = Yii::$app->request;
        $isActive = (bool) $request->get('is_active', true);
        $id = $request->get('plafonbpjs_id');
        $response = [];

        try {
            $model = PlafonBpjs::findOne($id);
            if (empty($model)) {
                $response = $this->errorResult('Data Plafon tidak ditemukan.');
            } else {
                $instalasiId = $model->instalasi_id;
                $kelasPelayananId = $model->kelaspelayanan_id;
                $ruanganIds = $model->list_ruangan_id;
                $hasConflict = $this->hasActiveConflict($instalasiId, $kelasPelayananId, $ruanganIds, $id, $isActive);
                if(isset($hasConflict['isValid']) && !$hasConflict['isValid']) {
                    $type = ArrayHelper::getValue($hasConflict, 'type');
                    $message = $type == 'ruangan' ? self::MESSAGE_KOMBINASI : self::MESSAGE_NOMINAL;
                    $response = $this->errorResult($message);
                }
                else {
                    $model->is_active = $isActive;
                    $model->save();
                    $response = ['message' => 'Data berhasil di Update.'];
                }
            }
        } catch (\Throwable $e) {
            Yii::$app->response->statusCode = 500;
            $response = ['message' => $e->getMessage()];
        }

        return $response;
    }

    private function hasActiveConflict($instalasiId, $kelasPelayananId, $ruanganIds, $id, $isActive)
    {
        if (!$isActive) {
            return false;
        }

        $currentPlafon = PlafonBpjs::findOne($id);
        $currentPlafon = ArrayHelper::getValue($currentPlafon, 'plafon', 0);
        $result = PlafonBpjs::find()
            ->where([
                'instalasi_id' => $instalasiId,
                'kelaspelayanan_id' => $kelasPelayananId,
                'is_active' => true,
            ])
            ->andWhere(['<>', 'plafonbpjs_id', $id])
            ->all();

        $isValid = true;
        $type = 'ruangan';
        foreach ($result as $row) {
            $plafon = ArrayHelper::getValue($row, 'plafon', 0);
            $listRuanganIds = $this->parseListRuangan($row->list_ruangan_id);
            $hasRuangan = $this->ruanganMatch($ruanganIds, $listRuanganIds);
            if($hasRuangan) {
                $isValid = false;
            }

            if($currentPlafon != $plafon) {
                $isValid = false;
                $type = 'nominal';
            }
        }
        
        return ['isValid' => $isValid, 'type' => $type];
    }

    public function actionListRuangan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $parent_label = $get['parent_label'];
        return Ruangan::find()
            ->select(['ruangan_id', 'ruangan_nama'])
            ->where(['is_active' => true, 'instalasi_id' => $parent_label])
            ->asArray()->all();
    }

    private function findPlafonModel($id)
    {
        return PlafonBpjsView::find()
            ->where(['plafonbpjs_id' => $id])
            ->one();
    }

    private function hasConflict($instalasiId, $kelasPelayananId, $ruanganId, $plafonBpjsId)
    {
        $results = PlafonBpjs::find()
            ->where([
                'instalasi_id' => $instalasiId,
                'kelaspelayanan_id' => $kelasPelayananId,
                'is_active' => true,
            ]);

            if($ruanganId) {
                $ruanganId = implode("','", array_map('trim', $ruanganId));
                $results = $results->andWhere(
                    new \yii\db\Expression(
                        "EXISTS (SELECT 1 FROM unnest(string_to_array(CAST(list_ruangan_id AS TEXT), ',')) AS id WHERE trim(id) IN ('{$ruanganId}'))"
                    )
                );
            }

            if($plafonBpjsId) {
                $results = $results->andWhere(['NOT IN', 'plafonbpjs_id', $plafonBpjsId]);
            }

            $results = $results->exists();

        return $results;
    }

    private function handleInsert($post)
    {
        $result = [];
        $plafonBpjsId = ArrayHelper::getValue($post, 'plafonbpjs_id');
        if(!$plafonBpjsId) {
            unset($post['plafonbpjs_id']);
        }
        
        $model = $plafonBpjsId ? PlafonBpjs::findOne($plafonBpjsId) : new PlafonBpjs;
        $model->attributes = $post;
        if(!empty($model->list_ruangan_id)) {
            $model->list_ruangan_id = implode(',', $model->list_ruangan_id);
        }
        $model->additional_data = json_encode([
            'plafon_ruangan' => ArrayHelper::getValue($post, 'plafon_ruangan'),
        ]);

        if($model->validate() && $model->save()) {
            $result = ['text' => 'Data Berhasil di Simpan.'];
        }
        else {
            $errors = DocoHelpers::parseError($model->errors, 'PlafonBpjsForm');
            $result = DocoHelpers::response([
              'status' => 422,
              'title' => 'Proses Gagal!',
              'data' => $errors
            ]);
        }

        return $result;
    }

    private function errorResult($message)
    {
        return [
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => $message
        ];
    }

    public function actionGetDataDetail()
    {
        $request = Yii::$app->request;
        $plafonBpjsId = $request->get('plafonbpjs_id');
        $plafon = PlafonBpjsView::find()->where(['plafonbpjs_id' => $plafonBpjsId])->one();

        $ruanganIds = [];
        if ($plafon && !empty($plafon->arr_ruangan_id)) {
            $ruanganIds = $plafon->arr_ruangan_id;
        }

        $modelRuangan = new Ruangan;
        $query = $modelRuangan->find()
            ->select(['ruangan_id', 'ruangan_nama'])
            ->where(['ruangan_id' => $ruanganIds]);

        if (isset($_GET['advanced-filter']['ruangan_nama'])) {
            $query->andFilterWhere(['ilike', 'ruangan_nama', $_GET['advanced-filter']['ruangan_nama']]);
            unset($_GET['advanced-filter']['ruangan_nama']);
        }
        
        return new ActiveDataProvider([
            'query' => $query->asArray(),
        ]);
    }

    public function actionGetDefaultPlafon()
    {
        $request = Yii::$app->request;
        $instalasiId = $request->get('instalasi_id');
        $kelasPelayananId = $request->get('kelaspelayanan_id');
        $data = PlafonBpjs::find()
            ->select(['plafon'])
            ->where([
                'instalasi_id' => $instalasiId,
                'kelaspelayanan_id' => $kelasPelayananId,
                'is_active' => true
        ])->one();

        return ArrayHelper::getValue($data, 'plafon', 0);
    }

    private function parseListRuangan($listRuanganId)
    {
        if (empty($listRuanganId)) {
            return [];
        }

        return array_map('trim', explode(',', $listRuanganId));
    }

    private function ruanganMatch($ruanganId, $listRuanganIds)
    {
        if (empty($ruanganId) || empty($listRuanganIds)) {
            return false;
        }

        return in_array($ruanganId, $listRuanganIds);
    }

    private function hasConflictNominal($instalasiId, $kelasPelayananId, $plafonBpjsId, $currentPlafon)
    {
        $isValid = false;
        $results = PlafonBpjs::find()
            ->where([
                'instalasi_id' => $instalasiId,
                'kelaspelayanan_id' => $kelasPelayananId,
                'is_active' => true,
            ])
            ->andWhere(['<>', 'plafonbpjs_id', $plafonBpjsId])
            ->all();

        foreach ($results as $row) {
            $plafon = ArrayHelper::getValue($row, 'plafon', 0);
            if($currentPlafon != $plafon) {
                $isValid = true;
            }
        }

        return $isValid;
    }
}


