<?php

namespace app\modules\v1\controllers;

/**
 * @Author  : Sunarko
 * @Date    : 2018-07-03 10:33:32
 * @Last Modified by    :
 * @Last Modified time  :
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\NilaiRujukan;
use app\modules\v1\models\NilaiRujukanView;
use app\modules\v1\models\NilaiRujukanDetail;
use app\modules\v1\models\GolonganUmurLab;
use app\modules\v1\models\GolonganUmur;
use app\modules\v1\models\Lookup;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;


class NilaiRujukanController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\NilaiRujukan';
    protected $_title = 'Nilai Rujukan Pemeriksaan Laboratorium';

    public function verbs()
    {
        $verbs = parent::verbs();
        //$verbs["index"] = ["POST", "GET"];
        //$verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['create']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new NilaiRujukanView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function getGolonganUmur()
    {
        $q = GolonganUmur::find();
        $golongan_data = $this->getOrSetCache(DocoConstants::VAR_CACHE_GOLONGANUMUR, $q, true);
        return $golongan_data;
    }

    public function actionDataGolongan()
    {
        $data = $this->getGolonganUmur();
        return $data;
    }


    public function actionDataPemeriksaanLab()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        $sql = "select pemeriksaanlab_nama from pemeriksaanlab_m where UPPER( pemeriksaanlab_nama ) LIKE '%{$term}%'
            group by pemeriksaanlab_nama
            order by pemeriksaanlab_nama asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionDataNewPemeriksaanLab()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        $query = NilaiRujukanView::find()->where(['LIKE', 'UPPER(nama_pemeriksaan)', $term])->all();
        
        // $sql = "select pemeriksaanlab_nama from pemeriksaanlab_m where UPPER( pemeriksaanlab_nama ) LIKE '%{$term}%'
        //     group by pemeriksaanlab_nama
        //     order by pemeriksaanlab_nama asc limit 50
        // "; 
        // $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $query;
    }

    public function actionDataKelompokPeriksa()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        $sql = "select nama_kelompok from kelompokpemeriksaanlab_m where UPPER( nama_kelompok ) LIKE '%{$term}%'
            group by nama_kelompok
            order by nama_kelompok asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionUpdateParent($id)
    {
        $request = Yii::$app->request;
        try {
            $kelId = $request->post('k_id');
            $golId = $request->post('g_id');
            $value = $request->post('value');
            $field = $request->post('field');
            $model = NilaiRujukan::find()->where([
                'pemeriksaanlab_id' => $id,
                'nama_rujukan' => null,
                'jenis_kelamin' => $kelId,
                'golonganumur_id' => $golId
            ])->one();
            
            if (empty($model)) {
                $model = new NilaiRujukan;
            }
            $model->jenis_kelamin = $kelId;
            $model->pemeriksaanlab_id = $id;
            $model->golonganumur_id = $golId;
            switch ($field) {
                case 'nilai_min':
                    $model->nilai_min = $value;
                    break;
                case 'nilai_max':
                    $model->nilai_max = $value;
                    break;
                case 'satuan_hasillab':
                    $model->satuan_hasillab = $value;
                    break;
                case 'nilaikritis_min':
                    $model->nilaikritis_min = $value;
                    break;
                case 'nilaikritis_max':
                    $model->nilaikritis_max = $value;
                    break;
                case 'keterangan':
                    $model->keterangan = $value;
                    break;
                default:
                    # code...
                    break;
            }
            if (($model->validate()) && ($model->save(false))) {
                $result = [
                    'message' => 'Data berhasil simpan'
                ];
            }else{
                $result['status'] = 422;
                // $result['data'] = $model->errors;
                $result['text'] = 'Data Yang Diinput Tidak Valid!';
                $result['title'] = 'Proses Gagal!';
            }
            return $result;
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getDetail($id = null, $nama = null)
    {
        $model = new NilaiRujukanView;
        $query = $model::find()->where(['pemeriksaanlab_id' => $id])->asArray()->one();

        if($nama) {
            $query = NilaiRujukan::find()->where(['pemeriksaanlab_id' => $id, 'nama_rujukan' => $nama])->asArray()->all();
        }

        return $query;
    }

    public function getDetailNilaiRujukan($id)
    {
        $model = new NilaiRujukan;
        $query = $model::find()->where(['pemeriksaanlab_id' => $id])->asArray()->all();

        return $query;
    }

    public function getGolonganUmurLab()
    {
        $query = new GolonganUmurLab;
        $query = $query::find()->orderBy('gol_umurlab_nama')->all();

        return $query;
    }

    public function getJenisKelamin()
    {
        $query = new Lookup;
        $query = $query::find()->where(['lookup_type' => 'jenis_kelamin'])/*->orderBy('lookup_name')*/->all();

        return $query;
    }

    public function actionGetRequest($id = null, $nama = null)
    {
        $nama = DocoHelpers::decrypt($nama);
        $detail = [];
        if($id) {
            $detail = $this->getDetail($id);
        }

        if($nama) {
            $detail = $this->getDetail($id, $nama);
        }
        
        $golonganUmur = $this->getGolonganUmurLab();
        $jenisKelamin = $this->getJenisKelamin();
        $arrRujukan = [];
        $valueParent = [];
        $nama_rujukan = NilaiRujukan::find()
            ->joinWith(['golonganUmurLab'])
            ->where(['pemeriksaanlab_id' => $id, 'nilairujukan_m.is_active' => true])
            ->orderBy([
                'golonganumurlab_m.gol_umurlab_nama' => SORT_ASC
            ])
            ->all();

        $is_active = NilaiRujukan::find()->select('is_active')->where(['pemeriksaanlab_id' => $id, 'is_deleted' => false])->distinct()->one();

        foreach ($nama_rujukan as $key => $value) {
            if(!empty($value['nama_rujukan'])) {
                $arrRujukan[$value['nama_rujukan']][$value['jenis_kelamin']][] = $value;
            }
            else {
                $valueParent[$value['jenis_kelamin']][$value['golonganumur_id']] = $value; 
            }
        }

        return [
            'detail' => $detail,
            'golongan_umur' => $golonganUmur,
            'jenis_kelamin' => $jenisKelamin,
            'nilai_rujukan' => $arrRujukan,
            'nama_rujukan' => $nama_rujukan ? $nama_rujukan : '',
            'pemeriksaanlab_id' => $id,
            'nama' => $nama,
            'is_active' => $is_active['is_active'],
            'valueParent' => $valueParent
        ];
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = NilaiRujukan::find()->where(['pemeriksaanlab_id' => $post['pemeriksaanlab_id']])->all();

            $jenis_kelamin = $this->getJenisKelamin();
            $golongan_umur = $this->getGolonganUmurLab();
            
            if ($post) {
                $insert = [];
                if($model) {
                    // $insert = new NilaiRujukan;
                    // $insert->pemeriksaanlab_id = $post['pemeriksaanlab_id'];
                    // $insert->jenis_kelamin = $post['jenis_kelamin'];
                    // $insert->golonganumur_id = $post['golonganumur_id'];

                    // if(isset($post['nilai_min'])) {
                    //     $insert->nilai_min = $post['nilai_min'];
                    // }
                    // elseif(isset($post['nilai_max'])) {
                    //     $insert->nilai_max = $post['nilai_max'];
                    // }
                    // elseif(isset($post['satuan_hasillab'])) {
                    //     $insert->satuan_hasillab = $post['satuan_hasillab'];
                    // }
                    // elseif(isset($post['nilaikritis_min'])) {
                    //     $insert->nilaikritis_min = $post['nilaikritis_min'];
                    // }
                    // elseif(isset($post['nilaikritis_max'])) {
                    //     $insert->nilaikritis_max = $post['nilaikritis_max'];
                    // }
                    // elseif(isset($post['keterangan'])) {
                    //     $insert->keterangan = $post['keterangan'];
                    // }

                    // $insert->nilai_rujukan = $insert->nilai_min.'-'.$insert->nilai_max;
                    // $insert->save(false);
                    foreach ($model as $key => $value) {
                        $update = NilaiRujukan::find()->where([
                            'nilairujukan_id' => $value['nilairujukan_id'],
                        ])
                        ->one();
                        if (empty($value['nama_rujukan'])) {
                            $insert[$value['jenis_kelamin']][$value['golonganumur_id']] = true;
                            $update->nilai_min = isset($post['parent']['nilai_min'][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['parent']['nilai_min'][$value['jenis_kelamin']][$value['golonganumur_id']] : null;
                            $update->nilai_max = isset($post['parent']['nilai_max'][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['parent']['nilai_max'][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->satuan_hasillab = isset($post['parent']['satuan_hasillab'][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['parent']['satuan_hasillab'][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->nilaikritis_min = isset($post['parent']['nilaikritis_min'][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['parent']['nilaikritis_min'][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->nilaikritis_max = isset($post['parent']['nilaikritis_max'][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['parent']['nilaikritis_max'][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->keterangan = isset($post['parent']['keterangan'][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['parent']['keterangan'][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->nilai_rujukan = $update->nilai_min.' - '.$update->nilai_max;
                            $update->save(false);
                            continue;

                        }

                        if($update) {
                            $update->nilai_min = isset($post['nilai_min'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['nilai_min'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] : null;
                            $update->nilai_max = isset($post['nilai_max'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['nilai_max'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->satuan_hasillab = isset($post['satuan_hasillab'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['satuan_hasillab'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->nilaikritis_min = isset($post['nilaikritis_min'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['nilaikritis_min'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->nilaikritis_max = isset($post['nilaikritis_max'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['nilaikritis_max'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->keterangan = isset($post['keterangan'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] )
                                            ? $post['keterangan'][$value['nilairujukan_id']][$value['jenis_kelamin']][$value['golonganumur_id']] : null;

                            $update->nilai_rujukan = $update->nilai_min.' - '.$update->nilai_max;
                            $update->save(false);
                        }
                    }

                    $insertBatch = [];
                    foreach ($jenis_kelamin as $keyKelamin => $valueKelamin) {
                        foreach ($golongan_umur as $k => $v) {
                            if(!isset($insert[$valueKelamin['lookup_id']][$v['golonganumurlab_id']])) {
                                $insertBatch[] = [
                                    'pemeriksaanlab_id' => (int) $id,
                                    'jenis_kelamin' => (int) $valueKelamin['lookup_id'],
                                    'golonganumur_id' => (int) $v['golonganumurlab_id'],

                                    'nilai_min' => !empty($post['parent']['nilai_min'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']]) ? $post['parent']['nilai_min'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']] : null,

                                    'nilai_max' => !empty($post['parent']['nilai_max'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']]) ? $post['parent']['nilai_max'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']] : null,

                                    'satuan_hasillab' => !empty($post['parent']['satuan_hasillab'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']]) ? $post['parent']['satuan_hasillab'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']] : null,

                                    'nilaikritis_min' => !empty($post['parent']['nilaikritis_min'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']]) ? $post['parent']['nilaikritis_min'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']] : null,

                                    'nilaikritis_max' => !empty($post['parent']['nilaikritis_max'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']]) ? $post['parent']['nilaikritis_max'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']] : null,

                                    'keterangan' => !empty($post['parent']['keterangan'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']]) ? $post['parent']['keterangan'][$valueKelamin['lookup_id']][$v['golonganumurlab_id']] : null,
                                ];
                            }
                        }
                    }
                    if(count($insertBatch)) {
                        NilaiRujukan::batchInsert($insertBatch, false);
                    }

                    return ['message' => 'Data Berhasil di simpan'];
                }
            }
        } catch (\yii\db\Exception $e) {
            // \Yii::$app->response->statusCode = 500;
        
            return [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // \Yii::$app->response->statusCode = 500;
            return [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = new NilaiRujukan;
            if ($model->validate()) {
                $model->attributes = $request->post();
                
                $array_insert = [];
                $jk = [];
                $golongan_umur = [];

                foreach ($this->getGolonganUmurLab() as $key => $value) {
                    $golongan_umur[$value['golonganumurlab_id']] = $value['gol_umurlab_nama'];
                }

                foreach ($this->getJenisKelamin() as $key => $value) {
                    $jk[$value['lookup_id']] = [
                        'id' => $value['lookup_id'],
                        'label' => $value['lookup_name'],
                        'data' => $golongan_umur
                    ];
                }
                
                foreach ($jk as $keyJk => $valueJk) {
                    foreach ($valueJk['data'] as $k => $v) {
                        $array_insert[] = [
                            'pemeriksaanlab_id' => $model->pemeriksaanlab_id,
                            'nama_rujukan' => $model->nama_rujukan,
                            'jenis_kelamin' => $keyJk,
                            'golonganumur_id' => $k
                        ];
                    }
                }
                
                NilaiRujukan::batchInsert($array_insert, false);
                $transaction->commit();
                return ['message' => 'Data Berhasil di simpan'];
            }
            else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        }
    }

    public function actionUpdateHasil($id, $nama)
    {
        $nama = DocoHelpers::decrypt($nama);
        try {
            $request = Yii::$app->request;
            $model = NilaiRujukan::find()->where(['pemeriksaanlab_id' => $id, 'nama_rujukan' => $nama])->all();
            if ($request->post()) {
                $post = $request->post();
                if($model) {
                    foreach ($model as $key => $value) {
                       NilaiRujukan::updateAll(['nama_rujukan' => $post['nama_rujukan'], 'is_active' => $post['is_active']], 
                        ['pemeriksaanlab_id' => $id, 'nama_rujukan' => $nama]);
                    }

                    return ['message' => 'Data Berhasil di simpan'];
                }
            }
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

    public function actionDeleteHasil($id, $nama)
    {
        $nama = DocoHelpers::decrypt($nama);
        try {
            if ($result = NilaiRujukan::updateAll([
                'is_deleted' => true, 
                'deleted_date' => date('Y-m-d H:i:s')
            ], [
                'pemeriksaanlab_id' => $id, 
                'nama_rujukan' => $nama
            ])) {
                return [
                    "title" => "Proses Berhasil !",
                    "message" => "Data berhasil dihapus",
                ];
            }
            return [
                'status' => 422,
                'message' => 'Tidak dapat menghapus ' . $nama,
                "title" => "Proses Gagal !",
            ];
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdateForm()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = NilaiRujukan::findOne($post['nilairujukan_id']);
            if ($request->post()) {
                $post = $request->post();
                if($model) {
                    $model->attributes = $post;
                    // if(isset($post['nilai_min'])) {
                    //     $model->nilai_min = $post['nilai_min'];
                    // }
                    // elseif(isset($post['nilai_max'])) {
                    //     $model->nilai_max = $post['nilai_max'];
                    // }
                    // elseif(isset($post['satuan_hasillab'])) {
                    //     $model->satuan_hasillab = $post['satuan_hasillab'];
                    // }
                    // elseif(isset($post['nilaikritis_min'])) {
                    //     $model->nilaikritis_min = $post['nilaikritis_min'];
                    // }
                    // elseif(isset($post['nilaikritis_max'])) {
                    //     $model->nilaikritis_max = $post['nilaikritis_max'];
                    // }
                    // elseif(isset($post['keterangan'])) {
                    //     $model->keterangan = $post['keterangan'];
                    // }
                    // elseif(isset($post['nilai_rujukan'])) {
                    //     $model->nilai_rujukan = $post['nilai_rujukan'];
                    // }
                    $model->nilai_rujukan = $model->nilai_min .' - '.$model->nilai_max;
                    if (($model->validate()) && ($model->save())) {
                        $result = [
                            'message' => 'Data berhasil simpan'
                        ];
                    }else{
                        $result['status'] = 422;
                        // $result['data'] = $model->errors;
                        $result['text'] = 'Data Yang Diinput Tidak Valid!';
                        $result['title'] = 'Proses Gagal!';
                    }
                    return $result;
                }
            }
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

}
