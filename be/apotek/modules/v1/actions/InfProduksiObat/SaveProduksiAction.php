<?php

namespace app\modules\v1\actions\InfProduksiObat;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\ProduksiObatAlkes;
use app\modules\v1\models\ProduksiObatAlkesDetail;
use app\modules\v1\models\InfoProduksiObatAlkesView;
use app\modules\v1\models\ProduksiObatAlkesBahanBaku;
use app\modules\v1\models\InfoProduksiObatAlkesBahanBakuView;
use app\modules\v1\models\InfoProduksiObatAlkesDetailView;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\ObatAlkes;
use Doco\models\FgetKetersediaanobatFn;
use SirsCore\features\FeatureTindakanBmhp;
use app\modules\v1\models\PemesananProduksiObat;

class SaveProduksiAction extends Action {
    public function run()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $idProduksi = isset($post['data']) ? $post['data'] : null;
        // cek produksiobatalkes_id
        if(empty($idProduksi)){
            return $this->controller->responseJson(422, 'produksiobatalkes_id Tidak boleh kosong', [], ['title' => 'Proses Gagal !']);
        }
        $infoProduksi = InfoProduksiObatAlkesView::find()->where(['produksiobatalkes_id' => $idProduksi])->asArray()->one();
        $pemesanan_id = $infoProduksi['pemesananproduksiobat_id'];
        $pemesanan = PemesananProduksiObat::find()->where(['pemesananproduksiobat_id' => $pemesanan_id])->asArray()->one();
        $ruanganId = isset($pemesanan['ruangan_id']) ? $pemesanan['ruangan_id'] : null;
        $_POST['ruangan_id'] = $ruanganId;
        $detail = ProduksiObatAlkesDetail::find()->where(['produksiobatalkes_id' => $idProduksi])->all();
        $detail_view = InfoProduksiObatAlkesDetailView::find()->where(['produksiobatalkes_id' => $idProduksi])->all();
        $bahan_baku = InfoProduksiObatAlkesBahanBakuView::find()->where(['produksiobatalkes_id' => $idProduksi])->all();
        $model = ProduksiObatAlkes::find()->where(['produksiobatalkes_id' => $idProduksi])->one();
        $tanggal_produksi = isset($post['post']['tgl_produksi']) ? date('Y-m-d H:i:s',strtotime($post['post']['tgl_produksi'])) : date('Y-m-d H:i:s');
        $tanggal_kadaluarsa = isset($post['post']['tgl_kadaluarsa']) ? date('Y-m-d H:i:s',strtotime($post['post']['tgl_kadaluarsa'])) : null;
        $transaction = $connection->beginTransaction();

        try {
            $model->pegawai_approve = $model->pemesananproduksiobat_id;
            $model->tglproduksiobat = $tanggal_produksi;
            $model->status_produksi = DocoConstants::PRODUKSI;
            $model->is_approve = true;
            $model->pegawai_approve = isset($post['post']['pegawai_id']) ? $post['post']['pegawai_id'] : null;

            if($model->validate() && $model->save()) {
                $dataInStok = [];
                $dataOutStok = [];
                $listObatId = ArrayHelper::getColumn($bahan_baku,'obatalkes_id');
                // [1,2,3]
                $listObatAlkesToString = implode(",", $listObatId);
                $getStok = $this->cekKetersediaan($ruanganId, $listObatAlkesToString);
                $validasiStok = ArrayHelper::map($getStok,'obatalkes_id','qty_tersedia');
                // [
                    // '123' => 2
                // ]
                
                if(isset($bahan_baku)){
                    foreach ($bahan_baku as $key => $value){
                        $total_qty_out = isset($value['qty_obat']) ? ceil($value['qty_obat']) : 0;
                        // get dan check ketersedianan obat ruangan
                        if(empty($validasiStok[$value['obatalkes_id']]) || $validasiStok[$value['obatalkes_id']] < $total_qty_out){
                            return $this->controller->responseJson(422, 'Stok Obat tidak mencukupi untuk kebutuhan produksi.<br> No Pesanan '.$infoProduksi['nopemesanan'], [], ['title' => 'Proses Gagal !']);
                        }
                        
                        $dataOutStok[] = [
                            'obatalkes_id' => $value['obatalkes_id'],
                            'qty_satuanpakai' => ceil($total_qty_out),
                            'satuankecil_id' => isset($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                            'harganetto' => empty($value['harganetto_satuan']) ? 0 : $value['harganetto_satuan'],
                            'persendiscount' => 0,
                            'persenppn' => 0,
                            'persenmargin' => 0,
                            'jmldiscount' => 0,
                            'jmlmargin' => 0,
                            'jmlppn' => 0,
                            'ruangan_id' => $ruanganId,
                            'produksiobatalkesbahanbaku_id' => $value['produksiobatalkesbahanbaku_id']
                        ];
                    }
                    $potongStok = FeatureTindakanBmhp::stokObatAlkes($dataOutStok, false);
                }
              
                if(isset($detail_view)){
                    foreach ($detail_view as $key => $value) {
                        $total_in_stok = $value['qty_produksi'];

                        $dataInStok[] = [
                            'tglstok_in' => $tanggal_produksi,
                            'stokoa_aktif' => true,
                            'ruangan_id' => $ruanganId,
                            'obatalkes_id' => $value['obatalkes_id'],
                            'tglkadaluarsa' => $tanggal_kadaluarsa,
                            'qtystok_in' => $total_in_stok,
                            'harganetto' => $value['harganetto_awal'],
                            'satuankecil_id' => $value['satuankecil_id'],
                            'qtystok_out' => 0,
                            'produksiobatalkesdetail_id' => (int) $value['produksiobatalkesdetail_id']
                        ];
                        $updateObat = $this->updateObatAlkes($value['obatalkes_id'],$post['post']['tgl_kadaluarsa'],$post['post']['batch_number']);
                    }
                    StokObatAlkes::batchInsert($dataInStok);
                }
                
                $transaction->commit();
                $response = [
                    'text' => 'Proses Produksi obat telah berhasil. <br>No.Produksi '.$model['noproduksiobat'],
                    'title' => 'Proses berhasil !',
                    'no_produksi' => $model['noproduksiobat']
                ];

                return $response;
            }
            else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->controller->responseJson(422, $e->getMessage(),$e->getLine());
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->controller->responseJson(422, $e->getMessage(),$e->getLine());
        }
    }

    public function cekKetersediaan($ruangan,$obatalkes) {
        $getStok = (new FgetKetersediaanobatFn(['extParam'=>[$ruangan, $obatalkes]]))
                    ->find()->select(['obatalkes_id', 'qty_tersedia'])
                    ->asArray()
                    ->all();

        return $getStok;
    }

    public function updateObatAlkes($obatalkes,$tgl_kadaluarsa,$batch_number) {
        $obatalkes = ObatAlkes::find()->where(['obatalkes_id' => $obatalkes])->one();
        $obatalkes->tglkadaluarsa = date('Y-m-d H:i:s',strtotime($tgl_kadaluarsa));
        $obatalkes->obatalkes_nobatch = $batch_number;
        $obatalkes->save();

        return $obatalkes;
    }
}