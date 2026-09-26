<?php
namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use app\modules\v1\models\AkuntingKodeKategori;
use app\modules\v1\models\AkuntingVendor;
use app\modules\v1\models\AkuntingPayMethod;
use app\modules\v1\models\AkuntingSync;
use app\modules\v1\models\AkuntingPenjamin;
use app\modules\v1\models\AkuntingPayTerm;
use app\modules\v1\models\AkuntingTax;
use app\modules\v1\models\AkuntingBank;
use app\modules\v1\models\PengajuanKlaim;
use app\modules\v1\models\TerimaBayarKlaim;
use app\modules\v1\models\TerimaBayarKlaimDetail;
use app\modules\v1\models\Penjamin;
use Doco\components\DocoConstants;

class SyncAkutansiController extends DocoActiveController
{
    public $modelClass = '';

    public function actionTest()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'message' => 'Sync Akutansi',
            'code' => 200,
        ];
    }

    public function actionSyncDate()
    {
        $response = AkuntingSync::find()->all();

        return $response;
    }

    public function actionCategory()
    {
        $response = AkuntingKodeKategori::find()->all();

        return $response;
    }

    public function actionSupplier()
    {
        $response = AkuntingVendor::find()->all();

        return $response;
    }

    public function actionPayMethod()
    {
        $response = AkuntingPayMethod::find()->all();

        return $response;
    }

    public function actionCustomer()
    {
        $respone = AkuntingPenjamin::find()->all();

        return $respone;
    }

    public function actionGetPayTerm()
    {
        $request = Yii::$app->request;
        $crud = $request->post('crud');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            if($crud == DocoConstants::$crud['CREATE']){
                $this->saveData(DocoConstants::PAYTERM, DocoConstants::$crud['CREATE']);
            } 
            if($crud == DocoConstants::$crud['UPDATE']) {
                $this->saveData(DocoConstants::PAYTERM, DocoConstants::$crud['UPDATE']);
            }
            if($crud == DocoConstants::$crud['DELETE']){
                $this->saveData(DocoConstants::PAYTERM, DocoConstants::$crud['DELETE']);
            }
          
            $transaction->commit();
            return [
                'messages' => 'Success', 
                'status' => 200
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        }
    }

    public function actionGetTax()
    {
        $request = Yii::$app->request;
        $crud = $request->post('crud');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            if($crud == DocoConstants::$crud['CREATE']){
                $this->saveData(DocoConstants::TAX, DocoConstants::$crud['CREATE']);
            } 
            if($crud == DocoConstants::$crud['UPDATE']) {
                $this->saveData(DocoConstants::TAX, DocoConstants::$crud['UPDATE']);
            }
            if($crud == DocoConstants::$crud['DELETE']){
                $this->saveData(DocoConstants::TAX, DocoConstants::$crud['DELETE']);
            }

            $transaction->commit();
            return [
                'messages' => 'Success', 
                'status' => 200
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        }
    }

    public function actionGetBank()
    {
        $request = Yii::$app->request;
        $crud = $request->post('crud');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            if($crud == DocoConstants::$crud['CREATE']){
                $this->saveData(DocoConstants::BANK, DocoConstants::$crud['CREATE']);
            } 
            if($crud == DocoConstants::$crud['UPDATE']) {
                $this->saveData(DocoConstants::BANK, DocoConstants::$crud['UPDATE']);
            }
            if($crud == DocoConstants::$crud['DELETE']){
                $this->saveData(DocoConstants::BANK, DocoConstants::$crud['DELETE']);
            }

            $transaction->commit();
            return [
                'messages' => 'Success', 
                'status' => 200
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        }
    }

    public function actionGetClaim()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $penjamin = Penjamin::find()->where([
            'penjamin_id' => $request->post('penjamin_id')
            ])->one();
        $pengajuan = PengajuanKlaim::find()->where([
            'ILIKE', 'no_pengajuanklaim', $request->post('no_pengajuanklaim')
            ])->one();
        $payment_method = ($request->post('payment_method') == 'cash') ? false : true;
        $transaction = $connection->beginTransaction();
        try{
            $h_bayar = new TerimaBayarKlaim;
            $h_bayar->carabayar_id = $penjamin->carabayar_id;
            $h_bayar->penjamin_id = $request->post('penjamin_id');
            $h_bayar->tgl_terimabayarklaim = $request->post('tgl_terimabayarklaim');
            $h_bayar->no_terimabayarklaim = $request->post('no_terimabayarklaim');
            $h_bayar->total_terimabayar = $request->post('total_terimabayar');
            $h_bayar->pegawaipenerima_id = 1;
            $h_bayar->catatan = $request->post('catatan');
            $h_bayar->is_nontunai = false;
            $h_bayar->pemilik_rekening = $request->post('pemilik_rekening');
            $h_bayar->bank = $request->post('bank');
            $h_bayar->no_rekening = $request->post('no_rekening');
            $h_bayar->is_active = true;
            $h_bayar->save();
            if($h_bayar){
                $detail_bayar = new TerimaBayarKlaimDetail;
                $detail_bayar->terimabayarklaim_id = $h_bayar['terimabayarklaim_id'];
                $detail_bayar->pengajuanklaim_id = $pengajuan['pengajuanklaim_id'];
                $detail_bayar->no_pengajuanklaim = $pengajuan['no_pengajuanklaim'];
                $detail_bayar->total_pengajuan = $pengajuan['total_piutang'];
                $detail_bayar->total_terbayar = 0;
                $detail_bayar->pembayaran = $h_bayar['total_terimabayar'];
                $detail_bayar->total_sisapiutang = $pengajuan['total_piutang'] - $h_bayar['total_terimabayar'];
                $detail_bayar->is_alokasi = false;
                $detail_bayar->is_active = true;
                $detail_bayar->save();
            }
            $transaction->commit();
            return [
                'messages' => 'Success', 
                'status' => 200
            ];
        } catch(\Execption $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        }
    }


    protected function saveData($params, $crud)
    {    
        $request = Yii::$app->request;
        $messages = null;
        
        if($params == DocoConstants::TAX){
            if($crud == DocoConstants::$crud['CREATE']){
                $model = new AkuntingTax;
                $model->pajak_kode = $request->post('id');
                $model->pajak_name = $request->post('name');
                $model->pajak_persen = $request->post('rate');
                $model->additional_data = $request->post('deskripsi');
                $model->is_active = $request->post('enabled');
                $model->save();
            } else {
                try {
                    $kode = $request->post('id');
                    
                    $model = AkuntingTax::find()->where([
                        'pajak_kode' => $kode
                    ])->one();
                        if($crud == DocoConstants::$crud['DELETE']){
                            $model->is_deleted = 1;
                        }  
                    $model->pajak_name = $request->post('name');
                    $model->pajak_persen = $request->post('rate');
                    $model->additional_data = $request->post('deskripsi');
                    $model->is_active = $request->post('enabled');
                    $model->save();
                } catch (Exception $e) {
                    $messages = $e->getMessage();
                }
            }
        }
        if($params == DocoConstants::PAYTERM){
            if($crud == DocoConstants::$crud['CREATE']){
                $model = new AkuntingPayTerm;
                $model->payterm_kode = $request->post('payterm_kode');
                $model->payterm_nama = $request->post('payterm_nama');
                $model->jumlah_hari = $request->post('jumlah_hari');
                $model->additional_data = $request->post('additional_data');
                $model->is_active = $request->post('enabled');
                $model->save();
            } else {
                try{
                    $pay_code = $request->post('payterm_kode');
                    $model = AkuntingPayTerm::find()->where([
                        'payterm_kode' => $pay_code
                    ])->one();
                    if($crud == DocoConstants::$crud['DELETE']){
                        $model->is_deleted = 1;
                    }
                    $model->payterm_nama = $request->post('payterm_nama');
                    $model->jumlah_hari = $request->post('jumlah_hari');
                    $model->additional_data = $request->post('additional_data');
                    $model->is_active = $request->post('enabled');
                    $model->save();  
                } catch (Exception $e) {
                    $messages = $e->getMessage();
                }
            }
        }
        
        if($params == DocoConstants::BANK){
            if($crud == DocoConstants::$crud['CREATE']){
                $model = new AkuntingBank;
                $model->no_rekening = $request->post('no_rek'); // not null
                $model->nama_bank = $request->post('nama_bank'); // not null
                $model->cabang = $request->post('cabang'); // not null
                $model->nama_pemilikrek = $request->post('nama_pemilikrek'); // not null
                $model->is_active = $request->post('is_active'); // not null
                $model->save();
            }
        }

        return [
            'messages' => $messages,
            'msg_error' => $messages
        ];
    } 
}

?>