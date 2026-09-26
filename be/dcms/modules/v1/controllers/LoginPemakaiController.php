<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\Instalasi;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use Doco\models\Loginpemakai;
use Doco\models\Pegawai;
use Doco\models\RuanganPemakai;
use Yii;
use yii\data\ActiveDataProvider;

class LoginPemakaiController extends DocoActiveController
{
    public $modelClass = Loginpemakai::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-data"] = ["GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new Loginpemakai;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetData($id = null)
    {
        $query = Instalasi::find()->select([
            'instalasi_m.instalasi_id',
            'instalasi_m.instalasi_nama',
            'instalasi_m.instalasi_namalainnya',
        ])->joinWith([
            'ruangan' => function ($query) {
                $query->select([
                    'ruangan_m.ruangan_id',
                    'ruangan_m.instalasi_id',
                    'ruangan_m.instalasi_id',
                    'ruangan_m.ruangan_nama',
                    'ruangan_m.ruangan_namalainnya',
                    'ruangan_m.ruangan_singkatan',
                ])->where([ 'ruangan_m.is_active' => true ]);
            },
        ])->asArray()->all();

        if ($id) {
            $dataPemakai = Loginpemakai::find()
                ->select([
                    'loginpemakai_k.loginpemakai_id',
                    'loginpemakai_k.pegawai_id',
                    'loginpemakai_k.pasien_id',
                    'loginpemakai_k.nama_pemakai',
                    'concat(pegawai_m.nomorindukpegawai,\' - \',pegawai_m.nama_pegawai) as pegawai_nama',
                ])
                ->joinWith([
                    'ruangPemakai' => function ($query) {
                        $query->select([
                            'ruanganpemakai_k.ruangan_id',
                            'ruanganpemakai_k.loginpemakai_id',
                        ]);
                    },
                    'pegawai',
                ])
                ->where([
                    'loginpemakai_k.loginpemakai_id' => $id,
                ])->asArray()->one();
            return [
                'data_login' => $dataPemakai,
                'data_instalasi' => $query,
            ];
        }

        return $query;
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Loginpemakai;
        if ($post = $request->post()) {
            $existingEmployee = Loginpemakai::find()->andFilterWhere(['or',
                ['pegawai_id' => $post['pegawai_id']],
                ['nama_pemakai' => $post['nama_pemakai']],
            ])->select(['loginpemakai_id', 'nama_pemakai'])->one();
            $model->attributes = $post;
            if (!empty($existingEmployee)) {
                return [
                    'message' => 'Nama Pengguna atau Pegawai yang diinput telah ada.',
                    'status' => 400,
                ];
            } else if (empty($existingEmployee)) {
                if ($model->save()) {
                    if ($instalasi = $request->post('instalasi', [])) {
                        $ruanganPemakai = [];
                        $jwt = Yii::$app->jwt;
                        $dataDefault = [
                            'ruangan_id' => '',
                            'loginpemakai_id' => $model->loginpemakai_id,
                            'created_date' => date('Y-m-d H:i:s'),
                            'created_by' => !empty($jwt->user->loginpemakai_id) ? $jwt->user->loginpemakai_id : null,
                        ];
                        foreach ($instalasi as $key => $value) {
                            $id_ruangan = DocoHelpers::decrypt($value);
                            $dataDefault['ruangan_id'] = (int) $id_ruangan;
                            $ruanganPemakai[] = $dataDefault;
                        }
                        RuanganPemakai::batchInsert($ruanganPemakai);
                    }
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422,
                    ];
                }
            }
        }
    }

    public function actionUpdate($id)
    {
        $model = Loginpemakai::find()->where(['loginpemakai_id' => $id])->one();
        $payload = Yii::$app->request->post();
        if (!empty($model)) {
            $model->attributes = $payload;
            // Checking if related nama pemakai or pegawai is already exist
            try {
                $existingEmployee = Loginpemakai::find()
                    ->where(["!=", 'loginpemakai_id', $id])
                    ->andFilterWhere(['or',
                        ['pegawai_id' => $payload['pegawai_id']],
                        ['nama_pemakai' => $payload['nama_pemakai']],
                    ])
                    ->select(['loginpemakai_id'])
                    ->asArray()
                    ->one();
                if (empty($existingEmployee)) {
                    if ($model->save()) {
                        $instalasi = isset($payload['instalasi']) ? $payload['instalasi'] : [];
                        if (!empty($instalasi)) {
                            $ruanganPemakai = [];
                            $jwt = Yii::$app->jwt;

                            $dataDefault = [
                                'ruangan_id' => '',
                                'loginpemakai_id' => $model->loginpemakai_id,
                                'created_date' => date('Y-m-d H:i:s'),
                                'created_by' => !empty($jwt->user->loginpemakai_id) ? $jwt->user->loginpemakai_id : null,
                            ];

                            foreach ($instalasi as $key => $value) {
                                $id_ruangan = DocoHelpers::decrypt($value);
                                $dataDefault['ruangan_id'] = (int) $id_ruangan;
                                $ruanganPemakai[] = $dataDefault;
                            }
                            $delete = (new RuanganPemakai)->delete(['loginpemakai_id' => $model->loginpemakai_id]);
                            RuanganPemakai::batchInsert($ruanganPemakai);
                        }
                        return ['message' => 'Data Berhasil di simpan'];
                    } else {
                        return [
                            'data' => $model->errors,
                            'status' => 422,
                        ];
                    }
                } else {
                    return [
                        'message' => 'Nama Pengguna atau Pegawai yang diinput telah ada.',
                        'status' => 400,
                    ];
                }

            } catch (\Exception $e) {
                \Yii::$app->response->statusCode = 500;
                Yii::error([
                    'Message' => $e->getMessage(),
                    'Line' => $e->getLine(),
                    'File' => $e->getFile(),
                ]);
                return [
                    'message' => 'Terjadi kesalahan pada server, silakan coba beberapa saat lagi.',
                ];
            }
        } else {
            return [
                'message' => 'Data login pemakai tidak ditemukan.',
                'status' => 400,
            ];
        }
    }

    public function actionDelete($id)
    {
        try {
            $result = (new Loginpemakai)->delete($id);
            $delete = (new RuanganPemakai)->delete(['loginpemakai_id' => $id]);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionAutocompletePegawai()
    {
        $request = Yii::$app->request;
        $search = $request->get('q');
        $query = Pegawai::find()->select([
            'pegawai_id',
            'nomorindukpegawai',
            'nama_pegawai',
        ])->andFilterWhere(['or',
            ['ILIKE', 'nama_pegawai', $search],
            ['ILIKE', 'nomorindukpegawai', $search],
        ])->all();
        $result = [];

        foreach ($query as $value) {
            $result[] = [
                'id' => $value->pegawai_id,
                'value' => $value->nomorindukpegawai . ' - ' . $value->nama_pegawai,
            ];
        }

        return [
            'data' => $result,
        ];
    }

    public function actionGetPegawai()
    {
        $model = new Pegawai;
        $query = $model::find();
        $query->select([
            'pegawai_id',
            'nomorindukpegawai',
            'nama_pegawai',
            'tgl_lahirpegawai',
            'alamat_pegawai',
        ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
