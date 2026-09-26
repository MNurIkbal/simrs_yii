<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use Yii;
use SirsCore\models\ObatAlkesFn;
use Doco\models\InfoStokObatAlkesFnr;
use Doco\models\InfoStokObatAlkesFnrNew;
use app\modules\v1\payloads\ObatAlkesPasienPayload;
use app\modules\v1\models\ObatAlkesPasien as ObatAlkesPasienModel;
use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\InfoResepView;
use app\components\ApotekComponent;
use Exception;
use yii\helpers\ArrayHelper;

class ObatAlkesPasien
{
    protected $_infoResep;
    protected $_oa;

    public function loadByResepturData($reseptur, $pendaftaran = [])
    {
        $now = date('Y-m-d H:i:s');
        $listInfoObatAlkes = $reseptur->listHargaObatAlkes;
        $reseptur_detail   = $reseptur->details;
        $trx_details = [];
        foreach ($reseptur_detail as $value) {
            $infoObatAlkes   = isset($listInfoObatAlkes[$value['obatalkes_id']]) ? $listInfoObatAlkes[$value['obatalkes_id']] : [];
            $additional_data = !empty($value['additional_data'])? json_decode($value['additional_data'],true) : [];
            if(isset($value['qty_reseptur'])) {
                $qty = $value['qty_reseptur'];
                $hargasatuan = $value['hargasatuan_reseptur'];
                $harganetto  = $value['harganetto_reseptur'];
            } else {
                $qty = $value['qty_oa'];
                $hargasatuan = $value['hargasatuan_oa'];
                $harganetto  = $value['harganetto_oa'];
            }

            $qty_rounded    = ceil($qty);
            $nilai_konversi = isset($additional_data['nilai_konversi']) ? $additional_data['nilai_konversi'] : 1;
            $qty_konversi_rounded = $qty_rounded * $nilai_konversi;

            $qty_hitung = !empty($value['det']) ? ceil($value['det']) : ceil($qty);
            $hargajual  = $hargasatuan * $qty_hitung;

            $row = [];
            $row['tglpelayanan']    = $now;
            $row['carabayar_id']    = isset($pendaftaran['carabayar_id']) ? $pendaftaran['carabayar_id'] : null;
            $row['penjamin_id']     = isset($pendaftaran['penjamin_id']) ? $pendaftaran['penjamin_id'] : null;
            $row['pendaftaran_id']  = $reseptur->pendaftaran_id;
            $row['pasien_id']       = $reseptur->pasien_id;
            $row['pasienadmisi_id'] = $reseptur->pasienadmisi_id;
            $row['kelaspelayanan_id'] = isset($pendaftaran['kelaspelayanan_id']) ?  $pendaftaran['kelaspelayanan_id'] : null;
            $row['det']             = !empty($value['det']) ? (int) ceil($value['det']) : null;
            $row['additional_data'] = json_encode(array_merge([
                'posisi' => isset($value['posisi']) ? $value['posisi'] : 9999
            ], $additional_data));
            $row['satuankecil_id']  = !isset($infoObatAlkes['satuankecil_id']) ? $infoObatAlkes['satuankecil_id'] : $value['satuankecil_id'];
            // $row['hargasatuan_oa']  = isset($infoObatAlkes['hargaygdipakai']) ? $infoObatAlkes['hargaygdipakai'] : $hargasatuan;
            $row['hargasatuan_oa']  = $hargasatuan;
            $row['hargajual_oa']    = $hargajual;
            $row['harganetto_oa']   = isset($infoObatAlkes['harganetto_ygdipakai']) ? $infoObatAlkes['harganetto_ygdipakai'] : $harganetto;
            $row['ruangan_id']      = $reseptur->ruangan_id;
            $row['penjualanresep_id'] = $reseptur->penjualanresep_id;
            $row['det_medis']       = !empty($value['det_medis']) ? $value['det_medis'] : null;
            $row['det_konversi']    = !empty($value['det_konversi']) ? $value['det_konversi'] : null;
            $row['pegawai_id']      = Yii::$app->user->identity->pegawai_id;
            $row['racikan_id']      = $value['racikan_id'] == '-' ? 2 : $value['racikan_id'];
            $row['obatalkes_id']    = $value['obatalkes_id'];
            $row['qty_oa']          = $qty_rounded;
            $row['qty_medis']       = !empty($value['qty_medis']) ? $value['qty_medis'] : $qty;
            $row['signa_oa']        = $value['signa_id'];
            $row['signa']           = isset($value['signa']) ? $value['signa'] : null;
            $row['rke']             = (empty($value['rke']) || $value['rke'] == '-') ? null : $value['rke'];
            $row['etiket']          = $value['etiket'];
            $row['is_kronis']       = isset($value['is_kronis']) ? $value['is_kronis'] : false;
            $row['qty_konversi']    = $qty_konversi_rounded;
            $row['created_by']      = Yii::$app->user->identity->pegawai_id;

            $row['is_ditagihkan']   = true;
            $row['nama_racikan']    = !empty($value['nama_racikan']) ? $value['nama_racikan'] : null;
            $row['qty_racikan']     = !empty($value['qty_racikan']) ? $value['qty_racikan'] : null;
            $row['satuan_racikan_id'] = !empty($value['satuan_racikan_id']) ? $value['satuan_racikan_id'] : null;
            $row['resepturdetail_id'] = !empty($value['resepturdetail_id']) ? $value['resepturdetail_id'] : null;

            $obatAlkesPasienPayload = new ObatAlkesPasienPayload;
            $obatAlkesPasienPayload->attributes = $row;
            if(!$obatAlkesPasienPayload->validate()) throw new Exception('Validasi ObatAlkesPasienPayload Gagal: '. json_encode($obatAlkesPasienPayload->errors), 422);
            $trx_details[] = $row;
        }
        $this->_oa = $trx_details;
        return $this;
    }

