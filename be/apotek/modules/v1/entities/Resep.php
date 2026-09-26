<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use app\components\ApotekComponent;
use app\modules\v1\payloads\ResepturPayload;
use app\modules\v1\payloads\ResepturDetailPayload;
use app\modules\v1\entities\ObatAlkesPasien;
use app\modules\v1\entities\PenjualanResep;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\PenjualanResep as PenjualanResepModel;
use app\modules\v1\models\Racikan;
use app\modules\v1\models\ObatAlkesPasien as ObatAlkesPasienModel;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\StokObatAlkesR;
use app\modules\v1\models\InformasiResepturView;
use Doco\Services\PlafonBpjsService;

class Resep {
    protected $_resep;
    protected $_modelResep;

    public function __get($name)
    {
        if (array_key_exists($name, $this->_resep)) {
            return $this->_resep[$name];
        }

        return null;
    }

    public function loadById($penjualanresep_id) {
        $resep = PenjualanResepModel::find()->where(['penjualanresep_id' => $penjualanresep_id])->asArray()->one();
        if(empty($resep)) throw new \Exception("Error Processing Request", 1);

        $this->_resep = $resep;
        return $this;
    }

    public function updatePenjualan($penjualanresep_id) {
        $this->_modelResep->status_reseptur = DocoConstants::VAR_AR;
        $this->_modelResep->penjualanresep_id = $penjualanresep_id;
        if(!$this->_modelResep->save()) throw new \Exception("Error Processing Request", 1);
    }

    public function updateTagihan($id, $inputHeader){
        $modelPenjualan = PenjualanResepModel::find()->where(['penjualanresep_id' => $id])->one();
        $modelPenjualan->attributes = $inputHeader;
        if(!$modelPenjualan->save()) throw new \Exception("Error Processing Request Penjualan Resep", 1);
        return true;
    }

