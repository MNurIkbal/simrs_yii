<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\Ambulan;
use app\modules\v1\models\Barang;
use app\modules\v1\models\AmbulanDetail;
use app\modules\v1\models\AmbulanView;
use app\modules\v1\models\AmbulanDetailView;
use app\modules\v1\models\InfoObatAlkesView;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PesanAmbulan;

class AmbulanceController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Ambulan';
    protected $_tersedia = 591; // id lookup tersedia
    
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
        // unset($actions['create']);
        // unset($actions['update']);
        // unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new AmbulanView;
            $query = $model->find();

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['no_polisi'])) {
                    $no_polisi = $advancedFilter['no_polisi'];
                    $query->andWhere(['ILIKE', 'no_polisi', $no_polisi]);
                }
                if(isset($advancedFilter['is_emergency'])) {
                    $is_emergency = $advancedFilter['is_emergency'];
                    $query->andWhere(['is_emergency' => $is_emergency]);
                }
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

    public function actionView($id)
    {
        $header = AmbulanView::find()->where(['ambulan_id' => $id])->one();
        $tindakan = AmbulanDetailView::find()
            ->where(['ambulan_id' => $id])
            ->andWhere(['IS NOT', 'daftartindakan_nama', null])
            ->all();

        $obat = AmbulanDetailView::find()
            ->where(['ambulan_id' => $id])
            ->andWhere(['IS NOT', 'obatalkes_nama', null])
            ->all();

        return [
            'header' => $header,
            'tindakan' => $tindakan,
            'obat' => $obat,
        ];
    }

    public function actionGetTindakan($daftartindakan_id)
    {
        $query = DaftarTindakan::findOne($daftartindakan_id);

        return $query;
    }

    public function actionGetObat($obatalkes_id)
    {
        $query = InfoObatAlkesView::find()->where(['obatalkes_id' => $obatalkes_id])->one();

        return $query;
    }

    public function actionGetMerek($barang_id)
    {
        $query = Barang::find()->where(['barang_id' => $barang_id])->one();

        return $query;
    }

    public function actionGenerateApi()
    {
        $barang = Barang::find()->where(['kelompokbarang_id' => 1])->all();
        $jenisAmbulan = [
            DocoConstants::EMERGENCY => DocoConstants::EMERGENCY,
            DocoConstants::NON_EMERGENCY => DocoConstants::NON_EMERGENCY,
        ];

        return [
            'jenisAmbulan' => $jenisAmbulan,
            'barang' => $barang,
        ];
    }

    /**
    * @controller actionPrint 
    * @attribute #table# => table
    * @attribute #title# => title
    * @attribute #barang_nama# => barang_nama
    * @attribute #no_polisi# => no_polisi
    * @attribute #is_emergency# => is_emergency
    * @attribute #barang_merk# => barang_merk
    * @attribute #keterangan# => keterangan
    * @attribute #tanggal# => tanggal
    **/
    public function actionPrint()
    {
        $title = 'Detail Ambulan';
        try{
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $query =  $this->actionView($id);
            $header = $query['header'];

            $print = new DocoPrint();
            $print->attributes = [
                '#title#' => $title,
                '#barang_nama#' => $header['barang_nama'],
                '#no_polisi#' => $this->renderPartial('_nomor_polisi',[
                    'noPolisi' => $header['no_polisi']
                ]),
                // '#no_polisi#' => $header['no_polisi'],
                '#is_emergency#' => $header['is_emergency'],
                '#barang_merk#' => $header['barang_merk'],
                '#keterangan#' => $header['keterangan'],
                '#tanggal#' => date('d M Y'),
                '#table#' => $this->renderPartial('_print',[
                    'tindakan' => $query['tindakan'],
                    'obat' => $query['obat'],
                ]),
            ];
            $print->Output();
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $model = new Ambulan;
        try {
            $dataJsonTindakan = $request->post('cacheTindakan',"{}");
            $dataTindakan = json_decode($dataJsonTindakan,true);
            $dataJsonObat = $request->post('cacheObat',"{}");
            $dataObat = json_decode($dataJsonObat,true);
            $postData = $post['data'];
            $model->barang_id = $postData['barang_id'];
            $model->no_polisi = $postData['no_polisi'];
            $model->is_emergency = $postData['is_emergency'];
            $model->keterangan = $postData['keterangan'];
            $model->status_ambulan = DocoConstants::AMBULAN_IDLE;

            if($model->validate() && $model->save()) {
                $idParent = $model->ambulan_id;
                $insert = [];
                if(is_array($dataTindakan)) {
                    foreach ($dataTindakan as $key => $value) {
                        $insert[] = [
                            'ambulan_id' => $idParent,
                            'daftartindakan_id' => $value['daftartindakan_id'],
                            'obatalkes_id' => null,
                            'qty' => null,
                            // 'is_default' => !empty($value['biaya_tetap']) ? true : false,
                            'is_default' => ($value['is_default'] == 1 ) ? true : false,
                        ];
                    }
                }

                if(is_array($dataObat)) {
                    foreach ($dataObat as $key => $value) {
                        $insert[] = [
                            'ambulan_id' => $idParent,
                            'obatalkes_id' => $value['obatalkes_id'],
                            'qty' => (int) $value['qty'],
                            'daftartindakan_id' => null,
                            'is_default' => false,
                        ];
                    }
                }

                if($insert) {
                    AmbulanDetail::batchInsert($insert);
                }

                $transaction->commit();
                $response = [
                    'text' => 'Ambulan berhasil disimpan',
                    'title' => 'Proses berhasil !',
                ];
                
                return $response;
            } 
            else {
                return [
                    'status' => 422,
                    'data' => $model->errors,
                ];
            }
            
        }
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSaveUpdate()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $id = $post['ambulan_id'];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $model = Ambulan::findOne($id);
        
        try {
            $dataJsonTindakan = $request->post('cacheTindakan',"{}");
            $dataTindakan = json_decode($dataJsonTindakan,true);

            $dataJsonObat = $request->post('cacheObat',"{}");
            $dataObat = json_decode($dataJsonObat,true);

            $postData = $request->post('data',"{}");
            $postData = json_decode($postData,true);
            
            $model->ambulan_id = $id;
            $model->barang_id = $postData['barang_id'];
            $model->no_polisi = $postData['no_polisi'];
            $model->is_emergency = $postData['is_emergency'];
            $model->keterangan = $postData['keterangan'];
            $model->status_ambulan = DocoConstants::AMBULAN_IDLE;
            if($model->validate() && $model->save()) {
                $idParent = $id;
                $insert = [];
                AmbulanDetail::deleteAll(['ambulan_id' => $idParent]);
                if (is_array($dataTindakan)) {
                    foreach ($dataTindakan as $key => $value) {
                        $insert[] = [
                            'ambulan_id' => $idParent,
                            'daftartindakan_id' => $value['daftartindakan_id'],
                            'obatalkes_id' => null,
                            'qty' => null,
                            // 'is_default' => !empty($value['biaya_tetap']) ? true : false,
                            'is_default' => ($value['is_default'] == 1 ) ? true : false,
                        ];
                    }
                }

                if (is_array($dataObat)) {
                    foreach ($dataObat as $key => $value) {
                        $insert[] = [
                            'ambulan_id' => $idParent,
                            'obatalkes_id' => $value['obatalkes_id'],
                            'qty' => (int) $value['qty'],
                            'daftartindakan_id' => null,
                            'is_default' => false,
                        ];
                    }
                }

                
                if($insert) {
                    AmbulanDetail::batchInsert($insert,false);
                }

                $transaction->commit();
                $response = [
                    'text' => 'Ambulan berhasil disimpan',
                    'title' => 'Proses berhasil !',
                ];
                
                return $response;
            } 
            else {
                return [
                    'status' => 422,
                    'data' => $model->errors,
                ];
            }
            
        }
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        $title = Yii::t('app', 'Master Ambulan');
        $header = array();
        $footer = array();
        $model = new AmbulanView;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $no_polisi = '';
        $is_emergency = '';
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['no_polisi'])) {
                $no_polisi = $advancedFilters['no_polisi'];
                // $query->andWhere(['ILIKE', 'no_polisi', $no_polisi]);
            }
            if(isset($advancedFilters['is_emergency'])) {
                $is_emergency = $advancedFilters['is_emergency'];
                // $query->andWhere(['is_emergency' => $is_emergency]);
            }
        }
        // $query->orderBy(['no_polisi' => SORT_ASC]);

        $header = [Yii::t('app', 'Nomor Polisi') => $no_polisi, Yii::t('app', 'Jenis Ambulan') => $is_emergency];
        $result = [];
        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Nomor Polisi')] = $value['no_polisi'];
            $newValue[\Yii::t('app', 'Jenis Ambulan')] = $value['is_emergency'];
            $newValue[\Yii::t('app', 'Merek')] = $value['barang_merk'];
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? "Aktif" : "Tidak Aktif";
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode tanggal
    * @attribute #tanggal# => tanggal sekarang
    * @attribute #title# => title
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $title = 'Ambulan ';
        $model = new AmbulanView;
        $query = $model::find();
        
        $no_polisi = '';
        $is_emergency = '';
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['no_polisi'])) {
                $no_polisi = $advancedFilters['no_polisi'];
                $query->andWhere(['ILIKE', 'no_polisi', $no_polisi]);
            }
            if(isset($advancedFilters['is_emergency'])) {
                $is_emergency = $advancedFilters['is_emergency'];
                $query->andWhere(['is_emergency' => $is_emergency]);
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

    private function checkDataAmbulan($id){
        try {
            $model = new AmbulanView;
            $data = $model::find()
                            ->where(['ambulan_id'=>$id])
                            ->andWhere(['NOT',[ 'status_ambulan' => $this->_tersedia ] ]) 
                            ->count();
            return $data;
        } catch (Exception $e) {
            return [];
        }
    }

    private function checkAmbulanTransaksi($id){
        try {
            $modelPesanAmbulan = new PesanAmbulan;
            $getDataPesanAmbulan = $modelPesanAmbulan::find()
                                                    ->where(['ambulan_id'=>$id])
                                                    ->andWhere(['NOT',[ 'status_pesan' => $this->_tersedia ] ]) 
                                                    ->count();
            return $getDataPesanAmbulan;
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function actionDeleteAmbulan()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $request = Yii::$app->request;
            $model = Ambulan::findOne($id);
            $checkAmbulan = $this->checkAmbulanTransaksi($id);
            if($checkAmbulan > 0){
                return $response['response'] = [
                            'title' => 'Proses Hapus Gagal !',
                            'text' => 'Ambulan ini Tidak bisa di hapus.',
                            'status' => 422
                       ];
            }else{
                if ($model->delete()) {
                    return $response['response'] = [
                            'title' => 'Proses Hapus Berhasil !',
                            'text' => 'Data berhasil dihapus',
                       ];
                } else {
                    return $response['response'] = [
                            'title' => 'Proses Hapus Gagal !',
                            'text' => 'Data Gagal di hapus',
                            'status' => 422
                       ];
                }                
            }
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUbahStatusAmbulan()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $status = $request->get('is_active');
        try {
            $request = Yii::$app->request;
            $model = Ambulan::findOne($id);
            $checkAmbulan = $this->checkDataAmbulan($id);
            if($checkAmbulan > 0){
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Ambulan ini sedang dipakai',
                            'status' => 422
                       ];
            }else{
                $model->is_active = $status;
                if ($model->update()) {
                    return $response['response'] = [
                            'title' => 'Proses Berhasil !',
                            'text' => 'Status berhasil diubah',
                       ];
                } else {
                    return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Status gagal di ubah',
                            'status' => 422
                       ];
                }                
            }
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeleteTindakanLama()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $id = $request->get('id');
        try {
            $model = AmbulanDetail::findOne($id);
            $model->is_deleted = true;
            if ($model->save()) {
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Hapus Berhasil',
                    'text' => 'Hapus Tarif Berhasil',
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

    public function actionDeleteObatLama()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $id = $request->get('id');
        try {
            $model = AmbulanDetail::findOne($id);
            $model->is_deleted = true;
            if ($model->save()) {
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Hapus Berhasil',
                    'text' => 'Hapus Obat Alkes Berhasil',
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
}