    public function saveObatAlkesPasien()
    {
        ObatAlkesPasienModel::batchInsert($this->_oa);
        return $this;
    }

    public function loadFromReseptur($inputOA,$infoResep,$penjualanresep_id)
    {
        $now = date('Y-m-d H:i:s');
        $obj_array_insert = [];
        foreach ($inputOA as $key => $value) {
            $additional_data = !empty($value['additional_data'])? json_decode($value['additional_data'],true) : [];
            if(isset($value['qty_reseptur'])) {
                $qty = $value['qty_reseptur'];
                $hargasatuan = $value['hargasatuan_reseptur'];
                $harganetto = $value['harganetto_reseptur'];
            } else {
                $qty = $value['qty_oa'];
                $hargasatuan = $value['hargasatuan_oa'];
                $harganetto = $value['harganetto_oa'];
            }

            $qty_rounded = ceil($qty);
            $nilai_konversi = isset($additional_data['nilai_konversi']) ? $additional_data['nilai_konversi'] : 1;
            $qty_konversi_rounded = $qty_rounded * $nilai_konversi;

            $qty_hitung = isset($value['det']) && !is_null($value['det']) ? ceil($value['det']) : ceil($qty);
            $hargajual = $hargasatuan * $qty_hitung;

            $row = [
                'ruangan_id' => $infoResep['ruangan_id'],
                'carabayar_id' => $infoResep['carabayar_id'],
                'pendaftaran_id' => $infoResep['pendaftaran_id'],
                'pasien_id' => $infoResep['pasien_id'],
                'penjamin_id' => $infoResep['penjamin_id'],
                'penjualanresep_id' => $penjualanresep_id,
                'tglpelayanan' => $now,
                'det' => isset($value['det']) && !is_null($value['det']) ? ceil($value['det']) : NULL,
                'det_medis' => isset($value['det_medis']) && !is_null($value['det_medis']) ? $value['det_medis'] : NULL,
                'det_konversi' => isset($value['det_konversi']) && !is_null($value['det_konversi']) ? $value['det_konversi'] : NULL,
                'pegawai_id' => Yii::$app->user->identity->pegawai_id,
                'racikan_id' => ($value['racikan_id'] == '-') ? 2 : $value['racikan_id'],
                'satuankecil_id' => $value['satuankecil_id'],
                'obatalkes_id' => $value['obatalkes_id'],
                'qty_oa' => $qty_rounded,
                'qty_medis' => isset($value['qty_medis']) ? $value['qty_medis'] : $qty,
                'signa_oa' => $value['signa_id'],
                'signa' => isset($value['signa']) ? $value['signa'] : null,
                'rke' => (empty($value['rke']) || $value['rke'] == '-') ? null : $value['rke'],
                'etiket' => $value['etiket'],
                'is_kronis' => isset($value['is_kronis']) ? $value['is_kronis'] : false,
                // ..._reseptur jika dari reseptur, ..._oa jika dari resep
                'hargasatuan_oa' => $hargasatuan,
                'harganetto_oa' => $harganetto,
                'hargajual_oa' => $hargajual,
                'qty_konversi' => $qty_konversi_rounded,
                'created_by' => Yii::$app->user->identity->pegawai_id,
                'additional_data'=> json_encode(array_merge([
                    'posisi' => isset($value['posisi']) ? $value['posisi'] : 9999
                ],$additional_data)),
                'is_ditagihkan' => true,
                'nama_racikan' => !empty($value['nama_racikan']) ? $value['nama_racikan'] : null,
                'qty_racikan' => !empty($value['qty_racikan']) ? $value['qty_racikan'] : null,
                'satuan_racikan_id' => !empty($value['satuan_racikan_id']) ? $value['satuan_racikan_id'] : null,
            ];
            $obj_array_insert[] = $row;
        }
        $this->_oa = $obj_array_insert;
        $this->_infoResep = $infoResep;
        return $this;
    }

