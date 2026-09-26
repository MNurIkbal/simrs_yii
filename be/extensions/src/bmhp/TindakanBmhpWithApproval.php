<?php

/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\bmhp;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use Doco\models\InfoKunjunganRajal;
use Doco\models\TindakanPelayanan;
use Doco\models\InfoTarifRs;
use Doco\models\Pendaftaran;
use Doco\models\ObatAlkesPasien;
use Doco\models\InfoStokObatAlkesFn;
use SirsCore\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use SirsCore\features\IntegrasiAkunting;
use Doco\components\DocoMessages;
use Doco\models\SatuanKonversiView;
use Doco\Services\PlafonBpjsService;

class TindakanBmhpWithApproval extends \Doco\processes\TindakanBmhpProcess
{
	protected function saveObatAlkes()
	{
		if (!empty($this->dataObat)) {
			$sisa = 0;
            $detailTrans = [];
            $depo_id = $this->dataTindakanBmhp['depo_id'];
            $kelas_id = $this->info['kelaspelayanan_id'];
            $penjamin_id = $this->info['penjamin_id'];

            // validasi plafon
            $validasiPlafon = new PlafonBpjsService($this->pendaftaran_id, $this->getTotalTarif());
            $result = $validasiPlafon->validasiPlafon();
            if (!$result['isValid']) {
                throw new \yii\web\HttpException(400, $result['message'] ? $result['message'] : 'Validasi Plafon Gagal');
            }
            
            foreach ($this->dataObat as $key => $val_bmhp) {
            	$bmhp = $val_bmhp;
            	$tipepaket_id = isset($bmhp['tipepaket_id']) ? $bmhp['tipepaket_id'] : null;
				$daftartindakan_id = isset($bmhp['daftartindakan_id']) ? $bmhp['daftartindakan_id'] : null;
				$tindakan_is_cyto = isset($bmhp['tindakan_is_cyto']) ? $bmhp['tindakan_is_cyto'] : 0;

				// Declare model
                $model = new ObatAlkesPasien();
                $idObatAlkes = $bmhp['obatalkes_id'];
                $tagihkan = $bmhp['is_ditagihkan'] == 1 ? true : false;
                $infoObat = $this->medicines[$bmhp['obatalkes_id']];
                $stokObat = $this->medicinesStok[$bmhp['obatalkes_id']];
				
                $satuankecil_id = isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null;
                    
                $hargaygdipakai = isset($infoObat['hargaygdipakai']) && $tagihkan ? ceil($infoObat['hargaygdipakai']) : 0;

                $dataKonversi = SatuanKonversiView::find()
                    ->select(['nilai_konversi', 'satuankonversi_id', 'satuan_besar'])
                    ->where(['obatalkes_id' => $idObatAlkes, 'satuankecil_id' => $satuankecil_id])
                    ->one();

				if (!($infoObat)) {
                    throw new \yii\web\HttpException(400, 'Obat pada baris ke-' . ($key + 1) . ' tidak terdaftar');
                }

                $idTindakan = null;
                $infoTindakan = new TindakanPelayanan;
                
                if($tipepaket_id != '') {
					$is_paket_tindakan = true;
                    $model->tipepaket_id = $tipepaket_id;
                    if($tindakan_is_cyto == 1){
                        if(isset($this->list_save_tindakan['paket']['cyto'][$tipepaket_id])){
                            $idTindakan = $this->list_save_tindakan['paket']['cyto'][$tipepaket_id];
                        }
                    }else{
                        if(isset($this->list_save_tindakan['paket']['noncyto'][$tipepaket_id])){
                            $idTindakan = $this->list_save_tindakan['paket']['noncyto'][$tipepaket_id];
                        }
                    }
                } else if($daftartindakan_id != '') {
					$is_paket_tindakan = false;
                    $model->daftartindakan_id = $daftartindakan_id;
                    if($tindakan_is_cyto == 1){
                        if(isset($this->list_save_tindakan['tindakan']['cyto'][$daftartindakan_id])){
                            $idTindakan = $this->list_save_tindakan['tindakan']['cyto'][$daftartindakan_id];
                        }
                    }else{
                        if(isset($this->list_save_tindakan['tindakan']['noncyto'][$daftartindakan_id])){
                            $idTindakan = $this->list_save_tindakan['tindakan']['noncyto'][$daftartindakan_id];
                        }
                    }
                }

                $model->tindakanpelayanan_id = $idTindakan;
                $model->obatalkes_id = $idObatAlkes;
                $model->stok_obat = isset($stokObat['qty_tersedia']) ? $stokObat['qty_tersedia'] : 0;
                $model->perawat1_id = isset($this->dataTindakanBmhp['perawat1_id']) ? $this->dataTindakanBmhp['perawat1_id'] : @$bmhp['perawat1_id'];
                $model->perawat2_id = isset($this->dataTindakanBmhp['perawat2_id']) ? $this->dataTindakanBmhp['perawat2_id'] : @$bmhp['perawat2_id'];
                $model->carabayar_id = isset($this->info['carabayar_id']) ? $this->info['carabayar_id'] : null;
                $model->pegawai_id = isset($bmhp['dokterpenanggungjawab_id']) ? $bmhp['dokterpenanggungjawab_id'] : null;
                $model->satuankecil_id = isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null;
                $model->pendaftaran_id = $this->pendaftaran_id;
                $model->pasien_id = isset($this->info['pasien_id']) ? $this->info['pasien_id'] : null;
                $model->penjamin_id = isset($this->info['penjamin_id']) ? $this->info['penjamin_id'] : null;
                $model->kelaspelayanan_id = isset($this->info['kelaspelayanan_id']) ? $this->info['kelaspelayanan_id'] : null;
                $model->tglpelayanan = date('Y-m-d H:i:s');
                $model->qty_oa = $bmhp['qty_oa'];
                $model->hargasatuan_oa = $hargaygdipakai;
                $model->harganetto_oa = isset($infoObat['harganetto_ygdipakai']) && $tagihkan ? $infoObat['harganetto_ygdipakai'] : 0;
                $model->hargajual_oa = $tagihkan ? $model->qty_oa * $model->hargasatuan_oa : 0;
                $status_bmhp = ($model->ruangan_id == $depo_id) ? null : DocoConstants::BMHP_BELUM_VERIFIKASI;
                $model->status_bmhp = $status_bmhp;
                $model->ruangan_id = $depo_id;
                $harga_konversi = ($bmhp['qty_oa'] * $dataKonversi['nilai_konversi']) * $hargaygdipakai;
                    
                $additional_data = [
                    'satuaninput_id' => $satuankecil_id,
                    'satuan_input' => isset($infoObat['satuankecil_nama']) ? $infoObat['satuankecil_nama'] : null,
                    'satuankonversi_id' => $dataKonversi['satuankonversi_id'],
                    'satuan_konversi' => $dataKonversi['satuan_besar'],
                    'harga_konversi' => $harga_konversi,
                    'nilai_konversi' => $dataKonversi['nilai_konversi'],
                    'jml_konversi' => 1,
                    'satuan_penyimpanan' => isset($infoObat['satuankecil_nama']) ? $infoObat['satuankecil_nama'] : null,
                ];

                $model->additional_data = json_encode($additional_data);

                if ($model->validate() && $model->save()) {
					$tanggalBerlaku = date('Y-m-d');
                    // Mencari Metode
                    $konfig = Yii::$app->db->createCommand("
                        SELECT metodeantrian FROM konfigfarmasi_k
                        WHERE tglberlaku >= '{$tanggalBerlaku}'
                        AND konfigfarmasi_aktif = true
                        AND is_active = true
                    ")->queryOne();
                    // Mencari Metode dengan nilai default FEFO
                    $currentMetode = LogicStokObatAlkes::FEFO;
                    if ($konfig) {
                        $currentMetode = isset($konfig['metodeantrian'])
                                            ? strtoupper($konfig['metodeantrian']) : LogicStokObatAlkes::FEFO;
                    }
                    $detailTrans[] = [
                        'obatalkes_id' => $idObatAlkes,
                        'qty_satuanpakai' => $bmhp['qty_oa'],
                        'satuankecil_id' => isset($infoObat['satuankecil_id']) ? $infoObat['satuankecil_id'] : null,
                        'obatalkespasien_id' => $model->obatalkespasien_id,
                        'harganetto' => $infoObat['harganetto_ygdipakai'],
                        'jmlppn' => $infoObat['jmlppn'],
                        'jmlmargin' => $infoObat['jmlmargin'],
                        'jmldiscount' => $infoObat['jmldiscount'],
                        'persendiscount' => $infoObat['persendiscount'],
                        'persenmargin' => $infoObat['persenmargin'],
                        'persenppn' => $infoObat['persenppn'],
                    ];
                }
                else {
                    $errors = $model->getErrors();
                    Yii::error($errors);
					throw new ValidationException(422, $this->_error, [
                        'text' => "Terjadi Kesalahan"
                    ]);
                }
            }

            if(count($detailTrans) > 0 && $this->ruangan_id == $depo_id){
                $tanggalPemakaian = date('Y-m-d H:i:s');
                // Execute By Condition
                LogicStokObatAlkes::$distribusi = false;
                if ($currentMetode === LogicStokObatAlkes::FEFO) {
                   $methode = LogicStokObatAlkes::methodeFEFO($detailTrans,$tanggalPemakaian);
                } else {
                   $methode = LogicStokObatAlkes::methodeFIFO($detailTrans,$tanggalPemakaian);
                }
            }

            $status = DocoConstants::BELUM_LUNAS;
            if ($this->pendaftaran_id != '') {
            $modelPendaftaran = Pendaftaran::findOne($this->pendaftaran_id);
            if ($modelPendaftaran) {
                $modelPendaftaran->status_bayar = DocoConstants::BELUM_LUNAS;
                $modelPendaftaran->save();
            }
	        }
            
            $no_pendaftaran = $modelPendaftaran->no_pendaftaran;
            $instalasi_id = Yii::$app->jwt->instalasi_id;

            $this->no_pendaftaran = $no_pendaftaran;
            $this->instalasi_id = $instalasi_id;
        }
	}

    protected function getTotalTarif()
    {
        $totalTarif = 0;
        foreach ($this->dataObat as $value)
        {
            $isDitagihkan = ArrayHelper::getValue($value, 'is_ditagihkan', true);
            $infoObat = $this->medicines[ArrayHelper::getValue($value, 'obatalkes_id')];
            $qty = ArrayHelper::getValue($value, 'qty_oa', 1);
            $harga = ArrayHelper::getValue($infoObat, 'hargaygdipakai', 0);
            $totalTarif += $isDitagihkan ? $qty * $harga : 0;
        }

        return $totalTarif;
    }
}