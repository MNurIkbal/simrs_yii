<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\ValidasiPoBarang;
use app\modules\v1\models\ValidasiPoBarangDetail;
use app\modules\v1\models\ValidasiPoObat;
use app\modules\v1\models\ValidasiPoObatDetail;
use app\modules\v1\models\InfoPoDetailView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Barang;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\PurchaseRequisitionDetail;
use app\modules\v1\models\PurchaseRequisitionBarangDetail;
use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\LogActivityR;
use app\components\PengadaanComponent;
use app\modules\v1\models\KonfigFarmasi;

class SaveAction extends Action {
    public function run($id, $type_po) {
        $request = Yii::$app->request;
        $info_po = $request->post('info_po');
        $datenow = date('Y-m-d H:i:s');
        $user_login = Yii::$app->user->identity->pegawai_id;
        $alasan_edit = $request->post('history_edit', null);
        $additional_detail = [];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            switch ($type_po) {
                case DocoConstants::JENIS_OBAT:
                    $model = ValidasiPoObat::find()->where([
                        'validasipoobat_id' => $id
                    ])->one();
                    if(!empty($info_po['is_validasi'])) {
                        $model->scenario = ValidasiPoObat::SCENARIO_VALIDASI_PO;
                    }
                    $no_po = $model->no_poobat;
                    $detailTabel = ValidasiPoObatDetail::getTableSchema()->name;
                    $detailItem = InfoPoDetailView::find()->where(['transaksi_id' => $id,'jenis' => $type_po])->asArray()->all();
                    $primaryKeyHeader = 'validasipoobat_id';
                    $primarykey = 'validasipoobatdetail_id';
                    $no_po = 'no_poobat';
                    break;
                case DocoConstants::JENIS_BARANG:
                    $model = ValidasiPoBarang::find()->where([
                        'validasipobarang_id' => $id
                    ])->one();
                    if(!empty($info_po['is_validasi'])) {
                        $model->scenario = ValidasiPoBarang::SCENARIO_VALIDASI_PO;
                    }
                    $no_po = $model->no_pobarang;
                    $detailTabel = ValidasiPoBarangDetail::getTableSchema()->name;
                    $detailItem = InfoPoDetailView::find()->where(['transaksi_id' => $id, 'jenis'=>$type_po])->asArray()->all();
                    $primaryKeyHeader = 'validasipobarang_id';
                    $primarykey = 'validasipobarangdetail_id';
                    $no_po = 'no_pobarang';
                    break;
                default:
                    Yii::$app->response->statusCode = 500;
                    return ['message' => 'Tipe PO tidak boleh kosong'];
                    break;
            }

            $list_data = $info_po['list_data'];
            $list_data = json_decode($list_data,true);
            $sub_total = $total_discount = 0;
            $idParent = $model->{$primaryKeyHeader};
            $supplier_id = $model->supplier_id;
            $arrInsert = $listItem = [];
            $log_edit = null;
            if (is_array($list_data)) {
                foreach ($list_data as $key => $value) {
                    if (!empty($value['id_detail'])) {
                        $harga_satuan   = $value['harga'] ? $value['harga'] : 0;
                        $qty            = $value['qty'] ? $value['qty'] : 0;
                        $diskon         = $value['discount'] ? $value['discount'] : 0;
                        $diskon_rp      = strlen(substr(strrchr($diskon, "."), 1)) > 2 ? $value['discount_rp'] : $qty * $harga_satuan * ($diskon / 100);
                        $total_harga    = ($qty * $harga_satuan) - $diskon_rp;

                        $attributes = [
                            'harga' => $harga_satuan,
                            'discount' => $diskon,
                            'discount_rp' => $diskon_rp,
                            'jumlah' => $total_harga,
                            'qty_input' => $qty,
                            'additional_data' => json_encode([
                                'qty_sekarang' => $qty
                            ]),
                            'is_disc_nominal' => $value['is_disc_nominal']
                        ];
                        if ($value['is_obat']) {
                            $attributes['s_konversiobt_id'] = $value['satuan_id'];
                        } else {
                            $attributes['s_konversibrg_id'] = $value['satuan_id'];
                        }
                        $condition = [
                            $primarykey => $value['id_detail']
                        ];
                        $connection->createCommand()->update($detailTabel, $attributes, $condition)->execute();
                        $log_edit[$value['id_detail']] = $attributes;
                    } else {
                        // insert detail baru
                        list($item_id, $tipe) = explode('-', $value['item_id']);
                        $listItem[] = $value['satuan_id'];
                        $attributes = [
                            'harga' => $value['harga'],
                            'discount' => $value['discount'],
                            'discount_rp' => $value['discount_rp'],
                            'jumlah' => $value['total_harga'],
                            'qty_input' => $value['qty'],
                            'qty_po' => 0,
                            'is_completed' => false,
                        ];
                        if ($tipe == 'B') {
                            $attributes['validasipobarang_id'] = $idParent;
                            $attributes['barang_id'] = $item_id;
                            $attributes['s_konversibrg_id'] = $value['satuan_id'];
                        } else {
                            $attributes['validasipoobat_id'] = $idParent;
                            $attributes['obatalkes_id'] = $item_id;
                            $attributes['s_konversiobt_id'] = $value['satuan_id'];
                        }

                        $arrInsert[$item_id] = $attributes;
                    }
                    $sub_total += $total_harga;
                    $total_discount += $diskon_rp;
                }
                $additional_detail['edit'] = $log_edit;

                // delete obat
                $deleted_exist = false;
                $delCondition = $conditions = $arrUpdate = [];
                $isDeleted = $deletedDate = $deletedBy = $purchasereqdetail_ids = [];
                foreach ($detailItem as $key => $value) {
                    $id_obat = $value['no_pr'] . '-' . $value['obat_barang_id'];
                    if(!isset($list_data[$id_obat])) {
                        $deleted_exist = true;
                        $conditions[] = $value['id_detail'];
                        $isDeleted[] = true;
                        $deletedDate[] = date('Y-m-d H:i:s');
                        $deletedBy[] = Yii::$app->user->identity->pegawai_id;
                        $log_delete[$primarykey] = $value['id_detail'];

                        $pr_field = $value['jenis'] == 'obat' ? 'purchasereqdetail_id' : 'purchasereqbrgdetail_id';
                        $purchasereqdetail_ids[] = null;
                    }
                }

                if($deleted_exist) {
                    $arrUpdate = [
                        $pr_field => $purchasereqdetail_ids
                    ];

                    $delConditions = [
                        $primarykey => $conditions,
                        'is_active' => $conditions,
                    ];

                    $delCondition = [
                        $primarykey => $conditions,
                    ];

                    $validasipod_ids = implode(",", $conditions);

                    if($type_po == DocoConstants::JENIS_OBAT) {
                        $poDetails = ValidasiPoObatDetail::find()->where(['validasipoobatdetail_id'=>$conditions])->all();
                    } else if ($type_po == DocoConstants::JENIS_BARANG) {
                        $poDetails = ValidasiPoBarangDetail::find()->where(['validasipobarangdetail_id'=>$conditions])->all();
                    }

                    foreach ($poDetails as $poDetail) {
                        if($poDetail->getPurchaseRequisitionDetail()->exists()){
                            $purchaseReqDetail = $poDetail->purchaseRequisitionDetail;
                            $purchaseReqDetail->status = DocoConstants::VAR_BELUM_PO;
                            $purchaseReqDetail->update();

                            if($type_po == DocoConstants::JENIS_OBAT) {
                                $countDiff = PurchaseRequisitionDetail::find()
                                    ->where(['purchasereq_id' => $purchaseReqDetail->header->purchasereq_id])
                                    ->select(['status','count(status) as numberofstatus'])
                                    ->andWhere(['<>','status',DocoConstants::VAR_CANCEL_PR])
                                    ->groupBy(['status'])
                                    ->asArray()
                                    ->all();
                            } else {
                                $countDiff = PurchaseRequisitionBarangDetail::find()
                                    ->where(['purchasereqbrg_id' => $purchaseReqDetail->header->purchasereqbrg_id])
                                    ->select(['status','count(status) as numberofstatus'])
                                    ->andWhere(['<>','status',DocoConstants::VAR_CANCEL_PR])
                                    ->groupBy(['status'])
                                    ->asArray()
                                    ->all();
                            }

                            if(count($countDiff) == 1){
                                $status = @$countDiff[0]['status'];
                            }else{
                                $status = DocoConstants::VAR_PO_SEBAGIAN;
                            }

                            $purchaseReqDetail->header->status = $status;
                            $purchaseReqDetail->header->update();
                        }
                    }

                    $deleteObat = PengadaanComponent::batchDelete($detailTabel, $delConditions);
                    $updateDetail = PengadaanComponent::batchUpdate($detailTabel, $arrUpdate, $delCondition);
                    if(!$deleteObat) {
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Gagal menghapus obat',
                        ];
                    }

                    $additional_detail['delete'] = $log_delete;

                    $countPoDetail = InfoPoDetailView::find()->where(['transaksi_id' => $id])->count();
                    if($countPoDetail == 0){
                        $model->status_penerimaan = DocoConstants::STATUS_BATAL_PO;
                        $model->catatan = 'Hapus Semua Item PO';
                        $model->update();
                        $transaction->commit();
                        return [
                            'message' => 'PO berhasil dibatalkan'
                        ];
                    }
                }
            }