    public function getObatAlkesPasien($penjualanresep_id) {
        $oa_pasien = ObatAlkesPasienModel::find()
                        ->select([
                            'obatalkespasien_id',
                            'penjualanresep_id',
                            (new \yii\db\Expression('CASE 
                                WHEN det_konversi IS NULL 
                                THEN CEIL(qty_konversi)
                                ELSE CEIL(det_konversi)
                                END AS qty_satuanpakai'
                            )),
                            'ruangan_id',
                            'obatalkes_id',
                            'satuankecil_id',
                            'additional_data',
                            'harganetto_oa as harganetto',
                            'persenppnjual as persenppn',
                            'nilaippnjual as jmlppn'
                        ])
                        ->where(['penjualanresep_id' => $penjualanresep_id])
                        ->asArray()->all();
        return $oa_pasien;
    }

    public function save()
    {
        $obatalkesIds = ArrayHelper::getColumn($this->_oa, 'obatalkes_id');
        $infoObatBulk = (new InfoStokObatAlkesFnrNew([
            'extParam' => [
                (string) $this->_infoResep['penjamin_id'],
                isset($this->_infoResep['kelaspelayanan_id']) ? (string) $this->_infoResep['kelaspelayanan_id'] : "0",
                (string) $this->_infoResep['ruangan_id']
            ]
        ]))->find()->where([
            'obatalkes_id' => $obatalkesIds
        ])->asArray()->all();
        $infoObatBulk = ArrayHelper::index($infoObatBulk, 'obatalkes_id');
        foreach ($this->_oa as $k_oa => $v_oa) {
            $modelOAdetail = new ObatAlkesPasienPayload;
            $modelOAdetail->attributes = $v_oa;
            if(!$modelOAdetail->validate()) throw new \Exception("Data Obat Alkes Detail Tidak Sesuai", 1);

            /*
            * deprecated function
            $infoObat = (new ObatAlkesFn([
                'extParam' => [
                    (string) $this->_infoResep['penjamin_id'],
                    isset($this->_infoResep['kelaspelayanan_id']) ? (string) $this->_infoResep['kelaspelayanan_id'] : "0"
                ]
            ]))->find()->where([
                'ruangan_id' => $v_oa['ruangan_id'],
                'obatalkes_id' => $v_oa['obatalkes_id']
            ])->asArray()->one();
            */
            $infoObat = isset($infoObatBulk[ArrayHelper::getValue($v_oa, 'obatalkes_id')]) ? $infoObatBulk[ArrayHelper::getValue($v_oa, 'obatalkes_id')] : [];

            $generatedDetail= [
                'tglpelayanan' => date('Y-m-d H:i:s'),
                'carabayar_id' => $this->_infoResep['carabayar_id'],
                'penjamin_id' => $this->_infoResep['penjamin_id'],
                'pendaftaran_id' => $this->_infoResep['pendaftaran_id'],
                'pasien_id' => $this->_infoResep['pasien_id'],
                'pasienadmisi_id' => $this->_infoResep['pasienadmisi_id'],
                'kelaspelayanan_id' => isset($this->_infoResep['kelaspelayanan_id']) ? $this->_infoResep['kelaspelayanan_id'] : NULL,
                'det' => !is_null($v_oa['det']) ? (int) ceil($v_oa['det']) : null,
                'additional_data' => empty($modelOAdetail->additional_data) ? null : $modelOAdetail->additional_data
            ];

            if(!empty($infoObat)){
                $generatedDetail['satuankecil_id'] = $infoObat['satuankecil_id'];
                $generatedDetail['hargasatuan_oa'] = $modelOAdetail->is_ditagihkan == 1 ? $infoObat['hargaygdipakai'] : 0;
                $generatedDetail['hargajual_oa'] = $modelOAdetail->is_ditagihkan == 1 ? $v_oa['hargajual_oa'] : 0;
                $generatedDetail['harganetto_oa'] = $infoObat['harganetto_ygdipakai'];
            }

            $trx_detail[] = array_replace($generatedDetail, $this->_oa[$k_oa]);
        }
        $this->newOa = $trx_detail;
        $resOA = ObatAlkesPasienModel::batchInsert($trx_detail);
    }

