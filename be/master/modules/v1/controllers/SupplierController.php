<?php
/**
 * @Author: Sigit
 * @Date:   2018-06-06 08:53:53
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-02-07 20:44:36
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Bank;
use app\modules\v1\models\Kabupaten;
use app\modules\v1\models\Propinsi;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\PesanDarahPmi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Barang;
use app\modules\v1\models\IntSupplierView;
use Doco\Services\Vendors\OdooService;
use app\modules\v1\models\Pajak;

class SupplierController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Supplier';

    public $messageBroker = [
        'create' => [
            'services' => [
                'Odoo' => [
                    'Supplier' => [
                        'last_insert' => true
                    ]
                ]
            ]
        ],
        'update' => [
            'services' => [
                'Odoo' => [
                    'Supplier' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ],
        'delete' => [
            'services' => [
                'Odoo' => [
                    'Supplier' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ]
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
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
        try {
            $model = new Supplier;
            $query = $model::find()->where([
                            'is_deleted' => false,
                            'is_active' => true
                            ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['created_date' => SORT_DESC]);
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

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = new Supplier;
            $post = $request->post();
            $model->scenario = 'default';
            $model->attributes = $post;
            if($model->validate()){
                if ($model->save()) {
                    $transaction->commit();
                    $return = [
                        'text' => 'Data Berhasil di simpan',
                        'title' => 'Proses berhasil !',
                        'code' => 200
                    ];

                    return $return;
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($model->errors,'SupplierForm');
                    return ['data' => $errors,'status' => 422];
                }
            }else{
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($model->errors, 'SupplierForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        }
    }


    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $model = Supplier::findOne($id);
            $model->scenario = 'default';
            $model->attributes = $request->post();

            if($model->validate()){
                if ($model->update()) {
                    $return = [
                        'text' => 'Data Berhasil di ubah',
                        'title' => 'Proses berhasil !',
                        'code' => 200
                    ];

                    return $return;
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'SupplierForm');
                    return ['data' => $errors,'status' => 422];
                }
            }else{
                $errors = DocoHelpers::parseError($model->errors, 'SupplierForm');
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

    public function actionGetSourceData()
    {
        try {
            $id = Yii::$app->request->get('id');

            $modelPropinsi = Propinsi::find()->where(['is_deleted' => false, 'is_active' => true]);
            $modelKabupaten = Kabupaten::find()->where(['is_deleted' => false, 'is_active' => true]);
            $modelBank = Bank::find()->where(['is_deleted' => false, 'is_active' => true]);
            $modelPajak = Pajak::find()->where(['is_active' => true]);
            $generateKode = $this->getLastKodeSupplier();

            if ($id != '') {
                $modelSelectedKabupaten = Kabupaten::find()->where(['propinsi_id' => $id, 'is_deleted' => false, 'is_active' => true])->asArray()->all();
            }

            return [
                'propinsi' => $modelPropinsi->asArray()->all(),
                'kabupaten' => $modelKabupaten->asArray()->all(),
                'selectedKabupaten' => isset($modelSelectedKabupaten) ? $modelSelectedKabupaten : [],
                'bank' => $modelBank->asArray()->all(),
                'pajak' => $modelPajak->asArray()->all(),
                'kode_supplier' => $generateKode
            ];
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

    public function actionGetLastKodeSupplier() {
        return $this->getLastKodeSupplier();
    }

    private function getLastKodeSupplier() {
        try {
            $sql = "SELECT supplier_id as last_number, supplier_kode from supplier_m order by supplier_id desc";
            $query = Yii::$app->db->createCommand($sql)->queryAll();
            $last = array_pop(array_reverse($query));
            $arr_of_kode = array_column($query, 'supplier_kode');

            $nomor = 'S001';
            if (isset($last['last_number'])) {
                $last_number = $last['last_number'] + 1;
                $prefix = 'S';
                if (strlen($last_number) == 1) {
                    $last_number = '00' . $last_number;
                } else if (strlen($last_number) == 2) {
                    $last_number = '0' . $last_number;
                } else if (strlen($last_number) == 3) {
                    $last_number = $last_number;
                }
                $nomor = $prefix . $last_number;
            }

            while(in_array($nomor, $arr_of_kode)) {
                $last_number++;
                $nomor = $prefix . $last_number;
            }

            $query = $nomor;
        } catch (\yii\db\Exception $e) {
            $query = '';
        }
        return $query;
    }

    // Get propinsi
    public function actionGetPropinsi()
    {
        try {
            $id = Yii::$app->request->get('id');

            if ($id != '') {
                $model = Kabupaten::find()->where(['kabupaten_id' => $id, 'is_deleted' => false, 'is_active' => true])->one();

                if (!empty($model)) {
                    $newModel[] = $model->propinsi;
                }
            }
            else {
                $newModel[] = new Propinsi();
            }

            return ['data' => $newModel];
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetKabupaten()
    {
        try {
            $id = Yii::$app->request->get('id');
            if ($id != '') {
                $model = Kabupaten::find()->where(['propinsi_id' => $id, 'is_deleted' => false, 'is_active' => true])->all();
            }
            else {
                $model = new Kabupaten();
            }
            return ['data' => $model];
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

    /**
    * @controller actionExportPdf
    * @attribute #table_exportpdf# => Untuk menampilkan data di table
    * @attribute #kodeSupplier# => Untuk menampilkan data Kode
    * @attribute #namaSupplier# => Untuk menampilkan data Nama
    * @attribute #alamatSupplier# => Untuk menampilkan data Alamat
    * @attribute #noTeleponSupplier# => Untuk menampilkan data No Telepon
    */

    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $searchKode = '';
            $searchNama = '';
            $searchAlamat = '';
            $searchNoTelepon = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['supplier_kode'])) {
                    $searchKode = $advancedFilters['supplier_kode'];
                }
                if (!empty($advancedFilters['supplier_nama'])) {
                    $searchNama = $advancedFilters['supplier_nama'];
                }
                if (!empty($advancedFilters['supplier_alamat'])) {
                    $searchAlamat = $advancedFilters['supplier_alamat'];
                }
                if (!empty($advancedFilters['no_tlp'])) {
                    $searchNoTelepon = $advancedFilters['no_tlp'];
                }
            }

            $model = new Supplier;
            $query = $model::find()->where([
                                'is_deleted' => false,
                                'is_active' => true
                                ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['created_date' => SORT_DESC]);
            $result = $query->all();
            $header = [];
            $resultData = [];
            if (!empty($result)) {
                foreach ($result as $index => $value) {
                    $resultData[] = $value ;
                }
            }

            $print = new DocoPrint();
            $print->attributes = [
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'header' => $header,
                        'model' => $resultData,
                    ]),
                    '#kodeSupplier#' => $searchKode,
                    '#namaSupplier#' => $searchNama,
                    '#alamatSupplier#' => $searchAlamat,
                    '#noTeleponSupplier#' => $searchNoTelepon,
                ];

            $print->Output();
        } catch (Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        $title = 'Master Supplier';
        try {
            $request = Yii::$app->request;
            $ruangan_id = Yii::$app->jwt->ruangan_id;
            $ruangan = Ruangan::find()->where([
                'ruangan_id' => $ruangan_id
            ])->one();
            $searchKode = '';
            $searchNama = '';
            $searchAlamat = '';
            $searchNoTelepon = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (!empty($advancedFilters['supplier_kode'])) {
                    $searchKode = $advancedFilters['supplier_kode'];
                }
                if (!empty($advancedFilters['supplier_nama'])) {
                    $searchNama = $advancedFilters['supplier_nama'];
                }
                if (!empty($advancedFilters['supplier_alamat'])) {
                    $searchAlamat = $advancedFilters['supplier_alamat'];
                }
                if (!empty($advancedFilters['no_tlp'])) {
                    $searchNoTelepon = $advancedFilters['no_tlp'];
                }
            }

            $model = new Supplier;
            $query = $model::find()->where([
                                'is_deleted' => false,
                                'is_active' => true
                                ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['created_date' => SORT_DESC]);
            $result = $query->all();

            $data = [];
            if (!empty($result)) {
                $counter = 0;
                foreach ($result as $index => $value) {
                    $data[$counter]['Kode Supplier'] = $value->supplier_kode;
                    $data[$counter]['Nama Supplier'] = $value->supplier_nama;
                    $data[$counter]['Alamat'] = $value->supplier_alamat;
                    $data[$counter]['No.Telepon'] = $value->no_tlp;
                    $counter++;
                }
            }
            $header = [
                'Tanggal Unduh' => date('d-M-Y H:i:s'),
                'Kode Supplier' => $searchKode,
                'Nama Supplier' => $searchNama,
                'Alamat' => $searchAlamat,
                'No.Telepon' => $searchNoTelepon,
            ];

            $footer = [
                'title' => [
                    0 => '',
                    1 => '',
                ],
                'data' => [
                    'Tanggal Unduh' => date('d-M-Y H:i:s'),
                    'Diunduh Oleh' => $ruangan->ruangan_nama,
                ]
            ];

            $filePath = DocoHelpers::exportExcel($title, $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }


    // Print
    public function actionPrint()
    {
        try {
            $request = Yii::$app->request;

            $model = new Supplier;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();

            return $query;
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

    public function actionGetDataPmi()
    {
        try {
            $request = Yii::$app->request;
            $model = new Supplier;
            $query = $model::find()->where(['is_deleted' => false, 'is_pmi' => true]);
            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['is_active'])) {
                    $is_active = ($advancedFilter['is_active'] == 1) ? true : false;
                    $query->andWhere(['is_active' => $is_active]);
                }
            }

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

    public function actionCreatePmi()
    {
        try {
            $model = new Supplier;
            $post = Yii::$app->request->post();
            if ($model->load($post, '')) {
                $model->supplier_nama = $post['nama_pmi'];
                $model->is_pmi = true;
                $model->no_tlp = $post['no_tlp'];
                $model->supplier_alamat = $post['supplier_alamat'];
                $model->is_active = $post['is_active'];
                if ($model->validate() && $model->save(false)) {
                    return true;
                }
                else {
                    $errors = DocoHelpers::parseError($model->errors, 'SupplierForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $errors) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $message
            ];
        } catch (\Exception $errors) {
            \Yii::$app->response->statusCode = 500;
            return [
                'errors' => $errors
            ];
        }
    }

    public function actionUpdatePmi()
    {
        try {
            $request = Yii::$app->request;
            $model = Supplier::findOne($request->get('id'));
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'SupplierForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            else{
                throw new \Exception('Data Tidak Di Temukan');
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeletePmi($id)
    {
        $check = PesanDarahPmi::find()->where([
            'supplier_id' => $id
        ])->one();

        if (empty($check)) {
            $delete = (new Supplier)->delete($id);
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
        }
        return [
            'status' => 422,
            'title' => 'Proses Gagal !',
            'text' => 'Data sudah di gunakan'
        ];
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $getData = Supplier::find()
            ->where(['supplier_id' => $id])->all();

        $valPrimaryKey = [];
        foreach ($getData as $key => $value) {
            $valPrimaryKey[] = $value['supplier_id'];
        }

        $checkObat = ObatAlkes::find()
            ->where([
                'IN', 'supplier_id', $valPrimaryKey
            ])->all();

        $checkBarang = Barang::find()
            ->where([
                'IN', 'supplier_id', $valPrimaryKey
            ])->all();

        if ((empty($checkObat)) && (empty($checkBarang)) ) {
            $model_supplier = Supplier::findOne($id);
            // $delete = (new Supplier)->delete($id);
            $model_supplier->is_deleted = true;
            $model_supplier->is_active = false;
            $model_supplier->save();
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
        }
        return [
            'status' => 422,
            'title' => 'Proses Hapus Gagal !',
            'text' => 'Data ini sudah dipakai transaksi'
        ];
    }

    public function actionExportExcelPmi()
    {
        $request = Yii::$app->request;
        $model = new Supplier;
        $query = $model::find()->where(['is_deleted' => false, 'is_pmi' => true]);
        $title = 'Palang Merah Indonesia';
        $arrayNamaPmi = [];
        $arrayAlamat = [];
        $arrayStatus = [];
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['supplier_nama'])) {
                $query->andWhere(['ILIKE', 'supplier_nama', $advancedFilters['supplier_nama']]);
                $arrayNamaPmi = [
                    Yii::t('app', "Nama PMI") => $advancedFilters['supplier_nama']
                ];
            }
            if(isset($advancedFilters['supplier_alamat'])) {
                $query->andWhere(['ILIKE', 'supplier_alamat', $advancedFilters['supplier_alamat']]);
                $arrayAlamat = [
                    Yii::t('app', "Alamat") => $advancedFilters['supplier_alamat']
                ];
            }
            if(isset($advancedFilters['is_active'])) {
                $is_active = ($advancedFilters['is_active'] == 1) ? true : false;
                $query->andWhere(['is_active' => $is_active]);
                $arrayStatus = [
                    Yii::t('app', "Status") => ($advancedFilters['is_active'] == 0) ? "Tidak Aktif" : "Aktif"
                ];
            }
        }

        $additional = array_merge($arrayNamaPmi, $arrayAlamat, $arrayStatus);
        $header = $additional;
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Nama PMI')] = $value['supplier_nama'];
            $newValue[\Yii::t('app', 'Alamat')] = $value['supplier_alamat'];
            $newValue[\Yii::t('app', 'No Telepon')] = $value['no_tlp'];
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif' ;
            $result[$key] = $newValue;
        }

        $footer = [
            'title' => [
                0 => '',
                1 => '',
            ],
            'data' => [
                'Nama PMI' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s'),
                'Nama PMI' => 'Tanggal Unduh : ' . date('d-M-Y H:i:s'),
            ]
        ];
        $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
        /*

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ),$footer,[]);

        // return $filePath;
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));*/
    }
}
?>
