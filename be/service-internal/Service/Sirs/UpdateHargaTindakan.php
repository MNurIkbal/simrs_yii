<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoConstansId;
use Integrasi\Service\Sirs\Models\TindakanPelayanan;
use Integrasi\Service\Sirs\Models\TindakanKomponen;
use Integrasi\Service\Sirs\Cache\Cache;

class UpdateHargaTindakan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $primaryId = $this->tindakanpelayanan_id;
        $newAttr = $this->new_attributes;
        $uid = $this->user_id;
        $ruanganId = $this->ruangan_id;
        $penjaminId = $this->penjamin_id;
        $kelasId = $this->kelaspelayanan_id;
        $isChangeDoc = $this->is_change_doc;
        $qModel = TindakanPelayanan::find()->andWhere([
            'tindakanpelayanan_id' => $primaryId,
        ])->asArray()->one();
        
        $tipePaket = ArrayHelper::getValue($qModel, 'tipepaket_id');
        $tindakan = ArrayHelper::getValue($qModel, 'daftartindakan_id');
        $instalasiId = ArrayHelper::getValue($qModel, 'instalasi_id');
        $parentId = ArrayHelper::getValue($qModel, 'parent_id');
        $isPenyulit = ArrayHelper::getValue($qModel, 'penyulit_tindakan', false);
        $isCyto = ArrayHelper::getValue($qModel, 'cyto_tindakan', false);
        $qty = ArrayHelper::getValue($qModel, 'qty_tindakan', 1);
        $additionalData = ArrayHelper::getValue($qModel, 'additional_data', []);
        $detailAkomodasi = $remarks = [];
        $pengkali = 1;
        $isAkomodasi = $is_overwrite = false;
        
        if(!empty($additionalData)) {
            $additionalData = json_decode($additionalData, true);
            $listKomponen = ArrayHelper::getValue($additionalData, 'list_komponen', false);
            $remarks = ArrayHelper::getValue($additionalData, 'remarks', []);
            $detailAkomodasi = ArrayHelper::getValue($additionalData, 'detail_akomodasi', []);
            if (!empty($detailAkomodasi)) {
                $persentase = isset($detailAkomodasi['persentase']) ? (int) $detailAkomodasi['persentase'] : 0;
                $pengkali = $persentase/100;
                $tindakanAkomodasi = DocoConstansId::actionGetId('tindakan_akomodasi');
                if($tindakan == $tindakanAkomodasi){
                    $isAkomodasi = true;
                }
            }
            if(!empty($listKomponen)) {
                foreach ($listKomponen as $key => $value) {
                    // skip edit tarif ketika edit harga di penata jasa
                    $is_overwrite = ArrayHelper::getValue($value, 'is_overwrite', false);
                }
            }
        }
        
        if (!empty($tipePaket)) {
            $cond = "tipepaket_id = {$tipePaket}";
        } else {
            $cond = "daftartindakan_id = {$tindakan}";
        }

        $dpjpTindakan = ArrayHelper::getValue($qModel, 'dokterpenanggungjawab_id');
        $newTgl = isset($newAttr['tgl_tindakan']) ? date('Y-m-d H:i:s', strtotime($newAttr['tgl_tindakan'])) : null;
        $newDokter = !empty($newAttr['dokter_id']) ? $newAttr['dokter_id'] : $dpjpTindakan;
        $condUpdate = [];

        $getPenjamin = Cache::getPenjaminCaraBayar($penjaminId);
        $caraBayar = ArrayHelper::getValue($getPenjamin, 'carabayar_id');

        $condUpdate['penjamin_id'] = $penjaminId;
        $condUpdate['carabayar_id'] = $caraBayar;
        $condUpdate['kelaspelayanan_id'] = $kelasId;

        if ($newTgl != $qModel['tgl_tindakan'] && !empty($newTgl)) {
            $condUpdate['tgl_tindakan'] = $newTgl;
        }
        
        if (empty($tipePaket) && !empty($newDokter) 
            && $newDokter != $dpjpTindakan /* && empty($parentId) */) {
                $condUpdate['dokterpenanggungjawab_id'] = !empty($isChangeDoc) ? $newDokter : $dpjpTindakan;
                $newDokter = !empty($isChangeDoc) ? $newDokter : $dpjpTindakan;
        }
        
        if(!empty($tipePaket) && $instalasiId == DocoConstants::INST_ID_MCU) {
            $params = [
                'ruangan_id' => $ruanganId,
                'penjamin_id' => $penjaminId,
                'kelas_id' => $kelasId,
                'cond' => $cond,
                'is_cyto' => $isCyto,
                'is_penyulit' => $isPenyulit,
                'primary_id' => $primaryId,
                'tipepaket_id' => $tipePaket,
                'dokter_id' => $newDokter
            ];
            $condUpdate['dokterpenanggungjawab_id'] = $newDokter;
            $dataKomponen = $this->getTarifPaketKomponen($params);
            $komponen = ArrayHelper::getValue($dataKomponen, 'tarifPaket', []);
            $detailKomponen = ArrayHelper::getValue($dataKomponen, 'komponenPaket', []);
            $detailPaket = ArrayHelper::getValue($dataKomponen, 'detailPaket', []);
        }
        else {
            if(!empty($parentId)) {
                $params = [
                    'ruangan_id' => $ruanganId,
                    'penjamin_id' => $penjaminId,
                    'kelas_id' => $kelasId,
                    'parent_id' => $parentId,
                    'is_cyto' => $isCyto,
                    'is_penyulit' => $isPenyulit,
                    'tindakan_id' => $tindakan,
                    'primary_id' => $primaryId,
                    'dokter_id' => $newDokter
                ];
                $dataKomponen = $this->getDetailPaket($params);
                $komponen = ArrayHelper::getValue($dataKomponen, 'listTarif', []);
                $detailKomponen = ArrayHelper::getValue($dataKomponen, 'listKomponent', []);
            }
            else {
                $komponen = $this->newGetTarifKomponen($ruanganId, $penjaminId, $kelasId, $cond, $isAkomodasi, $newDokter);
            }
        }
        
        $listKomponen = $listKomponenAdditionalData = $listPaket = [];
        if (!empty($komponen) && !empty($caraBayar) && !$is_overwrite) {
            $totalPenyulit = $totalHarga = $totHargaSatuan = $totalCyto = 0;
            foreach ($komponen as $value) {
                $prctPenyulit = !empty($value['persen_penyulit']) ? $value['persen_penyulit'] : 0;
                $prctCyto = !empty($value['persencyto_tindakan']) ? $value['persencyto_tindakan'] : 0;
                $hargaSatuan = !empty($value['harga_tariftindakan']) ? $value['harga_tariftindakan'] * $pengkali : 0;
                $komponenId = !empty($value['komponentarif_id']) ? $value['komponentarif_id'] : null;
                $hargaSatuanPenyulit = $hargaSatuanCyto = $totalHargaKomp = 0;
                if (!empty($komponenId)) {
                    $totHargaSatuan += $hargaSatuan;

                    if ($isPenyulit) {
                        $hargaSatuanPenyulit = ($prctPenyulit/100) * $hargaSatuan;
                        $totalPenyulit += $hargaSatuanPenyulit;
                    }

                    if ($isCyto) {
                        $hargaSatuanCyto = ($prctCyto/100) * ($hargaSatuan + $hargaSatuanPenyulit);
                        $totalCyto += $hargaSatuanCyto;
                    }

                    $totalHargaKomp = ($hargaSatuan + $hargaSatuanCyto) * $qty;
                    $totalHarga += $totalHargaKomp;
                    $listKomponen[] = [
                        'komponentarif_id' => $komponenId,
                        'tindakanpelayanan_id' => $primaryId,
                        'tarif_kompsatuan' => (float) $hargaSatuan,
                        'tarif_tindakankomp' => $totalHargaKomp,
                        'tarifcyto_tindakankomp' => $hargaSatuanCyto,
                        'tarifpenyulit_komponen' => $hargaSatuanPenyulit,
                        'subsidiasuransikomp' => 0,
                        'subsidipemerintahkomp' => 0,
                        'subsidirumahsakitkomp' => 0,
                        'iurbiayakomp' => 0,
                        'created_date' => date('Y-m-d H:i:s', time()),
                        'created_by' => $uid,
                        'is_deleted' => false,
                        'is_active' => true,
                    ];

                    if(!empty($tipePaket) && $instalasiId == DocoConstants::INST_ID_MCU) {
                        $listKomponenAdditionalData = $detailKomponen;
                        $listPaket = $detailPaket;
                    }
                    elseif(!empty($parentId)) {
                        $listKomponenAdditionalData = $detailKomponen;
                    }
                    else {
                        $listKomponenAdditionalData = [
                            'komponentarif_id' => $komponenId,
                            'tindakanpelayanan_id' => $primaryId,
                            'tarif_kompsatuan' => (float) $hargaSatuan,
                            'tarif_tindakankomp' => $totalHargaKomp,
                            'tarifcyto_tindakankomp' => $hargaSatuanCyto,
                            'tarifpenyulit_komponen' => $hargaSatuanPenyulit,
                            'subsidiasuransikomp' => 0,
                            'subsidipemerintahkomp' => 0,
                            'subsidirumahsakitkomp' => 0,
                            'iurbiayakomp' => 0,
                        ];
                    }
                }
            }
            
            if (!empty($totHargaSatuan)) {
                $condUpdate['tarif_satuan'] = $totHargaSatuan;
                $condUpdate['tarif_tindakan'] = $totalHarga;
                $condUpdate['tarifpenyulit_tindakan'] = $totalPenyulit;
                $condUpdate['tarifcyto_tindakan'] = $totalCyto;
                $condUpdate['harga_origin'] = !empty($parentId) ? (float) $totHargaSatuan : 0;
                if(!empty($tipePaket) && $instalasiId == DocoConstants::INST_ID_MCU) {
                    $condUpdate['additional_data'] = json_encode([
                        'list_komponen'    => $listKomponenAdditionalData,
                        'detail_akomodasi' => $detailAkomodasi,
                        'detail_paket' => $listPaket,
                    ]);
                }
                elseif(!empty($parentId)) {
                    $condUpdate['tarif_tindakan'] = 0;
                    $condUpdate['additional_data'] = json_encode([
                        'list_komponen'    => $listKomponenAdditionalData,
                        'detail_akomodasi' => $detailAkomodasi,
                    ]);
                }
                else {
                    if(!empty($remarks)) {
                        $condUpdate['additional_data'] = json_encode([
                            'list_komponen'    => $listKomponenAdditionalData,
                            'detail_akomodasi' => $detailAkomodasi,
                            'remarks' => $remarks,
                        ]);
                    }
                    else {
                        $condUpdate['additional_data'] = json_encode([
                            'list_komponen'    => $listKomponenAdditionalData,
                            'detail_akomodasi' => $detailAkomodasi,
                        ]);
                    }
                }
            }
        }
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        if (!empty($listKomponen)) {
            $this->deleteTindakan($primaryId);
            TindakanKomponen::batchInsert($listKomponen);
        }
        
        $this->updateTindakan($condUpdate, $primaryId);
        $transaction->commit();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->pendaftaran_id.':edit_pendaftaran',
            'message' => json_encode([
                'status' => 1,
                'total' => $this->countTindakan,
                'processed' => $this->index,
                'tindakan' => $this->daftartindakan_nama,
                'totaltindakanobat' => $this->countTindakanObat,
                'messageProcess' => 'Tindakan '.$this->daftartindakan_nama.' berhasil di Update.',
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-UpdateHargaTindakan',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    protected function deleteTindakan($primary)
    {
        $cond = [
            'is_deleted' => true,
            'deleted_date' => date('Y-m-d H:i:s', time()),
            'deleted_by' => $this->user_id
        ];
        Yii::$app->db->createCommand()
            ->update('tindakankomponen_t', $cond, ['tindakanpelayanan_id' => $primary])
            ->execute();
    }

    protected function updateTindakan($cond = [], $primary)
    {
        $default = array_merge([
            'modified_count' => new \yii\db\Expression('COALESCE(modified_count,0) + 1'),
            'last_modified_date' => date('Y-m-d H:i:s', time()),
            'last_modified_by' => $this->user_id,
        ], $cond);
        Yii::$app->db->createCommand()
            ->update('tindakanpelayanan_t', $default, ['tindakanpelayanan_id' => $primary])
            ->execute();
    }

    protected function getTarifKomponen($penjaminId, $kelasId, $cond)
    {
        $qDetail = Yii::$app->db->createCommand("
            SELECT 
                daftartindakan_id,
                tipepaket_id,
                komponentarif_id,
                harga_tariftindakan,
                persencyto_tindakan,
                persen_penyulit
            FROM komponentarifnaikkelas_fn($penjaminId,$kelasId,'pelayanan') 
            WHERE $cond
            ORDER BY komponentarif_id, harga_tariftindakan DESC
        ")->queryAll();
        $mappDuplicateKomp = $tmpKomp = [];
        foreach ($qDetail as $key => $value) {
            if (!isset($tmpKomp[$value['komponentarif_id']])) {
                $tmpKomp[$value['komponentarif_id']] = true;
                $mappDuplicateKomp[] = $value;
            }
        }

        return $mappDuplicateKomp;
    }

    protected function newGetTarifKomponen($ruanganId,$penjaminId, $kelasId, $cond, $isAkomodasi = false, $dokterId = null)
    {
        $dokterId = !empty($dokterId) ? $dokterId : 0;
        if ($isAkomodasi) {
            $qDetail = Yii::$app->db->createCommand("
                SELECT 
                    daftartindakan_id,
                    tipepaket_id,
                    komponentarif_id,
                    harga_tariftindakan,
                    persencyto_tindakan,
                    persen_penyulit,
                    dokter_id
                FROM tarifkomponenrs_fn($ruanganId,$penjaminId,$kelasId,'kamar') 
                WHERE $cond AND (dokter_id = {$dokterId} OR dokter_id IS NULL)
                ORDER BY komponentarif_id, harga_tariftindakan DESC
            ")->queryAll();
        } else {
            $qDetail = Yii::$app->db->createCommand("
                SELECT 
                    daftartindakan_id,
                    tipepaket_id,
                    komponentarif_id,
                    harga_tariftindakan,
                    persencyto_tindakan,
                    persen_penyulit,
                    dokter_id
                FROM tarifkomponenrs_fn($ruanganId,$penjaminId,$kelasId,'pelayanan') 
                WHERE $cond AND (dokter_id = {$dokterId} OR dokter_id IS NULL)
                ORDER BY komponentarif_id, harga_tariftindakan DESC
            ")->queryAll();
        }


        $mappDuplicateKomp = $mappKomTarifDokter = $tmpKomp = $tmpKompTarifDokter = [];
        foreach ($qDetail as $key => $value) {
            $komponenTarifId = ArrayHelper::getValue($value, 'komponentarif_id');
            $dokter_id = ArrayHelper::getValue($value, 'dokter_id');

            if ($dokter_id == $dokterId) {
                if (!isset($tmpKompTarifDokter[$komponenTarifId])) {
                    $tmpKompTarifDokter[$komponenTarifId] = true;
                    $mappKomTarifDokter[] = $value;
                    continue;
                }
            }

            if (!isset($tmpKomp[$komponenTarifId])) {
                $tmpKomp[$komponenTarifId] = true;
                $mappDuplicateKomp[] = $value;
            }
        }

        return !empty($mappKomTarifDokter) ? $mappKomTarifDokter : $mappDuplicateKomp;
    }

    protected function getTarifPaketKomponen($params)
    {
        $primaryId = ArrayHelper::getValue($params, 'primary_id');
        $isCito = ArrayHelper::getValue($params, 'is_cyto', false);
        $isPenyulit = ArrayHelper::getValue($params, 'is_penyulit', false);
        $tarifPaket = $this->getMappingTarif($params);
        $komponenPaket = $this->getMappingKomponen($params);
        $listKomponent = $detailPaket = [];
        if(!empty($komponenPaket)) {
            foreach ($komponenPaket as $key => $value) {
                $hargaSatuanPenyulit = $hargaSatuanCyto = $totalHargaKomp = 0;
                $daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
                $komponenId = ArrayHelper::getValue($value, 'komponentarif_id');
                $hargaSatuan = ArrayHelper::getValue($value, 'harga_tariftindakan', 0);
                $persenCito = ArrayHelper::getValue($value, 'persencyto_tindakan', 0);
                $persenPenyulit = ArrayHelper::getValue($value, 'persen_penyulit', 0);
                $hargaSatuanCyto = ($isCito) ? ($persenCito/100) * $hargaSatuan : 0;
                $hargaSatuanPenyulit = ($isPenyulit) ? ($persenPenyulit/100) * $hargaSatuan : 0;
                $totalHargaKomp = $hargaSatuan + $hargaSatuanCyto + $hargaSatuanPenyulit;

                $listKomponent[$daftarTindakanId][] = [
                    'komponentarif_id' => $komponenId,
                    'tindakanpelayanan_id' => $primaryId,
                    'tarif_kompsatuan' => (float) $hargaSatuan,
                    'tarif_tindakankomp' => (float) $totalHargaKomp,
                    'tarifcyto_tindakankomp' => (float) $hargaSatuanCyto,
                    'tarifpenyulit_komponen' => (float) $hargaSatuanPenyulit,
                    'subsidiasuransikomp' => 0,
                    'subsidipemerintahkomp' => 0,
                    'subsidirumahsakitkomp' => 0,
                    'iurbiayakomp' => 0,
                    "is_overwrite" => false,
                    "harga_satuan_origin" => 0,
                    "harga_origin_komp" => (float) $hargaSatuan,
                    'daftartindakan_id' => $daftarTindakanId,
                ];

                $detailPaket[$daftarTindakanId][] = [
                    'komponentarif_id' => $komponenId,
                    'daftartindakan_id' => $daftarTindakanId,
                    'harga_satuan' => (float) $hargaSatuan,
                    'harga_cyto' => (float) $hargaSatuanCyto,
                    'harga_penyulit' => (float) $hargaSatuanPenyulit,
                    'harga_total' => (float) $totalHargaKomp,
                ];
            }
        }
        return [
            'tarifPaket' => $tarifPaket,
            'komponenPaket' => $listKomponent,
            'detailPaket' => $detailPaket,
        ];
    }

    protected function getDetailPaket($params)
    {
       $parentId = ArrayHelper::getValue($params, 'parent_id');
        $primaryId = ArrayHelper::getValue($params, 'primary_id');
        $tindakanId = ArrayHelper::getValue($params, 'tindakan_id');
        $tindakanPelayanan = TindakanPelayanan::findOne($parentId);
        $tipePaketId = ArrayHelper::getValue($tindakanPelayanan, 'tipepaket_id');
        $ruanganParentId = ArrayHelper::getValue($tindakanPelayanan, 'ruangan_id');
        $cond = "tipepaket_id = {$tipePaketId}";
        $isCito = ArrayHelper::getValue($params, 'is_cyto', false);
        $isPenyulit = ArrayHelper::getValue($params, 'is_penyulit', false);
        $params['cond'] = $cond;
        $params['ruangan_id'] = $ruanganParentId;
        $komponenParent = $this->getMappingKomponen($params);
        $listKomponent = $listTarif = [];
        if(!empty($komponenParent)) {
            foreach ($komponenParent as $key => $value) {
                $komponenTarifId = ArrayHelper::getValue($value, 'komponentarif_id');
                $daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
                $tipePaketId = ArrayHelper::getValue($value, 'tipepaket_id');
                $hargaSatuan = ArrayHelper::getValue($value, 'harga_tariftindakan', 0);
                $persenCito = ArrayHelper::getValue($value, 'persencyto_tindakan', 0);
                $persenPenyulit = ArrayHelper::getValue($value, 'persen_penyulit', 0);
                $hargaSatuanCyto = ($isCito) ? ($persenCito/100) * $hargaSatuan : 0;
                $hargaSatuanPenyulit = ($isPenyulit) ? ($persenPenyulit/100) * $hargaSatuan : 0;
                $totalHargaKomp = $hargaSatuan + $hargaSatuanCyto + $hargaSatuanPenyulit;

                if($tindakanId == $daftarTindakanId) {
                    $listTarif[] = [
                        'komponentarif_id' => $komponenTarifId,
                        'tindakanpelayanan_id' => $primaryId,
                        'daftartindakan_id' => $daftarTindakanId,
                        'tipepaket_id' => $tipePaketId,
                        'harga_tariftindakan' => (float) $hargaSatuan,
                        'persencyto_tindakan' => (float) $persenCito,
                        'persen_penyulit' => (float) $persenPenyulit,
                    ];
                    $listKomponent[] = [
                        'komponentarif_id' => $komponenTarifId,
                        'tindakanpelayanan_id' => $primaryId,
                        'tarif_kompsatuan' => (float) $hargaSatuan,
                        'tarif_tindakankomp' => (float) $totalHargaKomp,
                        'tarifcyto_tindakankomp' => (float) $hargaSatuanCyto,
                        'tarifpenyulit_komponen' => (float) $hargaSatuanPenyulit,
                        'subsidiasuransikomp' => 0,
                        'subsidipemerintahkomp' => 0,
                        'subsidirumahsakitkomp' => 0,
                        'iurbiayakomp' => 0,
                     ];
                }
            }
        }
        
        return [
            'listTarif' => $listTarif,
            'listKomponent' => $listKomponent,
        ];
    }

    protected function getMappingTarif($params = [])
    {
        // get tarif dokter
        $results = $this->getTarif($params,true,false);
        if(empty($results)) {
            // jika tarif dokter tidak ada maka ambil tarif default
            $results = $this->getTarif($params,true,true);
        }
        return $results;
    }

    protected function getMappingKomponen($params = [])
    {
        // get tarif dokter
        $results = $this->getTarif($params,false,false);
        if(empty($results)) {
            // jika tarif dokter tidaka ada maka ambil tarif default
            $results = $this->getTarif($params,false,true);
        }
        return $results;
    }

    protected function getTarif($params = [], $isTarif = true, $isDefault = true)
    {
        $ruanganId = ArrayHelper::getValue($params, 'ruangan_id');
        $penjaminId = ArrayHelper::getValue($params, 'penjamin_id');
        $kelasId = ArrayHelper::getValue($params, 'kelas_id');
        $dokterIdParams = ArrayHelper::getValue($params, 'dokter_id');
        $cond = ArrayHelper::getValue($params, 'cond');
        $condDokter = ($isDefault) ? " AND dokter_id IS NULL" : " AND dokter_id = {$dokterIdParams} ";
        $function = ($isTarif) ? 'tariftotalrs_fn' : 'tarifkomponenrs_fn';
        return Yii::$app->db->createCommand("
            SELECT 
            ruangan_id,
            instalasi_id,
            kelaspelayanan_id,
            penjamin_id,
            daftartindakan_id,
            tipepaket_id,
            komponentarif_id,
            harga_tariftindakan,
            persencyto_tindakan,
            carabayar_id,
            persen_penyulit,
            dokter_id
            FROM {$function}($ruanganId,$penjaminId,$kelasId,'pelayanan')
            WHERE $cond $condDokter
            GROUP BY ruangan_id,
            instalasi_id,
            kelaspelayanan_id,
            penjamin_id,
            daftartindakan_id,
            tipepaket_id,
            komponentarif_id,
            harga_tariftindakan,
            persencyto_tindakan,
            carabayar_id,
            persen_penyulit,
            dokter_id,
            daftartindakan_nama
            ORDER BY daftartindakan_nama ASC
        ")->queryAll();
    }

    private function secondsToTime($s)
    {
        $h = floor($s / 3600);
        $s -= $h * 3600;
        $m = floor($s / 60);
        $s -= $m * 60;
        return $h.':'.sprintf('%02d', $m).':'.sprintf('%02d', $s);
    }
}