    public function updateData($id, $obatReseptur, $type = 'reseptur') {
        $now = date('Y-m-d H:i:s');
        $user_login = Yii::$app->user->identity->pegawai_id;

        if($type == 'reseptur') {
            $penjualanResep = InformasiResepturView::find()->where(['reseptur_id' => $id])->asArray()->one();
        } else {
            $penjualanResep = InfoResepView::find()->where(['penjualanresep_id' => $id])->asArray()->one();
        }

        $obatAlkesPasien = ObatAlkesPasienModel::find()->where([
            'penjualanresep_id' => $penjualanResep['penjualanresep_id']
        ])->asArray()->all();

        $this->_oa = $obatAlkesPasien;
        $currentOA = $oa_to_update = [];
        foreach ($this->_oa as $key => $value) {
            $currentOA[] = [
                'obatalkes_id'  => (int) $value['obatalkes_id'],
                'rke'           => $value['rke'],
                'racikan_id'    => $value['racikan_id']
            ];
            $oa_to_update[] = [
                'obatalkespasien_id'    => $value['obatalkespasien_id'],
                'obatalkes_id'          => (int) $value['obatalkes_id'],
                'rke'                   => $value['rke'],
                'racikan_id'            => $value['racikan_id']
            ];
        }

        $fromReseptur = [];
        foreach ($obatReseptur['obatReseptur'] as $_id => $_val) {
            $obatReseptur['obatReseptur'][$_id]['rke'] = $_val['r_ke'] == "-" ? null : (int) $_val['r_ke'];
            $fromReseptur[] = [
                'obatalkes_id'  => $_val['obatalkes_id'],
                'rke'           => $_val['r_ke'] == "-" ? null : (int) $_val['r_ke'],
                'racikan_id'    => $_val['racikan_id']
            ];

            $obatResepturKeys[] = $_id;
        }

        $update_oa = [];
        $deleted_oa = $currentOA;
        foreach($fromReseptur as $frvalue) {
            foreach ($currentOA as $covalue) {
                // detail obat yang akan di update
                $res = array_intersect_assoc($frvalue, $covalue);
                if(count($res) == 3) {
                    $update_oa[] = $res;
                }
            }

            // detail obat yang akan di delete
            $isExist = array_search($frvalue, $currentOA);
            unset($deleted_oa[$isExist]);
        }
        
        $update_oa = $this->addObatalkespasienId($update_oa, $oa_to_update);
        $deleted_oa = $this->addObatalkespasienId($deleted_oa, $oa_to_update);
        $obatReseptur['obatReseptur'] = $this->addObatalkespasienId($obatReseptur['obatReseptur'], $oa_to_update);

        // delete obat (update is_deleted)
        $del_arr_is_deleted = $del_arr_obatalkespasien_id = [];
        foreach ($deleted_oa as $value) {
            $del_arr_is_deleted[] = true;
            $del_arr_is_active[] = false;
            $del_arr_obatalkespasien_id[] = $value['obatalkespasien_id'];
        }

        if(count($del_arr_obatalkespasien_id) > 0) {
            $delete_oapasien = [
                'is_deleted' => $del_arr_is_deleted,
                'is_active' => $del_arr_is_active
            ];

            $deleteCondition = [
                'obatalkespasien_id' => $del_arr_obatalkespasien_id
            ];

            $deleteOAPasien = ApotekComponent::updateMultiple('obatalkespasien_t', $delete_oapasien, $deleteCondition);
            if(!$deleteOAPasien || \Yii::$app->response->statusCode != 200) {
                throw new \Exception("Gagal delete obatalkespasien", 1);
            }
        }

        // update tagihan
        $arr_racikan_id = $arr_rke = $arr_qty = $arr_qty_konversi = $arr_qty_medis = $arr_det = $arr_det_konversi = $arr_det_medis = $arr_obatalkes_id = $arr_hargajual_oa = $arr_harganetto_oa = $arr_obatalkespasien_id = $arr_hargasatuan_oa = $arr_signaId =$signa = [];
        foreach ($obatReseptur['obatReseptur'] as $key => $value) {
            $key_oaid = array_keys(array_column($update_oa, 'obatalkes_id'), $value['obatalkes_id']);
            $key_racikan_id = array_keys(array_column($update_oa, 'racikan_id'), $value['racikan_id']);
            $rke = $value['r_ke'] == "-" ? null : (int) $value['r_ke'];
            $key_rke = array_keys(array_column($update_oa, 'rke'), $rke);
            $nilai_konversi = isset($value['nilai_konversi']) ? $value['nilai_konversi'] : 1;
            if($key_oaid && $key_racikan_id && $key_rke) {
                $arr_signaId[] = floatval(ceil($value['signa_id']));
                $arr_obatalkespasien_id[] = $value['obatalkespasien_id'];
                $arr_obatalkes_id[] = $value['obatalkes_id'];
                $arr_racikan_id[] = $value['racikan_id'];
                $arr_is_kronis[] = $value['is_kronis'] == "true" ? true : false;
                $arr_rke[] = $value['r_ke'] == "-" ? null : (int) $value['r_ke'];
                $arr_det[] = floatval(ceil($value['det']));
                $arr_det_konversi[] = ceil($value['det']) * $nilai_konversi;
                $arr_det_medis[] = floatval($value['det_medis']);
                $arr_qty[] = floatval(ceil($value['qty']));
                $arr_qty_konversi[] = ceil($value['qty']) * $nilai_konversi;
                $arr_qty_medis[] = floatval($value['qty']);
                $arr_hargajual_oa[] = floatval($value['harga']) * floatval(ceil($value['det']));
                $arr_harganetto_oa[] = floatval($value['harganetto']) * floatval(ceil($value['det']));
                $arr_hargasatuan_oa[] = floatval($value['harga']);
                $arr_last_modified_by[] = $user_login;

                $obatalkespasienId = $value['obatalkespasien_id'];
                $dataSigna = json_encode(['id' => $value['signa_id'], 'text' => $value['signa'], 'kode' => null]);
                $query = 
                    "UPDATE obatalkespasien_t 
                    SET signa = '{$dataSigna}' 
                    WHERE obatalkespasien_id = {$obatalkespasienId} AND is_deleted = false;";
                \Yii::$app->db->createCommand($query)->execute();
            }
        }

        if(count($arr_obatalkes_id) > 0){
            $update_oapasien = [
                'signa_oa' => $arr_signaId,
                'det' => $arr_det,
                'det_konversi' => $arr_det_konversi,
                'det_medis' => $arr_det_medis,
                'qty_oa' => $arr_qty,
                'is_kronis' => $arr_is_kronis,
                'qty_konversi' => $arr_qty_konversi,
                'qty_medis' => $arr_qty_medis,
                'hargajual_oa' => $arr_hargajual_oa,
                'harganetto_oa' => $arr_harganetto_oa,
                'hargasatuan_oa' => $arr_hargasatuan_oa,
                'last_modified_by' => $arr_last_modified_by
            ];

            $updateCondition = [
                'obatalkespasien_id' => $arr_obatalkespasien_id
            ];
            $updateOAPasien = ApotekComponent::updateMultiple('obatalkespasien_t', $update_oapasien, $updateCondition);
            if(!$updateOAPasien) {
                throw new \Exception("Gagal update obatalkespasien1", 1);
            }
        }

        // tambah obat baru
        if(count($obatReseptur['newObat']) > 0) {
            $oapasienbaru = $this->loadFromReseptur(
                                $obatReseptur['newObat'],
                                $penjualanResep,
                                $penjualanResep['penjualanresep_id'])->save();
        }

        return true;
    }

