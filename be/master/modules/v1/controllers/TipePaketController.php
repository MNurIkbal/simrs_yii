<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-26 10:14:57
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-22 15:09:55
 */

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use app\modules\v1\models\TipePaket;
use app\modules\v1\models\PaketRuanganMP;
use app\modules\v1\models\PaketPelayanan;
use app\modules\v1\models\PaketDetailV;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\DaftarTindakanV;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\TindakanPelayanan;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\MasterPaketMcuView;
use app\modules\v1\models\TindakanRuanganView;
use app\modules\v1\payload\PayloadForm;
use Doco\components\DocoMessages;
use app\modules\v1\models\TarifTindakan;
use app\modules\v1\models\PaketPelayananView;
use Doco\components\DocoConstants;
use Doco\Repositories\LookUpTransaksiRepositories;
use yii\helpers\ArrayHelper;

// Class tipe paket controller
class TipePaketController extends \Doco\components\DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\TipePaket';

    /**
     * Untuk Kebutuhan Integerasi Odoo
     * @var array
     */
    public $messageBroker = [
        'simpan-paket' => [
            'services' => [
                'Odoo' => [
                    'Paket' => [
                        'payload' => ['id' => 'tipepaket_id'],
                        'last_insert' => true
                    ]
                ]
            ]
        ],
        'update-paket' => [
            'services' => [
                'Odoo' => [
                    'Paket' => [
                        'query_params' => ['id' => 'tipepaket_id'],
                    ]
                ]
            ]
        ],
        'delete' => [
            'services' => [
                'Odoo' => [
                    'Paket' => [
                        'query_params' => ['id'],
                    ]
                ]
            ]
        ],
    ];

    // Verbs
    public function verbs()
    {
        $verbs = parent::verbs();
        
        $verbs["delete"] = ["DELETE","POST","GET"];
        return $verbs;
    }

    // Actions
    public function actions()
    {
        // Actions parent
        $actions = parent::actions();

        // Unset actions
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        // Return actions
        return $actions;
    }

    // Action index
    public function actionIndex()
    {
        $request = Yii::$app->request;
        // return $request->get();
        $advancedFilters = $request->get('advanced-filter', []);
        // Try catch
        try {
            // Find model
            $model = new TipePaket;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            if(isset($advancedFilters['is_mcu'])) {
                $is_mcu = ($advancedFilters['is_mcu'] == 1) ? true : false;
                $query->andWhere(['is_mcu' => $is_mcu]);
            }
            
            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
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
     * @attribute #tgl# => Tanggal sekarang 
     **/
    public function actionExportPdf()
    {
        // Try catch
        try {
            // Find model
            $query = $this->findModel();

            // Execute query
            $model = $query->all();

            // Check model
            if (!empty($model)) {
                // Print
                $print = new DocoPrint();

                // Assign attributes
                $print->attributes = [
                    '#tgl#'=>Docohelpers::convertTo224(date('Y-m-d')),
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'header' => array(),
                        'model' => $model,
                    ]),
                ];

                // Print output
                $print->Output();
            }
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Export excel
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        // Try catch
        try {
            // Declare empty variables
            $data = array();
            $header = $footer = [];
            if(!is_null($request->get('advanced-filter'))) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['tipepaket_kode'])){
                    $header['Kode Paket'] = $advancedFilter['tipepaket_kode'];
                }
                if(isset($advancedFilter['tipepaket_nama'])){
                    $header['Nama Paket'] = $advancedFilter['tipepaket_nama'];
                }
                if(isset($advancedFilter['tipepaket_namalainnya'])){
                    $header['Nama Lain Paket'] = $advancedFilter['tipepaket_namalainnya'];
                }
                if(isset($advancedFilter['keterangan_tipepaket'])){
                    $header['Catatan'] = $advancedFilter['keterangan_tipepaket'];
                }
                if(isset($advancedFilter['is_active'])) {
                    $is_active = $advancedFilter['is_active'];
                    $header['Status'] = ($is_active) ? 'Aktif' : 'Tidak Aktif';
                }
            }
            // Find model
            $query = $this->findModel();

            // Execute query
            $model = $query->all();

            // Assign data
            if (!empty($model)) {
                // Declare counter
                $counter = 0;
                $no = 0;

                // Loop
                foreach ($model as $index => $value) {
                    // Assign data
                    // $no++;
                    
                    $data[$counter]['kode_paket'] = $value->tipepaket_kode;
                    $data[$counter]['nama_paket'] = $value->tipepaket_nama;
                    $data[$counter]['nama_lainnya'] = $value->tipepaket_namalainnya;
                    $data[$counter]['status'] = ($value->is_active) ? "Aktif" : "Tidak Aktif";
                    $data[$counter]['catatan'] = $value->keterangan_tipepaket;

                    // Plus the counter
                    $counter++;
                }
            }
            
            // File path
            $filePath = DocoHelpers::exportExcel('Master - Tindakan - Paket', $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die();
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Find model
    private function findModel()
    {
        // Declare model
        $model = new TipePaket;

        // Find model
        $query = $model::find()
        // ->where([TipePaket::tableName().'.is_active' => true])
        ->where([TipePaket::tableName().'.is_deleted' => false]);

        // Doco active filter
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        // Return query
        return $query;
    }


    public function actionSimpanPaket(){
        $post = \Yii::$app->request->post();
        $daftarTindakan = $post['daftarTindakan'];
        $daftarTindakan = json_decode($daftarTindakan, true);
        $postData = isset($post['TipePaketForm']) ? $post['TipePaketForm'] : $post;
        $tipepaket_id = !empty($postData['tipepaket_id']) ? $postData['tipepaket_id'] : null;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $isActive =  $postData['is_active'] == 1 ? true : false;
        $is_mcu = $postData['is_mcu'] == 1 ? true : false;
        $payload = new PayloadForm;
        $payload->scenario = 'paket';
        $payload->attributes = $postData;
        $payload->is_active = $isActive;
        $payload->is_mcu = $is_mcu;

        if(empty($daftarTindakan['data'])) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => "Belum ada tindakan yang di mapping!"
            ]);
        }

        $isTransactionExist = false;
        if(!empty($tipepaket_id) && $tipepaket_id != null && $is_mcu){
            $isTransactionExist = (new TarifTindakan)->countBy(['tipepaket_id' => $tipepaket_id]) ? true : false;
        }

        if($is_mcu || $isTransactionExist) {
            $ruangan_id = [];
            foreach ($daftarTindakan['data'] as $key => $value) {
                $ruangan_id[] = $value['ruangan_id'];
                
            }
            
            if(in_array(null, $ruangan_id)) {
                return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => "Ruangan masih ada yang kosong."
                ]);
            }
            
        }

        $cekNamaPaket = TipePaket::find()->where(['LOWER (tipepaket_nama)' => strtolower($payload->tipepaket_nama), 'is_deleted' => false])->one();

        $cekKodePaket = TipePaket::find()->where(['LOWER (tipepaket_kode)' => strtolower($payload->tipepaket_kode), 'is_deleted' => false ])->one();
        
        if (!empty($cekNamaPaket) && $cekNamaPaket->tipepaket_id != $tipepaket_id) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => "Nama Paket Sudah Dipakai"
            ]);
        }

        if(!empty($cekKodePaket) && $cekKodePaket->tipepaket_id != $tipepaket_id) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_CUSTOM, [
                'text' => "Kode Paket Sudah Dipakai"
            ]);
        }
        
        if(!$payload->validate()) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                'data' => $payload->errors
            ]);
        }
        try {
            $model = !empty($tipepaket_id) ? TipePaket::findOne($tipepaket_id) : new TipePaket;
            $inputTipePaket = array(
                'tipepaket_id' => $tipepaket_id,
                'tipepaket_nama' => $postData['tipepaket_nama'],
                'tipepaket_kode' => $postData['tipepaket_kode'],
                'tipepaket_namalainnya' => $postData['tipepaket_namalainnya'],
                'keterangan_tipepaket' => $postData['keterangan_tipepaket'],
                'is_active' => $isActive,
                'is_mcu' => $is_mcu,
            );
            $model->attributes = $inputTipePaket;
            if($inputTipePaket['tipepaket_id'] == null) {
                unset($model->tipepaket_id);
            }

            if($model->validate() && $model->save()){

                if(!empty($daftarTindakan['data'])){
                    $inputPaketPelayananMP = array();
                    foreach($daftarTindakan['data'] as $k => $v){
                        if($model->is_mcu || $isTransactionExist) {
                            $inputPaketPelayananMP[] = [
                                'ruangan_id' => (int)$v['ruangan_id'],
                                'tipepaket_id' => (int)$model->tipepaket_id,
                                'daftartindakan_id' => empty($v['id']) ? null : (int)$v['id'],
                                'paketdetail_id' => empty($v['paketdetail_id']) ? null : (int)$v['paketdetail_id']
                            ];
                        }
                        else {
                            $inputPaketPelayananMP[] = array(
                                'daftartindakan_id' => $v['id'],
                                'tipepaket_id'=>$model->tipepaket_id,
                                'is_active'=>true
                            );
                        }
                    }

                    if(!empty($tipepaket_id)) {
                        Yii::$app->db->createCommand("
                            DELETE FROM paketpelayanan_mp 
                            WHERE tipepaket_id = {$tipepaket_id}
                        ")->execute();
                    }
                    
                    $PaketPelayanan = PaketPelayanan::batchInsert($inputPaketPelayananMP);
                }
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Simpan Berhasil',
                    'text' => 'Simpan Paket Berhasil',
                ];
            }else{
                $transaction->rollBack();
                $result['status'] = 422;
                $result['data'] = $model->getErrors();
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

    public function actionUpdatePaket(){
        $post = \Yii::$app->request->post();
        $get = \Yii::$app->request->get();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $model = TipePaket::findOne(['tipepaket_id'=>$get['tipepaket_id']]);
        try {

            if(!empty($model)){
                $inputTipePaket = array(
                    'tipepaket_nama' => $post['tipepaket_nama'],
                    'tipepaket_kode' => $post['tipepaket_kode'],
                    'tipepaket_namalainnya' => $post['tipepaket_namalainnya'],
                    'keterangan_tipepaket' => $post['keterangan_tipepaket'],
                    'is_active' => $post['is_active'],
                );
                $model->attributes = $inputTipePaket;
                if ($model->save()) {
                    if (!empty($post['daftarTindakan'])) {
                        $daftarTindakan = json_decode($post['daftarTindakan'], true);
                        $inputPaketPelayananMP = array();
                        foreach ($daftarTindakan['data'] as $k => $v) {
                            // periksa item2 yang belum di input. pendeteksinya adalah tipepkaet_id yang kosong
                            if(empty($v['tipepaket_id'])){
                                $inputPaketPelayananMP[] = array(
                                    'daftartindakan_id' => $v['id'],
                                    'tipepaket_id' => $model->tipepaket_id,
                                    'is_active' => 1
                                );
                            }
                            // periksa item2 yang belum di input. pendeteksinya adalah tipepkaet_id yang kosong

                        }

                        $PaketPelayanan = PaketPelayanan::batchInsert($inputPaketPelayananMP);
                    }
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Ubah Berhasil',
                        'text' => 'Data Berhasil di Ubah.',
                    ];
                } else {
                    $transaction->rollBack();
                    $result['status'] = 422;
                    $result['data'] = $model->getErrors();
                }
            }else{
                $transaction->rollBack();
                $result['status'] = 500;
                $result['title'] = 'Simpan gagal';
                $result['text'] = "Data tidak dikenali";
                $result['data'] = $model->getErrors();
            }
            
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                // 'post'=>$post
            ];
        }
    }



    public function actionDetailPaketTindakan()
    {
        $get = \Yii::$app->request->get();
        $is_paging = isset($get['is_paging']) ? $get['is_paging'] : 0;
        $is_paging = ($is_paging == 1) ? true : false;
        try {
            $model = new PaketPelayananView;
            $query = $model::find();
            $query = $query->where(['tipepaket_id' => $get['tipepaket_id']]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $provider = [
                'query' => $query,
            ];
            if(!$is_paging) {
                $provider = [
                    'query' => $query,
                    'pagination' => false,
                ];
            }
            return new ActiveDataProvider($provider);
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDelMapping(){
        $get = \Yii::$app->request->get();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        
        // $model = PaketPelayanan::findOne($get['daftartindakan_id'], $get['tipepaket_id']);
        $model = PaketPelayanan::findOne([
            'daftartindakan_id'=> $get['daftartindakan_id'],
            'tipepaket_id'=> $get['tipepaket_id']
        ]);

        if(!$model) {
            $model = PaketPelayanan::findOne([
                'paketdetail_id'=> $get['daftartindakan_id'],
                'tipepaket_id'=> $get['tipepaket_id']
            ]);
        }
        try {
            $model->is_deleted = true;
            if ($model->save()) {
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Hapus Berhasil',
                    'text' => 'Hapus Tindakan Berhasil',
                ];
            } else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['text'] = "Gagal Menghapus Data";
                $result['text'] = $model->getErrors();
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

    private function getKelompokTindakanFisio()
    {
        $keyId = LookupTransaksi::find()
            ->select(['additional_value'])
            ->where(['kode_transaksi' => DocoConstants::TINDAKAN_KELOMPOK_FISIO])
            ->asArray()
            ->one();
        return $keyId;
    }

    public function actionAutoTindakan()
    {
        $request = Yii::$app->request;
        $is_mcu = $request->get('is_mcu');
        $is_mcu = ($is_mcu == 1) ? true : false;
        $is_fisioterapi = $request->get('is_fisioterapi');
        $is_fisioterapi = ($is_fisioterapi == 1) ? true : false;
        $pagination = true;

        if (!is_null($request->get('advanced-filter'))) {
            $advancedFilter = $request->get('advanced-filter');
            $model = !empty($advancedFilter['ruangan_id']) ? new TindakanRuanganView : new DaftarTindakanV;
            $query = $model::find();
            $query->where(['is_active' => true]);

            if($is_mcu) {
                if(!is_null($advancedFilter['ruangan_id'])) {
                    $ruangan_id = !empty($advancedFilter['ruangan_id']) ? $advancedFilter['ruangan_id'] : null;
                    if($ruangan_id) {
                        $query->andWhere(['ruangan_id' => $advancedFilter['ruangan_id']]);
                        if (isset($advancedFilter['daftartindakan_nama'])) {
                            $query->andFilterWhere([
                                'or',
                                ['ILIKE','daftartindakan_nama', $advancedFilter['daftartindakan_nama']],
                                ['ILIKE', 'kelompoktindakan_nama', $advancedFilter['kelompoktindakan_nama']]
                            ]);
                        }

                    }
                    else {
                        $query->andWhere(['ruangan_id' => null]);
                    }
                    $pagination = false;
                }
            }else if($is_fisioterapi){
                $kelompokTindakan = $this->getKelompokTindakanFisio();
                // $kategoriTindakan = $this->getKategoriTindakanFisio();
                if($kelompokTindakan){
                    $kelompokTindakanIdsTemp = ArrayHelper::getValue($kelompokTindakan, 'additional_value');
                    $kelompokTindakanIdsTemp = str_replace('[', '', $kelompokTindakanIdsTemp);
                    $kelompokTindakanIdsTemp = str_replace(']', '', $kelompokTindakanIdsTemp);
                    $kelompokTindakanIds = array_map('intval' ,explode(",",$kelompokTindakanIdsTemp));
                    if (isset($advancedFilter['daftartindakan_nama'])) {
                        $query->andFilterWhere([
                            'or',
                            ['ILIKE','daftartindakan_nama', $advancedFilter['daftartindakan_nama']],
                            ['ILIKE', 'kelompoktindakan_nama', $advancedFilter['kelompoktindakan_nama']]
                        ]);
                    }
                    $query->andWhere([ 'in' ,'kelompoktindakan_id' ,$kelompokTindakanIds]);
                    $query->andWhere([ 'is_paketfisio' => false ]);
                    $pagination = false;
                }
            }
            else {
                if (isset($advancedFilter['daftartindakan_nama'])) {
                    $daftartindakan_nama = $advancedFilter['daftartindakan_nama'];
                    $query->andFilterWhere([
                        'or',
                        ['ILIKE','LOWER(daftartindakan_nama)', strtolower($daftartindakan_nama)],
                    ]);
                }
            }
        }
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);
    }

    public function actionDelete() 
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $paketRuangan = PaketRuanganMP::find()->where(['tipepaket_id' => $id])->count();

            if($paketRuangan > 0) {
                $result['status'] = 500;
                $result['text'] = "Tidak bisa menghapus Paket, data sedang digunakan di master lain.";
                // throw new \Exception("Tidak bisa menghapus Paket, data sedang digunakan di master lain.",1);
            }
            else {
                $model = TipePaket::findOne($id);
                if ($model) {
                    $model->is_deleted = true;
                    $model->deleted_date = date('Y-m-d H:i:s');
                    if($model->save(false)) {
                        PaketPelayanan::updateAll([
                            'is_deleted' => true,
                            'deleted_date' => date('Y-m-d H:i:s'),
                        ], 'tipepaket_id = '.$id.'');

                        $transaction->commit();
                        $result = [
                            'status' => 200,
                            'title' => 'Hapus Berhasil',
                            'text' => 'Hapus Tindakan Berhasil',
                        ];
                    }
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

    /**
     * @todo Fungsi untuk melakukan pengecekan transaksi paket
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCekTransaksiPaket()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $data = [];
        $tipePaket = TipePaket::findOne($id);
        if($tipePaket) {
            $model = new TarifTindakan;
            $query = $model::find();
            $data = $query->where(['tipepaket_id' => $id])->asArray()->all();
            
            $count = count($data);
        }

        return [
            'count' => $count,
            'data' => $data
        ];
    }

    public function actionView($id)
    {
        $model = new TipePaket;
        $query = $model::findOne($id);

        $dataMapingTarif = $this->actionCekTransaksiPaket($id);
        $dataMapingTarif = ($dataMapingTarif['count'] == 0) ? false : true;
        return [
            'data' => $query,
            'data_maping' => $dataMapingTarif
        ];
    }

}
?>