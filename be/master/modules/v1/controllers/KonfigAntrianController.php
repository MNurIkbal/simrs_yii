<?php
/**
 * @author: arief saputra
 * @modified : ali.padilah@docotel.com
 * @description: master untuk CRUD Layar Antrian
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\base\Exception;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\KonfigAntrian;
use app\modules\v1\models\KonfigantrianV;
use app\modules\v1\models\Layarantrian;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\InfoJadwalDokterView;
use Doco\components\DocoHelpers;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use app\modules\v1\cache\Cache;
use Doco\components\DocoConstants;

class KonfigAntrianController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Konfigantrian';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["create"] = ["POST"];
        $verbs["get-attribute-options"] = ["GET"];
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
        $get = $request->get();

        $result = $this->getData();
        if(isset($_GET['advanced-filter'])) {
            $post = $_GET['advanced-filter'];
            if (!empty($post['jenis_antrian'])) {
                $result->andWhere(['jenisantrian_id' => $post['jenis_antrian']]);
                unset($_GET['advanced-filter']['jenis_antrian']);
            }
            if (!empty($post['fungsi_antrian'])) {
                $result->andWhere(['fungsiantrian_id' => $post['fungsi_antrian']]);
                unset($_GET['advanced-filter']['fungsi_antrian']);
            }
            if (!empty($post['group_carabayar'])) {
                $result->andWhere(['groupcarabayar_id' => $post['group_carabayar']]);
                unset($_GET['advanced-filter']['group_carabayar']);
            }

        }
        $query = DocoRestActiveFilter::advancedFilter(new KonfigantrianV, $result);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetAttributeOptions($id = null)
    {
        $cara_bayar = Cache::getGroupCaraBayar();
        $klasifikasi = Cache::getKlasifikasiPasien();
        $jenis_antrian = Cache::getJenisAntrian();
        $fungsi_antrian = Cache::getFungsiAntrian(3600,false);
        $instalasi = Cache::getInstalasi();
        $ruangan = Cache::getRuangan(3600,false);
        $pegawaiDokter = Cache::getPegawaiDokter(3600, false);
        $find = [];
        if ($id) {
            $find = KonfigAntrian::find()->where([
                'konfigantrian_id' => $id
            ])->one();
        }
        return [
            'cara_bayar' => $cara_bayar,
            'klasifikasi' => $klasifikasi,
            'jenis_antrian' => $jenis_antrian,
            'fungsi_antrian' => $fungsi_antrian,
            'instalasi' => $instalasi,
            'ruangan' => $ruangan,
            'pegawai_dokter' => $pegawaiDokter,
            'data' => $find
        ];
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new KonfigAntrian;
        $transaction = Yii::$app->db->beginTransaction(); 
        try {

            $model->attributes = $request->post();
            // $model->instalasi_id = empty($model->instalasi_id) ? null : $model->instalasi_id;
            $model->ruangan_id = empty($model->ruangan_id) ? null : $model->ruangan_id;
            $model->pegawai_id = empty($request->post('pegawai_id')) ? null : $request->post('pegawai_id');

            $find = KonfigantrianV::find()->where([
                'jenisantrian_id' => $model->jenisantrian_id,
                'klasifikasipasien_id' => empty($model->klasifikasipasien_id) ? null : $model->klasifikasipasien_id,
                'ruangan_id' => empty($model->instalasi_id) ? null : $model->instalasi_id,
                'groupcarabayar_id' => empty($model->groupcarabayar_id) ? null : $model->groupcarabayar_id,
                'ruangan_id' => empty($model->ruangan_id) ? null : $model->ruangan_id,
                'fungsiantrian_id' => empty($request->post('fungsi_antrian_id')) ? null : $request->post('fungsi_antrian_id'),
                'instalasi_id' => empty($request->post('instalasi_id')) ? null : $request->post('instalasi_id'),
                'pegawai_id' => empty($request->post('pegawai_id')) ? null : $request->post('pegawai_id'),
                // 'is_deleted' => false
            ])->one();


            if ($find) {
                if ($model->jenisantrian_id == DocoConstants::VAR_JA_P) {
                    return [
                            'text' => 'Konfig dokter '.$find['nama_dokter'].' sudah ada',
                            'status' => 422
                        ];
                } else {
                    return [
                            'text' => 'Konfig sudah ada',
                            'status' => 422
                        ];
                }
            } else {
                if ($model->jenisantrian_id == DocoConstants::VAR_JA_P) {
                    $findAntrianPoli = KonfigAntrian::find()->where([
                        'jenisantrian_id' => $model->jenisantrian_id,
                        'kode_antrian' => $model->kode_antrian,
                        'is_deleted' => false
                    ])->one();

                    if ($findAntrianPoli) {
                        return [
                            'text' => 'Kode antrian '.$findAntrianPoli['kode_antrian'].' sudah digunakan',
                            'status' => 422
                        ];
                    }
                }


                $model->fungsiantrian_id = $request->post('fungsi_antrian_id');
                if ($model->save()) {
                    $transaction->commit();
                    return [
                        'message' => 'Data berhasil disimpan'
                    ];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            Yii::info($e->getMessage());
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollback();
            Yii::info($e->getMessage());
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = KonfigAntrian::find()->where([
            'konfigantrian_id' => $id
        ])->one();
        $transaction = Yii::$app->db->beginTransaction(); 
        try {
            $model->attributes = $request->post();
            $pegawai_id = empty($request->post('pegawai_id')) ? null : $request->post('pegawai_id');
            
            if ($model->jenisantrian_id == DocoConstants::VAR_JA_P) {
                if ($model->pegawai_id != $pegawai_id) {
                    $findAntrianPoli = KonfigAntrianV::find()->where([
                        'pegawai_id' => $pegawai_id
                    ])->one();

                    if ($findAntrianPoli) {
                        return [
                                'text' => 'Konfig dokter '.$findAntrianPoli['nama_dokter'].' sudah ada',
                                'status' => 422
                            ];
                    }

                } else {
                    $findAntrianPoli = KonfigAntrian::find()
                    ->where([
                        'jenisantrian_id' => $model->jenisantrian_id,
                        'kode_antrian' => $model->kode_antrian,
                        'is_deleted' => false
                    ])
                    ->andWhere(['<>','pegawai_id', $pegawai_id])
                    ->one();

                    if ($findAntrianPoli) {
                        return [
                            'text' => 'Kode antrian '.$findAntrianPoli['kode_antrian'].' sudah digunakan',
                            'status' => 422
                        ];
                    }
                }

            }

            $model->fungsiantrian_id = $request->post('fungsi_antrian_id');
            $model->instalasi_id = $request->post('instalasi_id',null);
            $model->ruangan_id = $request->post('ruangan_id',null);
            $model->ruangan_id = empty($model->ruangan_id) ? null : $model->ruangan_id;
            $model->pegawai_id = $pegawai_id;




            // $find = KonfigAntrian::find()->where([
            //     'jenisantrian_id' => $model->jenisantrian_id,
            //     'klasifikasipasien_id' => empty($model->klasifikasipasien_id) ? null : $model->klasifikasipasien_id,
            //     // 'instalasi_id' => $model->instalasi_id,
            //     'groupcarabayar_id' => empty($model->groupcarabayar_id) ? null : $model->groupcarabayar_id,
            //     'ruangan_id' => empty($model->ruangan_id) ? null : $model->ruangan_id,
            //     'is_deleted' => false,
            // ])->andWhere(['<>','konfigantrian_id', $id])->one();

            // if ($find) {
            //     return [
            //             'message' => 'Konfig sudah ada',
            //             'status' => 500
            //         ];
            // } else {
                if ($model->save()) {
                    $transaction->commit();
                    return [
                        'message' => 'Data berhasil disimpan'
                    ];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            // }

        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            Yii::info($e->getMessage());
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollback();
            Yii::info($e->getMessage());
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    // public function actionCreate()
    // {
    //     $request = Yii::$app->request;
    //     $model = new Konfigantrian;
    //     $transaction = Yii::$app->db->beginTransaction(); 
    //     try {
    //         if ($request->post()) {
    //             $data_post = $request->post();
    //             $arr = [];
    //             foreach ($data_post as $row) {
    //                 $arr[] = [
    //                     'fungsiantrian_id' => $row['fungsiantrian_id'],
    //                     'is_default' => isset($row['is_default']) ? true : '',
    //                     'klasifikasipasien_id' => $row['klasifikasipasien_id'],
    //                     'kode_antrian' => $row['kode_antrian'],
    //                     'ruangan_id' => $row['ruangan_id']
    //                 ];
    //             }
    //             KonfigAntrian::batchInsert($arr);
    //             $transaction->commit();
    //             return ['message'=>'Berhasil'];
    //         }else{
    //             throw new Exception("Terjadi Kesalahan", 500);
    //         }
    //     } catch (\yii\db\Exception $e) {
    //         $transaction->rollback();
    //         \Yii::$app->response->statusCode = 500;
    //         return ['message' => $e->getMessage()];
    //     } catch (\Exception $e) {
    //         $transaction->rollback();
    //         \Yii::$app->response->statusCode = 500;
    //         return ['message' => $e->getMessage()];
    //     }
    // }

    public function actionDelete($id)
    {
        $model = new KonfigAntrian;
        if ($model->delete($id)) {
            return [
                'message' => 'Data berhasil dihapus'
            ];
        }
        throw new HttpException(500, 'Data tidak berhasil di hapus');
    }

    public function actionView()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $result = $this->getData();

        $query = DocoRestActiveFilter::advancedFilter(new KonfigantrianV, $result);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    private function getData($filter = null)
    {
        $returnData = KonfigantrianV::find()->orderby(['jenisantrian_id' => SORT_ASC]);
        return $returnData;
    }

    public function actionListLayarantrian() {
        $items = ArrayHelper::map(Layarantrian::find()->where(['is_active' => true])->all(), 'layarantrian_id', 'layarantrian_judul');

        return $items;
    }

    public function actionListTypeScreen() {
        $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'jenis_antrian'])->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

    public function actionListFunctionScreen() {
        $items = ArrayHelper::map(Lookup::find()->where(['lookup_type' => 'fungsi_antrian'])->all(), 'lookup_id', 'lookup_name');

        return $items;
    }

    // Export excel
    public function actionExportExcel()
    {
        // Try catch
        try {
            // Model
            $request = Yii::$app->request;
            $get = $request->get();

            $result = $this->getData();
            if(!isset($_GET['order'])){
                $_GET['order'] = 'jenisantrian_id DESC';
            }
            $query = DocoRestActiveFilter::advancedFilter(new KonfigantrianV, $result);
            $data = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $newData = [];
                // $value['status'] = ($value['is_default'] == true) ? Yii::t('app','Aktif') :  Yii::t('app','Tidak aktif');
                // $data[$value['jenisantrian_id']][] = $value;
                $newData['jenis_antrian'] = $value['jenis_antrian'];
                $newData['kode_antrian'] = $value['kode_antrian'];
                $newData['nama_ruangan'] = $value['ruangan_nama'];
                $newData['cara_bayar'] = $value['carabayar_nama'];
                $newData['klasifikasi_pasien'] = $value['klasifikasipasien_nama'];
                $newData['status'] = ($value['is_default'] == true) ? Yii::t('app','Aktif') :  Yii::t('app','Tidak aktif');
                $data[] = $newData;
            }

            $header = [
                Yii::t('app', 'Jenis antrian') => isset($_GET['advanced-filter']['jenis_antrian']) ? $_GET['advanced-filter']['jenis_antrian'] : '',
                Yii::t('app', 'Kode antrian') => isset($_GET['advanced-filter']['kode_antrian']) ? $_GET['advanced-filter']['kode_antrian'] : '',
                Yii::t('app', 'Jenis pengambilan antrian') => isset($_GET['advanced-filter']['fungsi_antrian']) ? $_GET['advanced-filter']['fungsi_antrian'] : '',
                Yii::t('app', 'Cara bayar') => isset($_GET['advanced-filter']['carabayar_nama']) ? $_GET['advanced-filter']['carabayar_nama'] : '',
                Yii::t('app', 'Status') => isset($_GET['advanced-filter']['is_default']) ? ($_GET['advanced-filter']['is_default'] == true) ? Yii::t('app','Aktif') :  Yii::t('app','Tidak aktif') : '',
            ];
            
            // File path
            $filePath = DocoHelpers::exportExcel('Laporan pengambilan antrian', $data, $header, array("uploadPath" => "./uploads"));

            // Return
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
        } catch (Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    /**
    * @controller actionExportPdf
    * @attribute #table_exportpdf# => table 
    **/
    public function actionExportPdf()
    {
        // Try catch
        try {
            // Model
            $request = Yii::$app->request;
            $get = $request->get();

            $result = $this->getData();
            if(!isset($_GET['order'])){
                $_GET['order'] = 'jenisantrian_id DESC';
            }
            $query = DocoRestActiveFilter::advancedFilter(new KonfigantrianV, $result);
            $data = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $newData = [];
                // $value['status'] = ($value['is_default'] == true) ? Yii::t('app','Aktif') :  Yii::t('app','Tidak aktif');
                // $data[$value['jenisantrian_id']][] = $value;
                $newData['kode_antrian'] = $value['kode_antrian'];
                $newData['jenis_antrian'] = $value['jenis_antrian'];
                $newData['fungsi_antrian'] = $value['fungsi_antrian'];
                $newData['carabayar_nama'] = $value['carabayar_nama'];
                $newData['klasifikasipasien_nama'] = $value['klasifikasipasien_nama'];
                $newData['status'] = ($value['is_default'] == true) ? Yii::t('app','Aktif') :  Yii::t('app','Tidak aktif');
                $data[] = $newData;
            }

            $header = [
                'jenis_antrian' => isset($_GET['advanced-filter']['jenis_antrian']) ? $_GET['advanced-filter']['jenis_antrian'] : '',
                'kode_antrian' => isset($_GET['advanced-filter']['kode_antrian']) ? $_GET['advanced-filter']['kode_antrian'] : '',
                'fungsi_antrian' => isset($_GET['advanced-filter']['fungsi_antrian']) ? $_GET['advanced-filter']['fungsi_antrian'] : '',
                'carabayar_nama' => isset($_GET['advanced-filter']['carabayar_nama']) ? $_GET['advanced-filter']['carabayar_nama'] : '',
                'status' => isset($_GET['advanced-filter']['is_default']) ? ($_GET['advanced-filter']['is_default'] == true) ? Yii::t('app','Aktif') :  Yii::t('app','Tidak aktif') : '',
            ];
            // Print
            $print = new DocoPrint();

            // Assign attributes
            $print->attributes = [
                '#table_exportpdf#' => $this->renderPartial('index', [
                    'header' => $header,
                    'data' => $data,
                ]),
            ];

            // Print output
            $print->Output();
        } catch (Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}