    public function addObatalkespasienId($data, $oa_to_update)
    {
        foreach ($data as $updateKey => $updateValue) {
            foreach ($oa_to_update as $toKey => $toValue) {
                $diff = array_diff_assoc($toValue, $updateValue);
                if(count($diff) == 1) {
                    $data[$updateKey]['obatalkespasien_id'] = $toValue['obatalkespasien_id'];
                }
            }
        }

        return $data;
    }

    public function getTotalTarif($detailObat = []) 
    {
        $totalTarif = 0;
        if(empty($detailObat) && !empty($this->_oa)) {
            foreach ($this->_oa as $value) {
                $qty = ArrayHelper::getValue($value, 'qty_oa', 1);
                $hargaSatuan = ArrayHelper::getValue($value, 'hargasatuan_oa', 0);
                if(isset($value['qty_reseptur'])) {
                    $qty = ArrayHelper::getValue($value, 'qty_reseptur');
                    $hargaSatuan = ArrayHelper::getValue($value, 'hargasatuan_reseptur', 0);
                }

                $det = ArrayHelper::getValue($value, 'det');
                $qtyHitung = !empty($det) ? ceil($det) : ceil($qty);
                $hargajual  = $hargaSatuan * $qtyHitung;
                $totalTarif += $hargajual;
            }
        }
        else {
            // case edit resep Rajal
            // Hitung dari obatReseptur (obat lama)
            if (isset($detailObat['obatReseptur']) && !empty($detailObat['obatReseptur'])) {
                foreach ($detailObat['obatReseptur'] as $item) {
                    if (!empty($item['is_deleted'])) {
                        continue;
                    }

                    $harga = ArrayHelper::getValue($item, 'harga', 0);
                    $det = ArrayHelper::getValue($item, 'det');
                    $totalTarif += floatval($harga) * floatval(ceil($det));
                }
            }

            // Hitung dari newObat (obat baru)
            if (isset($detailObat['newObat']) && !empty($detailObat['newObat'])) {
                foreach ($detailObat['newObat'] as $item) {
                    $qty = ArrayHelper::getValue($item, 'qty_oa', 1);
                    $hargaSatuan = ArrayHelper::getValue($item, 'hargasatuan_oa', 0);
                    if(isset($item['qty_reseptur'])) {
                        $qty = ArrayHelper::getValue($item, 'qty_reseptur');
                        $hargaSatuan = ArrayHelper::getValue($item, 'hargasatuan_reseptur', 0);
                    }

                    $det = ArrayHelper::getValue($item, 'det');
                    $qtyHitung = !empty($det) ? ceil($det) : ceil($qty);
                    $totalTarif += $hargaSatuan * $qtyHitung;
                }
            }
        }

        return $totalTarif;
    }
}
