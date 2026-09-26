<?php
namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\components\DocoHelpers;
use yii\web\HttpException;
use Doco\models\Loginpemakai;

class ModulController extends \Doco\components\DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\Modul';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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
        unset($actions['get-menu']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            $status = $request->post('is_active');

            $status = $status ? true : false;
            $result->andWhere(['is_active' => $status]);

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionCreate()
    {
        throw new HttpException(404, 'The requested Item could not be found.');
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Modul::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete($id)
    {
        throw new HttpException(404, 'The requested Item could not be found.');
    }

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    public function actionGetMenu()
    {

        $loginPemakai = Loginpemakai::find()->select([
            'loginpemakai_k.loginpemakai_id',
            'loginpemakai_k.nama_pemakai'
        ])->joinWith([
            'ruangPemakai' => function ($query) {
                $query->select([
                    'ruanganpemakai_k.ruangan_id',
                    'ruanganpemakai_k.loginpemakai_id',
                ])->joinWith([
                    'ruangan' => function ($query) {
                        $query->select([
                            'ruangan_m.ruangan_id',
                            'ruangan_m.instalasi_id',
                            'ruangan_m.ruangan_nama',
                            'ruangan_m.ruangan_namalainnya',
                        ]);
                    }
                ]);
            }
        ])->one();

        $rooms = $modul_pemakai = [];
        if (!empty($loginPemakai->ruangPemakai)) {
            foreach ($loginPemakai->ruangPemakai as $value) {
                if (!empty($value->ruangan)) {
                    $rooms[$value->ruangan->instalasi_id][] = [
                        'id' => $value->ruangan->ruangan_id,
                        'name' => $value->ruangan->ruangan_nama
                    ];
                }
            }
        }
        
        $modul = $this->getData()->joinWith([
                'modulInstalasi' => function ($query) {
                    $query->select([
                        'modulinstalasi_mp.modul_id',
                        'modulinstalasi_mp.instalasi_id',
                    ])->joinWith([
                        'instalasi' => function ($query) {
                            $query->select([
                                'instalasi_m.instalasi_id',
                                'instalasi_m.instalasi_nama',
                                'instalasi_m.instalasi_namalainnya',
                            ]);
                        }
                    ]);
                }
            ])->andWhere(['<>','modul_key','master'])
            ->orderBy(['modul_namalainnya' => SORT_ASC])->asArray()->all();

        return $modul;
    }

    private function getData($id = null)
    {
        $modul = Modul::find()
                        ->select([
                                'modul_k.modul_id',
                                'modul_k.modul_nama',
                                'modul_k.modul_namalainnya',
                                'modul_k.modul_fungsi',
                                'modul_k.url_modul',
                                'modul_k.icon_modul',
                                'modul_k.modul_key',
                                'modul_k.modul_urutan',
                                'modul_k.modul_kategori',
                                'modul_k.imagemodul',
                                'modul_k.is_active'
                        ]);
        if ($id) {
            $modul->where(['modul_id' => $id]);
        }

        return $modul;
    }
}