    public function deleteOrAddDetail($inputListObat, $inputHeader) {
        // update header reseptur
        if(empty($this->_resep)) throw new \Exception("Error Processing Request1", 1);
        $model = PenjualanResepModel::findOne($this->_resep['penjualanresep_id']);
        $ruangan_id = $model->ruangan_id;
        $model->biayaadministrasi = $inputHeader['biaya_administrasi'];
        if(!$model->save()) throw new \Exception("Error Processing Request Resep", 1);

        foreach ($inputListObat as $_input) {
            if(is_int($_input['det']) && $_input['det'] > $_input['qty']){
                throw new \Exception("det tidak boleh melebihi qty", 1);
            }
        }

        //get detail resep
        $resepDetails = ObatAlkesPasienModel::find()->where([
            'penjualanresep_id' => $this->_resep['penjualanresep_id']
        ])->asArray()->all();

        $resepDetail = [];
        foreach ($resepDetails as $_valueResepDetail) {
            $resepDetail[$_valueResepDetail['obatalkespasien_id']] = $_valueResepDetail;
        }

        //separate existing resep & obat baru ditambahkan
        $currentObat = $newObat = [];
        foreach ($inputListObat as $_detailList) {
            if(isset($_detailList['obatalkespasien_id']) && !empty($_detailList['obatalkespasien_id'])){
                $det = !empty($_detailList['det']) ? (float) $_detailList['det'] : 0;
                $_detailList['det_medis'] = $det;
                $_det_rounded = ceil($det);
                $_detailList['det'] = $_det_rounded;
                $_detailList['det_medis'] = $det;
                $currentObat[$_detailList['obatalkespasien_id']] = $_detailList;
            }else{
                if(!$_detailList['is_deleted']){
                    $newObat[] = $_detailList;
                }
            }
        }

        $processedTime = date('Y-m-d H:i:s');
        $userLogin = Yii::$app->user->identity->pegawai_id;
        foreach ($resepDetail as $_id => $_val) {
            if(isset($currentObat[$_id]) && $currentObat[$_id]['is_deleted'] == TRUE){
                if($model->status_bayar == DocoConstants::LUNAS) {
                    throw new \Exception("Resep sudah dibayar. Tidak bisa melakukan edit resep.1", 422);
                }
                $update_detail = ObatAlkesPasienModel::findOne($_id);
                $update_detail->is_deleted = TRUE;
                $update_detail->is_active = FALSE;
                $update_detail->deleted_date = $processedTime;
                $update_detail->deleted_by = $userLogin;
                if(!$update_detail->save()) throw new \Exception("Gagal hapus detail resep", 1);
                $this->updateInfoStok($currentObat[$_id], $ruangan_id, 'delete');
            }
        }

        $obatResep = $new_stok = [];
        $index = 0;
        foreach ($currentObat as $key => $_currObat) {
            if($_currObat['is_deleted'] == TRUE) continue;
            $update_curdetail = ObatAlkesPasienModel::findOne($_currObat['obatalkespasien_id']);
            $additional_data = json_decode($update_curdetail->additional_data, true);
            $nilai_konversi = floatval($additional_data['nilai_konversi']);
            $old_det = $update_curdetail->det;
            $update_curdetail->signa_oa = $_currObat['signa_id'];
            $update_curdetail->det = $_currObat['det'];
            $update_curdetail->det_konversi = floatval($_currObat['det']) * $nilai_konversi;
            $update_curdetail->det_medis = $_currObat['det_medis'];
            $update_curdetail->biayaadministrasi = $inputHeader['biayaadministrasi'];
            $update_curdetail->harganetto_oa = $_currObat['harganetto'] * $_currObat['det'];
            $update_curdetail->hargajual_oa = $_currObat['hargajual'] * $_currObat['det'];
            $update_curdetail->hargasatuan_oa = $_currObat['hargajual'];
            $update_curdetail->is_kronis = $_currObat['is_kronis'];

            $dataSigna = json_encode(['id' => $_currObat['signa_id'], 'text' => $_currObat['signa'], 'kode' => null]);
            $query = 
                "UPDATE obatalkespasien_t 
                SET signa = '{$dataSigna}' 
                WHERE obatalkespasien_id = {$_currObat['obatalkespasien_id']} AND is_deleted = false;";
            \Yii::$app->db->createCommand($query)->execute();

            if(!$update_curdetail->save()) throw new \Exception("Gagal Update DET", 1);

            $obatResep[$key] = $_currObat;
            $obatResep[$key]['qty_konversi'] = $update_curdetail->qty_konversi;
            $obatResep[$key]['det_konversi'] = $update_curdetail->det_konversi;

            if(!isset($new_stok[$_currObat['obatalkes_id']])) {
                $new_stok[$_currObat['obatalkes_id']]['old_det'] = $old_det * $nilai_konversi;
                $new_stok[$_currObat['obatalkes_id']]['det'] = $_currObat['det'];
                $new_stok[$_currObat['obatalkes_id']]['det_konversi'] = $_currObat['det'] * $nilai_konversi;
                $new_stok[$_currObat['obatalkes_id']]['qty_konversi'] = $update_curdetail->qty_konversi;
                $arr_ruangan_id[] = $ruangan_id;
            } else {
                $new_stok[$_currObat['obatalkes_id']]['old_det'] += $old_det * $nilai_konversi;
                $new_stok[$_currObat['obatalkes_id']]['det'] += $_currObat['det'];
                $new_stok[$_currObat['obatalkes_id']]['det_konversi'] += $_currObat['det'] * $nilai_konversi;
                $new_stok[$_currObat['obatalkes_id']]['qty_konversi'] += $update_curdetail->qty_konversi;
            }
        }

        if(!empty($new_stok)) {
            $arr_obatalkes_id = array_keys($new_stok);
            $inCondition = "(" . implode(", ", $arr_obatalkes_id) . ")";
            $str = "SELECT
                        qty_dipesan, qty_tersedia, obatalkes_id
                    FROM stokobatalkes_r
                    WHERE obatalkes_id IN $inCondition
                        AND ruangan_id = $ruangan_id";

            $stok_obat = Yii::$app->db->createCommand($str)->queryAll();
            foreach ($stok_obat as $value) {
                $pk = $value['obatalkes_id'];

                if($value['qty_dipesan'] == 0) {
                    $qty_transaksi = $value['qty_dipesan'] - $new_stok[$pk]['det_konversi'];
                } else {
                    if(!empty($new_stok[$pk]['old_det'])) {
                        $qty_transaksi = $new_stok[$pk]['old_det'] - $new_stok[$pk]['det_konversi'];
                    } else {
                        $qty_transaksi = $new_stok[$pk]['qty_konversi'] - $new_stok[$pk]['det_konversi'];
                    }
                }

                $qty_dipesan[$pk] = $value['qty_dipesan'] - $qty_transaksi;
                $qty_tersedia[$pk] = $value['qty_tersedia'] + $qty_transaksi;
            }

            foreach ($arr_obatalkes_id as $key => $value) {
                $arr_qty_dipesan[] = $qty_dipesan[$value];
                $arr_qty_tersedia[] = $qty_tersedia[$value];
            }

            if(count($arr_qty_dipesan) > 0) {
                $obat_dipesan = [
                    'qty_dipesan' => $arr_qty_dipesan,
                    'qty_tersedia' => $arr_qty_tersedia
                ];

                $updateCondition = [
                    'obatalkes_id' => $arr_obatalkes_id,
                    'ruangan_id' => $arr_ruangan_id
                ];

                $updateStokDipesan = ApotekComponent::updateMultiple('stokobatalkes_r', $obat_dipesan, $updateCondition);
                if(!$updateStokDipesan) {
                    throw new \Exception("Gagal update stok dipesan", 1);
                }
            }
        }

        $signaObat = [];
        $rawSigna = SignaObat::find()->asArray()->all();
        if(count($rawSigna)>0)
            $signaObat = array_column($rawSigna, 'signa_nama','signa_id');

        $newResepDetail = [];
        $totalTarif = 0;
        foreach ($newObat as $_newObat) {
            $qty_rounded = ceil($_newObat['qty']);
            $nilai_konversi = isset($_newObat['nilai_konversi']) ? $_newObat['nilai_konversi'] : 1;
            $qty_konversi_rounded = $qty_rounded * $nilai_konversi;

            $newResepDetail[] = [
                'obatalkes_id' => $_newObat['obatalkes_id'],
                'racikan_id' => $_newObat['racikan_id'],
                'satuankecil_id' => $_newObat['satuankecil_id'],
                'penjualanresep_id' => $this->_resep['penjualanresep_id'],
                'r' => !empty($_newObat['racikan_id']) && $_newObat['racikan_id'] == 1 ? 'r' : null,
                'rke' => is_int($_newObat['r_ke']) ? $_newObat['r_ke'] : null,
                'kekuatan_oa' => null,
                'satuankekuatan' => null,
                'etiket' => @$_newObat['catatan'],
                'iter' => null,
                'signa_id' => isset($signaObat[$_newObat['signa_id']]) && $_newObat['signa'] != $_newObat['signa_id'] ? $_newObat['signa_id'] : 0,
                'signa' => isset($signaObat[$_newObat['signa_id']]) && $_newObat['signa'] != $_newObat['signa_id'] ? json_encode(['id'=>$_newObat['signa_id'],'text'=>$_newObat['signa']]) : json_encode(['text'=>$_newObat['signa']]),
                'status_implementasi' => 454,
                'tglpelayanan' => date('Y-m-d H:i:s'),
                'qty_konversi' => floatval($qty_konversi_rounded),
                'qty_medis' => floatval($_newObat['qty']),
                'det_medis' => floatval($_newObat['det']),
                'det' => floatval(ceil($_newObat['det'])),
                'is_kronis' => $_newObat['is_kronis'], 
                'det_konversi' => floatval(ceil($_newObat['det'])) * @floatval($_newObat['nilai_konversi']),
                'additional_data' => json_encode([
                    "satuaninput_id" => @$_newObat['satuaninput_id'],
                    "satuan_input" => @$_newObat['satuan_input'],
                    "satuankonversi_id" => @$_newObat['satuankonversi_id'],
                    "satuan_konversi" => @$_newObat['satuan_konversi'],
                    "harga_konversi" => @$_newObat['harga_konversi'],
                    "qty_input" => @$_newObat['qty'],
                    "posisi" => @$_newObat['posisi'],
                    "nilai_konversi" => @$_newObat['nilai_konversi']
                ]),

                // kebutuhan obatalkespasien_t
                'qty_oa' => floatval($qty_rounded),
                'hargajual_oa' => empty($_newObat['subtotal']) ? 0 : $_newObat['subtotal'],
                'harganetto_oa' => empty($_newObat['harganetto']) ? 0 : $_newObat['harganetto'],
                'hargasatuan_oa' => empty($_newObat['harga']) ? 0 : $_newObat['harga'],
                'signa_oa' => isset($signaObat[$_newObat['signa_id']]) && $_newObat['signa'] != $_newObat['signa_id'] ? $_newObat['signa_id'] : 0,

                'ruangan_id' => $ruangan_id,
                'penjamin_id' => ArrayHelper::getValue($model,'penjamin_id'),
                'carabayar_id' => ArrayHelper::getValue($model,'carabayar_id'),
                'kelaspelayanan_id' => ArrayHelper::getValue($model,'kelaspelayanan_id'),
                'pendaftaran_id' => ArrayHelper::getValue($model,'pendaftaran_id'),
                'pegawai_id' => ArrayHelper::getValue($model,'pegawai_id'),
                'pasien_id' => ArrayHelper::getValue($model,'pasien_id'),
                'nama_racikan' => !empty($_newObat['nama_racikan']) ? $_newObat['nama_racikan'] : null,
                'qty_racikan' => !empty($_newObat['qty_racikan']) ? $_newObat['qty_racikan'] : null,
                'satuan_racikan_id' => !empty($_newObat['satuan_racikan_id']) ? $_newObat['satuan_racikan_id'] : null,
            ];

            $totalTarif += empty($_newObat['subtotal']) ? 0 : $_newObat['subtotal'];
            $this->updateInfoStok($_newObat, $ruangan_id, 'add');
        }
        
        $pendaftaranId = ArrayHelper::getValue($this->_resep, 'pendaftaran_id');
        $validasiPlafon = new PlafonBpjsService($pendaftaranId, $totalTarif);
        $result = $validasiPlafon->validasiPlafon();
        if (!$result['isValid']) {
            $message = $result['message'] ? $result['message'] : 'Validasi Plafon Gagal';
            throw new \Exception($message, 1);
        }
        if(count($newResepDetail)>0) {
            if($model->status_bayar == DocoConstants::LUNAS) {
                throw new \Exception("Resep sudah dibayar. Tidak bisa melakukan edit resep.3", 422);
            }

            ObatAlkesPasienModel::batchInsert($newResepDetail);
        }

        return [
            'obatReseptur'  => $obatResep,
            'newObat'       => $newResepDetail
        ];
    }

    private function updateInfoStok($data_obat, $ruangan_id, $tipe) {
        $model = StokObatAlkesR::find()->where([
            'obatalkes_id' => $data_obat['obatalkes_id'],
            'ruangan_id' => $ruangan_id
        ])->one();

        $nilai_konversi = isset($data_obat['nilai_konversi']) ? $data_obat['nilai_konversi'] : null;
        $qty_transaksi = !is_null($data_obat['det']) ? $data_obat['det'] * $nilai_konversi : $data_obat['qty_konversi'];
        if($tipe == 'delete') {
            $model->qty_dipesan = $model->qty_dipesan - $qty_transaksi;
            $model->qty_tersedia = $model->qty_tersedia + $qty_transaksi;
        } else if($tipe == 'add') {
            $model->qty_dipesan = $model->qty_dipesan + $qty_transaksi;
            $model->qty_tersedia = $model->qty_tersedia - $qty_transaksi;
        }

        if(!$model->save(false)) throw new \Exception("Gagal update stokobatalkes_r saat hapus obat", 1);
    }
}