            $tgl_rencana = $info_po['tgl_rencanaterima'];
            $model->diorder_oleh = Yii::$app->jwt->user->pegawai_id;
            $model->tgl_rencanaterima = !empty($tgl_rencana) ? date('Y-m-d',strtotime($tgl_rencana)) : null;
            $model->payterm_id = $info_po['payterm_id'];
            $model->pajak_id = $info_po['pajak_id'];
            $model->peg_mengetahui_id = $info_po['peg_mengetahui_id'];
            $model->peg_menyetujui_id = $info_po['peg_menyetujui_id'];
            $model->catatan1 = $info_po['catatan1'];
            $model->catatan2 = $info_po['catatan2'];
            $model->sub_total = $sub_total;
            $model->total_discount = $total_discount;
            $model->supplier_id = $info_po['supplier_id'];

            $pajak = null;
            if ($model->pajak_id) {
                $pajak = Pajak::find()->where([
                    'is_active' => true,
                    'pajak_id' => $model->pajak_id
                ])->one();
            }

            $validasiCount = $this->getCountLogPo($id, $type_po);
            $isValidasiPoMunual = !empty($model->is_manual) && $this->getKonfigAutoValidasi() == false;
            
            $pajakPersen = !empty($pajak->pajak_persen) ? $pajak->pajak_persen : 0;
            $model->ppn_persen = $pajakPersen;
            $model->ppn_nilai = ($pajakPersen / 100) * $model->sub_total;
            $model->total = $model->sub_total + $model->ppn_nilai;
            $is_validasi = ArrayHelper::getValue($info_po, 'is_validasi', false);
            if (!empty($is_validasi)) {
                $model->tgl_validasi = date('Y-m-d H:i:s');
                $model->is_validasi = true;
                $model->peg_validasi_id = $user_login;
            } else {
                $model->tgl_validasi = $isValidasiPoMunual ? null : ($validasiCount ? null : ( empty($model->is_manual) ? null : $model->tgl_validasi) );
                $model->is_validasi = $isValidasiPoMunual ? false : ($validasiCount ? false : ( empty($model->is_manual) ? false : $model->is_validasi) );
                $model->peg_validasi_id = $isValidasiPoMunual ? null : ($validasiCount ? null : ( empty($model->is_manual) ? null : $model->peg_validasi_id) );
            }

