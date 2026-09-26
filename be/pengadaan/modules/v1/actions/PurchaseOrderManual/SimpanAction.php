<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseOrderManual;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\Barang;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\LogActivityR;
use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\ValidasiPoBarangDetail;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\cache\Cache;

class SimpanAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $detail = $request->post('detail');
        $instalasi_id = $request->post('instalasi_id');
        $primaryKey = $no_po = null;
        try {
            switch ($instalasi_id) {
                case DocoConstants::INSTALASI_GUDANG_UMUM:
                    $model = new ValidasiPoBarang;
                    $primaryKey = 'validasipobarang_id';
                    $no_po = 'no_pobarang';
                    $type = DocoConstants::JENIS_BARANG;
                    $log_tipe = 'PONONMEDIS';
                    break;
                case DocoConstants::INSTALASI_GUDANG_FARMASI:
                    $model = new ValidasiPoObat;
                    $primaryKey = 'validasipoobat_id';
                    $no_po = 'no_poobat';
                    $type = DocoConstants::JENIS_OBAT;
                    $log_tipe = DocoConstants::LA_TIPE_PO;
                    break;
                default:
                    return $this->controller->responseJson(422, 'Gagal membuat PO manual', [
                        'message' => 'Tidak dapat membuat PO manual di instalasi ini. Silakan ubah instalasi.'
                    ]);
                    break;
            }

            $postHeader = $request->post('header');
            $model->attributes = $postHeader;
            $model->pegawai_id = Yii::$app->jwt->user->pegawai_id;
            $model->tgl_rencanaterima = !empty($model->tgl_rencanaterima) ? date('Y-m-d', strtotime($model->tgl_rencanaterima)) : null;
            $model->diorder_oleh = ArrayHelper::getValue($postHeader,'diorder_oleh');
            $model->is_manual = true;
            $pajak = Pajak::findOne($postHeader['pajak_id']);
            $pajak_persen = $pajak->pajak_persen;

            if(Cache::getKonfigAutoValidasi()){
                $model->is_validasi = true;
                $model->tgl_validasi = date('Y-m-d H:i:s');
                $model->peg_validasi_id = Yii::$app->jwt->user->pegawai_id;
            }else{
                $model->is_validasi = false;
            }

            $arrInsert = [];
            $listItem = [];
            $subtotal = $total_diskon = 0;
            foreach ($detail['item_id'] as $details) {
                list($item_id, $tipe) = explode('-', $details['item_id']);
                $listItem[] = $details['satuan_id'];
                $qty = $details['qty'];
                $harga_satuan = $details['harga'];
                $diskon = str_replace(',','.',$details['discount']);
                // $diskon_rp = $qty * $harga_satuan * ($diskon / 100);
                $diskon_rp = $details['discount_rp'];
                $total_harga = ($qty * $harga_satuan) - $diskon_rp;
                $is_disc_nominal = $details['is_disc_nominal'];

                $attributes = [
                    'harga' => $harga_satuan,
                    'discount' => $diskon,
                    'discount_rp' => $diskon_rp,
                    'jumlah' => $total_harga,
                    'qty_input' => $qty,
                    'qty_po' => 0,
                    'is_completed' => false,
                    'is_disc_nominal' => $is_disc_nominal
                ];

                if ($tipe == 'B') {
                    $attributes['barang_id'] = $item_id;
                    $attributes['s_konversibrg_id'] = $details['satuan_id'];
                } else {
                    $attributes['obatalkes_id'] = $item_id;
                    $attributes['s_konversiobt_id'] = $details['satuan_id'];
                }

                $arrInsert[$item_id] = $attributes;
                $subtotal += $total_harga;
                $total_diskon += $diskon_rp;
            }

            $ppn_nilai = $subtotal * ($pajak_persen / 100);
            $grand_total = $subtotal + $ppn_nilai;
            $model->ppn_persen = $pajak_persen;
            $model->ppn_nilai = $ppn_nilai;
            $model->sub_total = $subtotal;
            $model->total_discount = $total_diskon;
            $model->total = $grand_total;

            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            if ($model->save()) {
                $idParent = $model->{$primaryKey};
                foreach($arrInsert as $key => $val) {
                    if ($tipe == 'B') {
                        $arrInsert[$key]['validasipobarang_id'] = $idParent;
                    } else {
                        $arrInsert[$key]['validasipoobat_id'] = $idParent;
                    }
                }

                $idSatuan = implode(",", $listItem);
                $supplier_id = $model->supplier_id;
                switch ($instalasi_id) {
                    case DocoConstants::INSTALASI_GUDANG_UMUM:
                        $konversi = $connection->createCommand("
                            SELECT barang_id, nilai_konversi
                                FROM satuankonversibrg_m
                                WHERE satuankonversibrg_id IN ({$idSatuan})
                        ")->queryAll();
                        $listBarang = [];
                        foreach ($konversi as $value) {
                            if (isset($arrInsert[$value['barang_id']])) {
                                $listBarang[] = $value['barang_id'];
                                $arrInsert[$value['barang_id']]['qty_po'] = $arrInsert[$value['barang_id']]['qty_input'];
                            }
                        }

                        ValidasiPoBarangDetail::batchInsert($arrInsert);
                        Barang::updateAll([
                            'supplier_id' => $supplier_id
                        ],[
                            'barang_id' => $listBarang
                        ]);
                        break;
                    case DocoConstants::INSTALASI_GUDANG_FARMASI:
                        $konversi = $connection->createCommand("
                            SELECT obatalkes_id, nilai_konversi
                                FROM satuankonversi_m
                                WHERE satuankonversi_id IN ({$idSatuan})
                        ")->queryAll();
                        $listObat = [];
                        foreach ($konversi as $value) {
                            if (isset($arrInsert[$value['obatalkes_id']])) {
                                $listObat[] = $value['obatalkes_id'];
                                $arrInsert[$value['obatalkes_id']]['qty_po'] = $arrInsert[$value['obatalkes_id']]['qty_input'];
                            }
                        }

                        ValidasiPoObatDetail::batchInsert($arrInsert);
                        ObatAlkes::updateAll([
                            'supplier_id' => $supplier_id
                        ],[
                            'obatalkes_id' => $listObat
                        ]);
                        break;
                    default:
                        break;
                }
                
                $logPO = new LogActivityR;
                $logPO->attributes = [
                    'transaksi_id' => $model->getPrimaryKey(),
                    'tgl' => date('Y-m-d H:i:s'),
                    'tipe' => $log_tipe,
                    'aksi' => DocoConstants::LA_AKSI_TAMBAH,
                    'keterangan' => 'POMANUAL',
                    'alasan' => null,
                    'additional_detail' => null,
                    'created_by' => Yii::$app->user->identity->pegawai_id
                ];

                $logPO->save(false);
                
                $transaction->commit();

                $po = $model::find()->where([
                    $primaryKey => $idParent
                ])->one();

                $response = [
                    'text' => 'PO Manual berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'no_pomanual' => $po->{$no_po},
                    'no_transaksi' => DocoHelpers::encrypt($idParent),
                    'type' => DocoHelpers::encrypt($type)
                ];
            } else {
                return $this->controller->responseJson(422, 'Gagal membuat PO manual', $model->errors);
            }

            return $this->controller->responseJson(200, 'PO manual berhasil disimpan', [
                'response' => $response
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return $this->controller->responseJson(500, $e->getMessage(), [
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->controller->responseJson(500, $e->getMessage(), [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}
