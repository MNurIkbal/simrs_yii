<?php

/**
 * @author: arief saputra
 * @description: master untuk CRUD Layar Antrian
 **/

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use Doco\components\DocoPrint;
use app\modules\v1\models\TindakanRuangan;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PaketRuanganMP;
use app\modules\v1\models\PaketRuanganV;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\DaftarTindakanV;
use app\modules\v1\models\KategoriTindakan;
use app\modules\v1\models\KelompokTindakan;
use app\modules\v1\models\Jeniskegiatantindakan;
use app\modules\v1\models\TindakanRuanganView;
use Doco\components\DocoHelpers;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

class TindakanRuanganController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TindakanRuangan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["delete"] = ["POST", "DELETE"];
        $verbs["list-layarantrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        // unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;

            $get = $request->post();

            // $model = new TindakanRuanganView;
            // $query = $model::find()->select(['ruangan_id', 'ruangan_nama','is_deleted','is_active'])->where(['is_deleted'=>false, 'is_active'=>true])->groupBy(['ruangan_id', 'ruangan_nama','is_deleted','is_active']);
            $model = new Ruangan;
            $query = $model::find()->select(['ruangan_id', 'ruangan_nama', 'is_deleted', 'is_active'])->where(['is_deleted' => false, 'is_active' => true])->groupBy(['ruangan_id', 'ruangan_nama', 'is_deleted', 'is_active']);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
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

    public function actionView()
    {
        $model = new TindakanRuanganView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionViewDetail($id)
    {
        $model = new TindakanRuanganView;
        $query = $model::find();
        $query->andWhere(['ruangan_id' => $id]);
        $query->orderBy(['created_date' => SORT_ASC]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
    }

    private function getData($id = null)
    {
        $returnData = (new \yii\db\Query())
            ->select([
                't.ruangan_id',
                't.daftartindakan_id',
                't.additional_data',
                't.is_active',
                't.is_deleted',
                't2.ruangan_nama',
                't3.daftartindakan_kode',
                't3.daftartindakan_nama',
                't3.daftartindakan_namalainnya',
                't4.kelompoktindakan_nama',
                't5.jeniskegiatantindakan_nama',
                't6.kategoritindakan_nama',
            ])->from('tindakanruangan_mp t')
            ->join('LEFT JOIN', 'ruangan_m t2', 't2.ruangan_id = t.ruangan_id')
            ->join('LEFT JOIN', 'daftartindakan_m t3', 't3.daftartindakan_id = t.daftartindakan_id')
            ->join('LEFT JOIN', 'kelompoktindakan_m t4', 't4.kelompoktindakan_id = t3.kelompoktindakan_id')
            ->join('LEFT JOIN', 'jeniskegiatantindakan_m t5', 't5.jeniskegiatantindakan_id = t3.jeniskegiatantindakan_id')
            ->join('LEFT JOIN', 'kategoritindakan_m t6', 't6.kategoritindakan_id = t3.kategoritindakan_id');
        // ->orderBy([ 't.daftartindakan_id' => SORT_ASC ]);

        if ($id) {
            $returnData->where(['t.layarantrian_id' => $id]);
        }

        return $returnData;
    }


    protected $_title = "Data Master Tindakan Ruangan";
    public function actionExportExcel()
    {
        $model = new TindakanRuanganView;
        $header = $footer = [];
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['ruangan_nama'])) {
                $header['Nama Ruangan'] = $_GET['advanced-filter']['ruangan_nama'];
            }
        }
        $query = $model::find()->where(['is_deleted' => false, 'is_active' => true]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['daftartindakan_kode' => SORT_ASC]);
        $data = $query->asArray()->all();
        $ruangan_nama = '';
        foreach ($data as $key => $value) {
            $newValue = [];
            if (isset($_GET['advanced-filter'])) {
                $ruangan_nama = ' ' . $value['ruangan_nama'];
            }
            $newValue[\Yii::t('app', 'Nama Ruangan')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Kode Tindakan')] = $value['daftartindakan_kode'];
            $newValue[\Yii::t('app', 'Nama Tindakan')] = $value['daftartindakan_nama'];
            $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['daftartindakan_namalainnya'];
            $newValue[\Yii::t('app', 'Kategori')] = $value['kategoritindakan_nama'];
            $newValue[\Yii::t('app', 'Kelompok')] = $value['kelompoktindakan_nama'];
            $newValue[\Yii::t('app', 'Kegiatan')] = $value['jeniskegiatantindakan_nama'];
            $newValue[\Yii::t('app', 'Group INA CBGS')] = $value['groupinacbg_nama'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($this->_title . $ruangan_nama, $result, $header, [
            "subTitle" => "Tanggal " . date('d-M-Y'),
        ], $footer, [], true);
        $filePath->save('php://output');
        die();
    }

    /**
     * @controller actionExportPdf
     * @attribute #datatable# => Untuk mengganti data di table
     * @attribute #username# => Nama user yang cetak dokumen
     * @attribute #tgl_skrg# => Tanggal hari ini
     * @attribute #ruangan# => Ruangan filter
     * @attribute #tgl_cetak# => Tanggal cetak dokumen
     */
    public function actionExportPdf()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '1800');
        ini_set("pcre.backtrack_limit", "1000000");
        $request = Yii::$app->request;
        $title = 'Data Master Tindakan Ruangan';
        // if ($request->get()) {
        $get = $request->get();
        $model = new TindakanRuanganView;
        $query = $model::find()->where(['is_deleted' => false, 'is_active' => true]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['daftartindakan_kode' => SORT_ASC]);
        $data = $query->asArray()->all();
        $ruangan_nama = '';
        if (isset($_GET['advanced-filter'])) {
            $ruangan_nama = isset($data[0]['ruangan_nama']) ? $data[0]['ruangan_nama'] : '';
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $data,
                'title' => $title,
            ]),
            '#username#' => Yii::$app->jwt->user->nama_pemakai,
            '#tgl_skrg#' => date('d-M-Y'),
            '#tgl_cetak#' => date('d-M-Y H:i:s'),
            '#ruangan#' => strtoupper($ruangan_nama)
        ];
        // return $print->attributes;
        $print->Output();
        // }
        // \Yii::$app->response->statusCode = 500;
        // return ['message' => 'Tidak Ada Data yang harus di cetak'];
    }

    public function actionGenerateApi()
    {

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()->where(['is_deleted' => false, 'is_active' => true]);
        $queryRuangan = $queryRuangan->asArray()->all();

        return [
            'ruangan' => $queryRuangan,
        ];
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new TindakanRuangan;
            if ($request->post()) {
                $post = $request->post();
                $model->attributes = $post;
                if ($model->validate()) {
                    if ($model->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    }
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        }
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query  = "UPDATE tindakanruangan_mp set is_deleted = true, deleted_by='" . Yii::$app->jwt->user->pegawai_id . "', deleted_date='" . date('Y-m-d H:i:s') . "' where ruangan_id = '{$get['ruangan_id']}' and daftartindakan_id = '{$get['daftartindakan_id']}'";
        // $query = TindakanRuangan::deleteMapping($get['ruangan_id'], $get['daftartindakan_id']);
        $update = Yii::$app->db->createCommand($query)->execute();
        return true;
        // return $query->delete();
    }

    public function actionUpdateDefault()
    {
        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();

                $model = TindakanRuangan::find()->where([
                    'daftartindakan_id' => $post['daftartindakan_id'],
                    'ruangan_id' => $post['ruangan_id']
                ])->one();

                $is_default = $model->is_default ? 'f' : 't';

                $query  = "UPDATE tindakanruangan_mp set is_default = '{$is_default}' where daftartindakan_id = '{$post['daftartindakan_id']}' and ruangan_id = '{$post['ruangan_id']}'";
                $update = Yii::$app->db->createCommand($query)->execute();
                if ($update) {
                    return ['message' => 'Data Berhasil di simpan'];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        }
    }

    public function actionSalinTindakanRuangan()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                // $post = json_decode($post['data'], true);
                $queryCheck = TindakanRuangan::find()->where(['ruangan_id' => $post['ruangan_tujuan'], 'is_deleted' => 'false', 'is_active' => 'true'])->asArray()->all();
                if (count($queryCheck) > 0) {
                    throw new \Exception("Salin ruangan tidak bisa dilakukan, Ruangan tujuan sudah memiliki tindakan", 1);
                }
                $queryGet = "SELECT * FROM tindakanruangan_mp where ruangan_id = {$post['ruangan_asal']} and is_deleted=false";
                $getSource = $connection->createCommand($queryGet)->queryAll();
                $query  = "UPDATE tindakanruangan_mp set is_deleted = true, deleted_by='" . Yii::$app->jwt->user->pegawai_id . "', deleted_date='" . date('Y-m-d H:i:s') . "' where ruangan_id = '{$post['ruangan_tujuan']}'";
                $deleteOld = $connection->createCommand($query)->execute();
                $sourceData = [];
                foreach ($getSource as $key => $value) {
                    $newData = [];
                    $newData = $value;
                    $newData['ruangan_id'] = $post['ruangan_tujuan'];
                    $sourceData[] = $newData;
                }
                $saveAll = TindakanRuangan::batchInsert($sourceData, false);
                if (!$saveAll) {
                    return $saveAll->getErrors();
                    throw new \Exception("terjadi kesalahan");
                }
                $transaction->commit();
                return true;
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'status' => 422,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 422,
                'text' => $e->getMessage(),
                'title' => 'Proses Gagal'
            ];
        }
    }
}
