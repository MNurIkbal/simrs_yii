<?php

/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use app\modules\v1\models\PemesananProduksiObat;
use app\modules\v1\models\PemesananProduksiObatDetail;
use app\modules\v1\models\InfoPemesananProduksiObatView;
use app\modules\v1\models\InfoPemesananProduksiObatDetailView;

class TransaksiPemesananProduksiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PemesananProduksiObat';
    
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
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
        return $actions;
    }

    public function actionSaveRequest()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $user_login = Yii::$app->user->identity->pegawai_id;
            $request = Yii::$app->request;
            $post = $request->post();
            if (isset($post['list_obat'])) {
                $model = new PemesananProduksiObat();
                if ($post['pemesanan_id']) {
                    $model = $model->findOne($post['pemesanan_id']);
                }
                $model->instalasi_id = $post['instalasi_tujuan'];
                $model->ruangan_id = $post['ruangan_tujuan'];
                $model->tglpemesanan = $now;
                $model->pegawaipemesanan_id = $user_login;
                $model->status_pemesanan = DocoConstants::BELUM_VERIFIKASI_PESANAN;
                $model->catatan_bahanbaku = $post['catatan_bahanbaku'];
                $model->created_by = $user_login;

                if ($model->validate() && $model->save()) {
                    $batchData = [];
                    $idParent = $model->pemesananproduksiobat_id;

                    PemesananProduksiObatDetail::updateAll([
                        'is_deleted' => true,
                        'deleted_date' => date("Y-m-d H:i:s"),
                        'deleted_by' => $user_login,
                        'is_active' => false,
                    ], ['pemesananproduksiobat_id' => $idParent]);
                    
                    foreach ($post['list_obat'] as $key => $value) {
                        $additional_data = [
                            'satuan_konversi' => $value['satuan_konversi']
                        ];
                        
                        $batchData[] = [
                            'pemesananproduksiobat_id' => $idParent,
                            'obatalkes_id' => $key,
                            'qty' => $value['qty_besar'],
                            'satuan_id' => $value['satuanbesar_id'],
                            'qty_konversi' => $value['qty_kecil'],
                            'additional_data' => !is_null($value['satuan_konversi']) ? json_encode($additional_data) : null,
                            'created_by' => $user_login
                        ];
                    }

                    PemesananProduksiObatDetail::batchInsert($batchData);
                    $transaction->commit();

                    $getPemesanan = PemesananProduksiObat::findOne($idParent);
                    return ['message' => 'Data Berhasil di simpan', 'nomor'=>isset($getPemesanan['nopemesanan']) ? $getPemesanan['nopemesanan'] : null];
                }
                else {
                    throw new \Exception('Gagal menyimpan data');
                }
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetRequestDetail()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');

        try{
            $requestData = InfoPemesananProduksiObatView::find()
                ->select([
                    'nopemesanan',
                    'tglpemesanan',
                    'instalasi_id',
                    'ruangan_id',
                    'catatan_bahanbaku',
                    'pegawai_pemesanan',
                ])
                ->where(['pemesananproduksiobat_id' => $id])
                ->asArray()->one();

            $obatalkesData = InfoPemesananProduksiObatDetailView::find()
                ->select([
                    'obatalkes_id',
                    'obatalkes_nama',
                    'qty',
                    'satuanunit_id',
                    'satuanunit_nama',
                    'qty_konversi',
                    'satuankecil_id',
                    'satuankecil_nama',
                    'additional_data',
                ])
                ->where(['pemesananproduksiobat_id' => $id])
                ->asArray()->all();
                
            return [
                'request_data' => $requestData,
                'obatalkes_data' => $obatalkesData,
            ];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
        } catch (\yii\db\Exception $e) {
            Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return ['message' => $e->getMessage()];
        }
    }
}
