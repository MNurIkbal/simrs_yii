<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Barang;
use app\modules\v1\models\SatuanKonversiBarang;
use app\modules\v1\models\BarangView;
use app\modules\v1\models\KelompokBarang;
use app\modules\v1\models\SubKelompokBarang;
use app\modules\v1\models\Lookup;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use app\modules\v1\models\InfoStokBarang;
use app\modules\v1\models\TambahStokOpnameBarangFn;
use app\modules\v1\models\BarangHistoryView;
use Doco\Services\Logger\BarangLogger;
use Doco\Services\Cache;

class BarangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Barang';

    public $messageBroker = [
        'create' => [
            'services' => [
                'Odoo' => [
                    'Barang' => [
                        'last_insert' => true
                    ]
                ],
                'Sirs' => [
                    'BarangAkunting' => [
                        'last_insert' => true,
                        'state' => 'create'
                    ]
                ]
            ]
        ],
        'update' => [
            'services' => [
                'Odoo' => [
                    'Barang' => [
                        'query_params' => ['id'],
                    ]
                ],
                'Sirs' => [
                    'BarangAkunting' => [
                        'query_params' => ['id'],
                        'state' => 'edit'
                    ]
                ]
            ]
        ],
        'delete' => [
            'services' => [
                'Odoo' => [
                    'Barang' => [
                        'query_params' => ['id'],
                    ]
                ],
                'Sirs' => [
                    'BarangAkunting' => [
                        'query_params' => ['id'],
                        'state' => 'delete'
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE", "POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new BarangView;
            $query = $model->find();

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['kelompokbarang_nama'])) {
                    $kelompokbarang_id = $advancedFilter['kelompokbarang_nama'];
                    $query->andWhere(['kelompokbarang_id' => $kelompokbarang_id]);
                    unset($_GET['advanced-filter']['kelompokbarang_nama']);
                }
                if(isset($advancedFilter['subkelompok_nama'])) {
                    $subkelompokbarang_id = $advancedFilter['subkelompok_nama'];
                    $query->andWhere(['subkelompokbarang_id' => $subkelompokbarang_id]);
                    unset($_GET['advanced-filter']['subkelompok_nama']);
                }
                if(isset($advancedFilter['golonganbarang_nama'])) {
                    $golonganbarang_id = $advancedFilter['golonganbarang_nama'];
                    $query->andWhere(['golonganbarang_id' => $golonganbarang_id]);
                    unset($_GET['advanced-filter']['golonganbarang_nama']);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
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

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = new Barang;
            $old_model = clone $model;
            $model->attributes = $post;
            if($model->validate() && $model->save()){
                $oa_id = $model->barang_id;
                if(!empty($model->satuan1_id) && empty($model->satuan2_id)) {
                    $find = SatuanKonversiBarang::find()->where(['barang_id'=>$oa_id, 'satuanbesar_id'=>$model->satuan1_id,'satuankecil_id'=>$model->satuankecil_id])->asArray()->all();

                    $isi_satuan1 = empty($model->isi_satuan1) ? 0 : $model->isi_satuan1;
                    $datakonversi[] = [
                        'satuanbesar_id'=>$model->satuankecil_id,
                        'satuankecil_id'=>$model->satuankecil_id,
                        'nilai_konversi'=>1,
                        'barang_id'=>$oa_id,
                    ];
                    $datakonversi[] = [
                        'satuanbesar_id'=>$model->satuan1_id,
                        'satuankecil_id'=>$model->satuankecil_id,
                        'nilai_konversi'=>$isi_satuan1,
                        'barang_id'=>$oa_id,
                    ];
                }

                if(!empty($model->satuan1_id) && !empty($model->satuan2_id)) {
                    $find = SatuanKonversiBarang::find()->where(['barang_id'=>$oa_id, 'satuanbesar_id'=>$model->satuan1_id,'satuankecil_id'=>$model->satuankecil_id])->asArray()->all();

                    $isi_satuan1 = empty($model->isi_satuan1) ? 0 : $model->isi_satuan1;
                    $isi_satuan2 = empty($model->isi_satuan2) ? 0 : $model->isi_satuan2;
                    $datakonversi[0] = [
                        'satuanbesar_id'=>$model->satuan1_id,
                        'satuankecil_id'=>$model->satuankecil_id,
                        'nilai_konversi'=>$isi_satuan1,
                        'barang_id'=>$oa_id,
                    ];
                    $datakonversi[1] = [
                        'satuanbesar_id'=>$model->satuan2_id,
                        'satuankecil_id'=>$model->satuankecil_id,
                        'nilai_konversi'=>$isi_satuan2,
                        'barang_id'=>$oa_id,
                    ];
                    $datakonversi[2] = [
                            'satuanbesar_id'=>$model->satuankecil_id,
                            'satuankecil_id'=>$model->satuankecil_id,
                            'nilai_konversi'=>1,
                            'barang_id'=>$oa_id,
                        ];
                }

                if(!empty($model->satuan2_id) && empty($model->satuan1_id)) {
                    $find = SatuanKonversiBarang::find()->where(['barang_id'=>$oa_id, 'satuanbesar_id'=>$model->satuan2_id,'satuankecil_id'=>$model->satuankecil_id])->asArray()->all();

                    $isi_satuan2 = empty($model->isi_satuan2) ? 0 : $model->isi_satuan2;
                    $datakonversi[] = [
                            'satuanbesar_id'=>$model->satuan2_id,
                            'satuankecil_id'=>$model->satuankecil_id,
                            'nilai_konversi'=>$isi_satuan2,
                            'barang_id'=>$oa_id,
                        ];
                }

                if(empty($model->satuan1_id) && empty($model->satuan2_id)) {
                    $find = SatuanKonversiBarang::find()->where(['barang_id'=>$oa_id, 'satuanbesar_id'=>$model->satuankecil_id,'satuankecil_id'=>$model->satuankecil_id])->asArray()->all();

                    $datakonversi[] = [
                            'satuanbesar_id'=>$model->satuankecil_id,
                            'satuankecil_id'=>$model->satuankecil_id,
                            'nilai_konversi'=>1,
                            'barang_id'=>$oa_id,
                        ];
                }

                if(count($find) < 1){
                    try {
                        SatuanKonversiBarang::batchInsert($datakonversi);
                    } catch (\yii\db\Exception $e) {
                        throw new \Exception("Data gagal diinputkan!");
                    }
                    
                }else{
                    throw new \Exception("Data sudah pernah di inputkan!");
                }
                (new BarangLogger())->log($model, DocoConstants::KET_HRG_BRG_PM, $old_model->attributes, $model->attributes, ['catatan' => 'Create Master Barang']);

                $transaction->commit();
                Yii::$app->cache->delete('all-satuan-konversi-barang');
                return true;
            }else{
                return ['meta-status'=>422, 'data'=>$model->getErrors()];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = Barang::findOne($id);
            $old_model = clone $model;
            $satuankecil_id = $model->satuankecil_id;
            $update = false;
            $booleanSedang = false;
            $booleanBesar = false;
            try {
                if(!empty($post['satuan1_id'])) {
                    $isi_satuan1 = empty($post['isi_satuan1']) ? 0 : $post['isi_satuan1'];
                    $cekSatuan1 = SatuanKonversiBarang::find()
                        ->where([
                            'satuanbesar_id' => $post['satuan1_id'], 
                            'satuankecil_id' => $post['satuankecil_id'],
                            'barang_id' => $id
                        ])
                        ->one();
                    
                    $cekSatuan1 = ($cekSatuan1) ? $cekSatuan1 : new SatuanKonversiBarang;
                    $cekSatuan1->barang_id = $id;
                    $cekSatuan1->satuanbesar_id = $post['satuan1_id'];
                    $cekSatuan1->satuankecil_id = $post['satuankecil_id'];
                    $cekSatuan1->nilai_konversi = $isi_satuan1;
                    $cekSatuan1->save();
                    // $sql_besar = "UPDATE satuankonversibrg_m set 
                    //     satuanbesar_id = '{$post['satuan1_id']}' , 
                    //     satuankecil_id = '{$post['satuankecil_id']}', 
                    //     nilai_konversi = '{$isi_satuan1}' 
                    //     WHERE satuanbesar_id = '{$post['satuan1_id']}' AND satuankecil_id = '{$post['satuankecil_id']}' AND barang_id = '{$id}'
                    // ";
                    // $saveBesar = $connection->createCommand($sql_besar)->execute();
                    // if($saveBesar){
                    //     $booleanBesar = true;
                    // }

                    // if($booleanBesar == true && $booleanSedang == true){
                    //     $update = true;
                    // }
                }
                
                if(!empty($post['satuan2_id'])) {
                    $isi_satuan2 = empty($post['isi_satuan2']) ? 0 : $post['isi_satuan2'];
                    $cekSatuan2 = SatuanKonversiBarang::find()
                        ->where([
                            'satuanbesar_id' => $post['satuan2_id'], 
                            'satuankecil_id' => $post['satuankecil_id'],
                            'barang_id' => $id
                        ])
                        ->one();
                    
                    $cekSatuan2 = ($cekSatuan2) ? $cekSatuan2 : new SatuanKonversiBarang;
                    $cekSatuan2->barang_id = $id;
                    $cekSatuan2->satuanbesar_id = $post['satuan2_id'];
                    $cekSatuan2->satuankecil_id = $post['satuankecil_id'];
                    $cekSatuan2->nilai_konversi = $isi_satuan2;
                    $cekSatuan2->save();
                    // $sql_sedang = "UPDATE satuankonversibrg_m set 
                    //     satuanbesar_id = '{$post['satuan2_id']}' , 
                    //     satuankecil_id = '{$post['satuankecil_id']}', 
                    //     nilai_konversi = '{$isi_satuan2}' 
                    //     WHERE satuanbesar_id = '{$model->satuan2_id}' AND satuankecil_id = '{$model->satuankecil_id}' AND barang_id = '{$id}'
                    // ";
                    // $saveSedang = $connection->createCommand($sql_sedang)->execute();
                    // if($saveSedang){
                    //     $booleanSedang = true;
                    // }
                }
            } catch (\yii\db\Exception $e){
                \Yii::$app->response->statusCode = 500;
                return [
                    'message'=>$e->getMessage()
                ];
            }
            // if($update){
                $model->attributes = $post;
                $model->satuankecil_id = $satuankecil_id;
                if($model->save() ){
                    (new BarangLogger())->log($model, DocoConstants::KET_HRG_BRG_PM, $old_model->attributes, $model->attributes, ['catatan' => 'Update Master Barang']);

                    $transaction->commit();
                    return ['message' => 'success'];
                } else {
                    return [
                        'status' => 422,
                        'data' => $model->errors
                    ];
                }
            // }else{
            //     throw new \Exception("Terjadi kesalahan", 1);
                
            // }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }

    public function actionView($id)
    {
        $query = Barang::findOne($id);

        return $query;
    }

    public function actionDelete() 
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $cekBarang = $connection
                ->createCommand("SELECT * FROM stokbarang_t WHERE barang_id = {$id}")
                ->queryAll();

            if(count($cekBarang) > 0) {
                $result['status'] = 500;
                $result['text'] = "Tidak bisa menghapus Barang, data sudah digunakan di transaksi.";
            }
            else {
                $model = Barang::findOne($id);
                if ($model) {
                    $model->is_deleted = true;
                    $model->deleted_date = date('Y-m-d H:i:s');
                    $model->save();
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Satuan Berhasil',
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

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new BarangView;
        $query = $model::find();
        $namaRs = $request->get('namaRs');
        $title = 'Barang '.$namaRs;

        $kelompokbarang_nama = '';
        $subkelompok_nama = '';
        $golonganbarang_nama = '';

        $arrayKelompok = [];
        $arraySubKelompok = [];
        $arrayGolongan = [];

        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['barang_nama'])) {
                $query->andWhere(['ILIKE', 'barang_nama', $advancedFilters['barang_nama']]);
            }
            if(isset($advancedFilters['kelompokbarang_nama'])) {
                $kelompokbarang_id = $advancedFilters['kelompokbarang_nama'];
                $query->andWhere(['kelompokbarang_id' => $kelompokbarang_id]);
                $kelompok = KelompokBarang::findOne($kelompokbarang_id);
                $kelompokbarang_nama = $kelompok->kelompokbarang_nama;
                $arrayKelompok = [
                    Yii::t('app', "Kelompok Barang") => $kelompokbarang_nama
                ];
            }
            if(isset($advancedFilters['subkelompok_nama'])) {
                $subkelompokbarang_id = $advancedFilters['subkelompok_nama'];
                $query->andWhere(['subkelompokbarang_id' => $subkelompokbarang_id]);
                $sub_kelompok = SubKelompokBarang::findOne($subkelompokbarang_id);
                $subkelompok_nama = $sub_kelompok->subkelompok_nama;
                $arraySubKelompok = [
                    Yii::t('app', "Sub Kelompok Barang") => $subkelompok_nama
                ];
            }
            if(isset($advancedFilters['golonganbarang_nama'])) {
                $golonganbarang_id = $advancedFilters['golonganbarang_nama'];
                $query->andWhere(['golonganbarang_id' => $golonganbarang_id]);
                $golongan = Lookup::findOne($golonganbarang_id);
                $golonganbarang_nama = $golongan->lookup_name;
                $arrayGolongan = [
                    Yii::t('app', "Golongan Barang") => $golonganbarang_nama
                ];
            }
            if(isset($advancedFilters['is_active'])) {
                $is_active = ($advancedFilters['is_active'] == 1) ? true : false;
                $query->andWhere(['is_active' => $is_active]);
            }
        }

        $additional = array_merge($arrayKelompok, $arraySubKelompok, $arrayGolongan);
        $header = $additional;
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Kode Barang')] = $value['barang_kode'];
            $newValue[\Yii::t('app', 'Nama Barang')] = $value['barang_nama'];
            $newValue[\Yii::t('app', 'Kelompok')] = $value['kelompokbarang_nama'];
            $newValue[\Yii::t('app', 'Sub Kelompok')] = $value['subkelompok_nama'];
            $newValue[\Yii::t('app', 'Golongan Barang')] = $value['golonganbarang_nama'];
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif' ;
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [], [], [], true);
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode tanggal
    * @attribute #tanggal# => tanggal sekarang
    * @attribute #jabatan# => jabatan
    * @attribute #title# => title
    * @attribute #pegawai# => pegawai mengetahui
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $namaRs = $get['namaRs'];
        $title = 'Barang '.$namaRs;
        $model = new BarangView;
        $query = $model::find();
        
        $kelompokbarang_nama = '';
        $subkelompok_nama = '';
        $golonganbarang_nama = '';
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['barang_nama'])) {
                $query->andWhere(['ILIKE', 'barang_nama', $advancedFilters['barang_nama']]);
            }
            if(isset($advancedFilters['kelompokbarang_nama'])) {
                $kelompokbarang_id = $advancedFilters['kelompokbarang_nama'];
                $query->andWhere(['kelompokbarang_id' => $kelompokbarang_id]);
            }
            if(isset($advancedFilters['subkelompok_nama'])) {
                $subkelompokbarang_id = $advancedFilters['subkelompok_nama'];
                $query->andWhere(['subkelompokbarang_id' => $subkelompokbarang_id]);
            }
            if(isset($advancedFilters['golonganbarang_nama'])) {
                $golonganbarang_id = $advancedFilters['golonganbarang_nama'];
                $query->andWhere(['golonganbarang_id' => $golonganbarang_id]);
            }
            if(isset($advancedFilters['is_active'])) {
                $is_active = ($advancedFilters['is_active'] == 1) ? true : false;
                $query->andWhere(['is_active' => $is_active]);
            }
        }
        
        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal#' => date('d M Y'),
            '#title#' => $title,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
            ]),
        ];
        $print->Output();
    }

    public function actionGenerateApi()
    {
        $kelompokBarang = KelompokBarang::find()->where(['is_active' => true])->all();
        $subKelompokBarang = SubKelompokBarang::find()->where(['is_active' => true])->all();
        $golonganBarang = Lookup::find()->where([
            'lookup_type' => 'golongan_barang', 'is_active' => true])
            ->all();

        return [
            'kelompok' => $kelompokBarang,
            'sub_kelompok' => $subKelompokBarang,
            'golongan' => $golonganBarang,
        ];
    }

    public function actionGetNamaSubKelompok($subkelompokbarang_id)
    {
        $query = SubKelompokBarang::findOne($subkelompokbarang_id);

        return $query;
    }

    public function actionGenerateKodeBarang($subkelompokbarang_id)
    {
        // Generator Config
        $incrementLength = 4;
        $padString = 0;

        $modelSubKelompok = new SubKelompokBarang;
        $querySubKelompok = $modelSubKelompok->find()->select(['subkelompok_kode']);
        $querySubKelompok->where(['subkelompokbarang_id' => $subkelompokbarang_id]);

        $prefix = ArrayHelper::getValue($querySubKelompok->asArray()->one(), 'subkelompok_kode', '');
        $prefixLength = strlen($prefix);

        $patternKode = $prefix . str_pad('_', $incrementLength, '_');
        
        $model = new Barang;
        $query = $model->find()->select(['barang_kode']);
        $query->where(['subkelompokbarang_id' => $subkelompokbarang_id, ])->andWhere(['ilike', 'barang_kode', $patternKode, false]);
        $query->orderBy(['barang_kode' => SORT_DESC]);
        $lastBarangKode = $query->one();

        // Generate section
        if($lastBarangKode == null) {
            // create new code, start from 1.
            $new_barang_kode = $prefix . str_pad(1, $incrementLength, $padString, STR_PAD_LEFT);
        } else {
            $num = substr($lastBarangKode->barang_kode, $prefixLength);
            $new_barang_kode = $prefix . str_pad($num + 1, $incrementLength, $padString, STR_PAD_LEFT);
        }

        return $new_barang_kode;
    }

    public function actionGetListBarang() {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);

        $modelBarang = new BarangView;
        $queryBarang = $modelBarang::find()->where(['is_active' => true])
            ->select([
                'barang_id', 'barang_nama', 'barang_kode', 
                'kelompokbarang_id', 'kelompokbarang_nama', 'subkelompokbarang_id', 'subkelompok_nama'
            ]);

        if (!empty($term)) {
            $queryBarang->andWhere(['like', 'LOWER(barang_nama)', strtolower($term)]);
        }
        $queryBarang->orderBy(['barang_nama' => SORT_ASC]);

        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;

        return $queryBarang->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    public function actionGetStok() {
        $request = Yii::$app->request;
        $barangId = $request->get('barang_id', null);
        $ruanganId = $request->get('ruangan_id', null);
        if(empty($barangId)) throw new \Exception("barang_id tidak boleh kosong");

        $model = InfoStokBarang::find()
            ->select(['barang_id', 'barang_nama', 'qty_tersedia', 'qty_stok', 'harga_netto'])
            ->where(['barang_id' => $barangId, 'ruangan_id' => $ruanganId])
            ->one();
        return $model ? $model : ['barang_id' => null,'qty_stok' => 0];
    }

    public function actionGetListBarangNewSo() {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $ruangan_id = $request->get('ruangan_id', null);

        $modelBarang = new TambahStokOpnameBarangFn(['extParam' => [(int)$ruangan_id]]);
        $queryBarang = $modelBarang::find()
        ->select([
            'barang_id', 'barang_nama', 'barang_kode', 
            'kelompok_barang AS kelompokbarang_nama', 
            'subkelompok_barang AS subkelompok_nama'
        ]);
        if (!empty($term)) {
            $queryBarang->where(['like', 'LOWER(barang_nama)', strtolower($term)])
            ->orWhere(['LIKE', 'LOWER(barang_kode)', strtolower($term)]);
        }
        $queryBarang->orderBy(['barang_nama' => SORT_ASC]);

        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;

        return $queryBarang->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    public function actionGetLogBarang()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $model = new BarangHistoryView;
            $query = $model->find();

            if ($id) {
                $query->where(["barang_id" => $id]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
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
}