            if ($model->save()) {
                if($type_po == DocoConstants::JENIS_OBAT){
                    $no_po = $model->no_poobat;
                    $log_tipe = DocoConstants::LA_TIPE_PO;
                }else if($type_po == DocoConstants::JENIS_BARANG){
                    $no_po = $model->no_pobarang;
                    $log_tipe = DocoConstants::LA_TIPE_PO_NONMEDIS;
                }
                $pegawai_login = PegawaiMasterView::find()
                    ->where(['pegawai_id' => $user_login])
                    ->asArray()
                    ->one();
                $pegawaiNama = ArrayHelper::getValue($pegawai_login, 'nama_pegawai');
                $log = [
                    'transaksi_id' => $id,
                    'tgl' => $datenow,
                    'tipe' => $log_tipe,
                    'aksi' => !empty($is_validasi) && !empty($validasiCount) ? DocoConstants::LA_AKSI_VALIDASI : DocoConstants::LA_AKSI_EDIT,
                    'keterangan' => $pegawaiNama,
                    'alasan' => !empty($is_validasi) && !empty($validasiCount) ? DocoConstants::ALASAN_VALIDASI : $alasan_edit,
                    'additional_detail' => json_encode($additional_detail)
                ];

                if((empty($is_validasi) && !empty($alasan_edit)) || !empty($is_validasi) && !empty($validasiCount)) {
                    $this->saveLog($log);
                }

                $transaction->commit();
                if($is_validasi) {
                    return [
                        'title' => 'Proses Berhasil !',
                        'text' => 'Pesanan berhasil divalidasi dengan no PO ' . $no_po,
                        'no_po' => $no_po
                    ];
                }

                return [
                    'message' => 'Data berhasil disimpan'
                ];
            } else {
                return [
                    'status' => 422,
                    'data' => $model->errors
                ];
            }
        } catch (\yii\db\Exception $e) {
            (new DocoHelpers)->logError($e);
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage(),'line'=>$e->getLine(), 'file' => $e->getFile()];
        } catch (\Exception $e) {
            (new DocoHelpers)->logError($e);
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage(), 'line'=>$e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function saveLog($log) {
        $user_login = Yii::$app->user->identity->pegawai_id;
        $model = new LogActivityR;
        $model->attributes = $log;
        $model->created_by = $user_login;
        if(!$model->save()) {
            throw new \Exception("Tidak Dapat Menyimpan Log Transaksi", 1);
        }
    }

    private function getKonfigAutoValidasi()
    {
        $konfig = KonfigFarmasi::find()->select(['auto_validasi_po_manual'])->asArray()->one();
        return ArrayHelper::getValue($konfig, 'auto_validasi_po_manual', false);
    }

    private function getCountLogPo($id, $type_po) {
        $log_tipe = $type_po == DocoConstants::JENIS_OBAT ? DocoConstants::LA_TIPE_PO : DocoConstants::LA_TIPE_PO_NONMEDIS;
        return LogActivityR::find()->select(['logactivity_id'])
        ->where(['transaksi_id' => $id, 'tipe' => $log_tipe])
        ->andWhere(['<>', 'aksi', DocoConstants::LA_AKSI_TAMBAH])
        ->count();
    }
}
