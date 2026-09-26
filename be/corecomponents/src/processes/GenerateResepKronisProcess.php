<?php

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;

use Doco\Notifications\FarmasiNotification;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use SirsCore\features\IntegrasiAkunting;
use SirsCore\models\ObatAlkesPasien;

use app\components\ApotekComponent;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\Racikan;
use app\modules\v1\models\KonfigAntrianFarmasi;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Reseptur;

class GenerateResepKronisProcess extends \Doco\components\DocoBaseProcessExtension {
    protected $payload;
    protected $ruanganId;
    protected $jenisPenjualanId;
    protected $userLogin;
    protected $listObat;
    protected $resep;
    protected $noresep;
    protected $tglPenjualan;

    protected $obatalkesIds;
    protected $qtyKonversi;
    protected $ruanganIds;
    protected $processFailed = 0;
    const ATTEMPT = 1;

    /**
     * @return void
     * @throws Doco\exceptions\ValidationException
     */
    protected function validasiPayload() {
        $this->userLogin = Yii::$app->user->identity->pegawai_id;
        $this->ruanganId = $this->_requestData->post('ruangan_id', null);
        $this->listObat = $this->_requestData->post('list_obat');
        $this->listObat = is_array($this->listObat) ? $this->listObat : json_decode($this->listObat, true);
    }

    protected function insertPenjualanResep() {
        $total_harganetto = 0;
        $racikanKode = [];
        foreach ($this->listObat as $value) {
            $total_harganetto += $value['harganetto'];
            $racikanKode[] = $value['racikan_id'];
        } 
        
        $this->tglPenjualan = $this->_requestData->post('tanggal_penjualan', date('Y-m-d H:i:s'));
        $this->tglPenjualan = date('Y-m-d H:i:s', strtotime($this->tglPenjualan));

        $this->resep = new PenjualanResep;
        $this->resep->attributes = $this->_requestData->post();
        $this->resep->jenispenjualan = $this->_requestData->post('jenispenjualan');
        $this->resep->pegawai_id = $this->_requestData->post('dokter');
        $this->resep->nama_pembeli = $this->_requestData->post('pembeli');
        $this->resep->nosep = $this->_requestData->post('sep');
        $this->resep->totharganetto = $total_harganetto;
        $this->resep->totalhargajual = $this->_requestData->post('total_obat', 0);
        $this->resep->tglpenjualan = $this->tglPenjualan;
        $this->resep->tglresep = $this->tglPenjualan;
        $this->resep->tgl_lahir = !empty($this->_requestData->post('tgl_lahir')) ? date('Y-m-d', strtotime($this->_requestData->post('tgl_lahir'))) : null;
        $this->resep->antrian_id = $this->_requestData->post('antrian_id');
        $this->resep->pegawai_approve_id = $this->userLogin;
        $this->resep->tgl_approve = date('Y-m-d H:i:s');
        $this->resep->resep_kronis_asal_id = $this->_requestData->post('penjualanresep_asal');
        $this->resep->reseptur_kronis_asal_id = $this->_requestData->post('reseptur_asal');
        $this->resep->hasil_resep_kronis_id = null;

        
        if (!$this->resep->validate() || !$this->resep->save()) {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_errorValidation, [
                'data' => $this->resep->errors
            ]);
        }
    }

    protected function generateAntrian($modelResep, $racikanKode) {
        $const = DocoConstants::VAR_FA_NR;
        $racikanType = "NR";
        $racikanKode = array_unique($racikanKode);
        if(count($racikanKode) > 1 || $racikanKode[0] == "OR"){
            $const = DocoConstants::VAR_FA_R;
            $racikanType = "OR";
        }

        $list_racikan = Racikan::find()->all();
        $list_racikan = ArrayHelper::map($list_racikan, 'racikan_singkatan', 'racikan_id');

        $data_konfigantrianfarmasi = KonfigAntrianFarmasi::find()->where(['fungsiantrian_id'=>$const,'is_default' => true])->one();
        $modelAntrian = new Antrian;
        $modelAntrian->pendaftaran_id = $modelResep->pendaftaran_id;
        $modelAntrian->ruangan_id = $modelResep->ruangan_id;
        $modelAntrian->tgl_antrian = date('Y-m-d H:i:s');
        $modelAntrian->jenisantrian_id = DocoConstants::VAR_JA_F;
        $modelAntrian->racikan_id = $list_racikan[$racikanType];
        $fungsiantrian_id = !empty($data_konfigantrianfarmasi->fungsiantrian_id) ? $data_konfigantrianfarmasi->fungsiantrian_id : null;

        $modelAntrian->fungsiantrian_id = $fungsiantrian_id;
        $modelAntrian->save(false);
        $antrian_id = $modelAntrian->antrian_id;

        return $antrian_id;
    }

    protected function insertBilling() {
        $index = 0;
        $obj_array_insert = $this->qtyKonversi = $this->obatalkesIds = [];
        
        $signaObat = [];
        $rawSigna = SignaObat::find()->asArray()->all();
        if(count($rawSigna) > 0){
            $signaObat = array_column($rawSigna, 'signa_nama','signa_id');
        }

        foreach ($this->listObat as $key => $value) {
            if($value['qty_konversi'] == 0 || $value['qty_konversi'] == "0"){
                $this->cancelDBTransaction();
                \Yii::$app->response->statusCode = 422;
                throw new ValidationException(422, $this->_error, [
                    'text' => 'Qty harus lebih dari 0'
                ]);
            }
            $qty_rounded = isset($value['qty']) ? ceil($value['qty']) : 0;
            $nilai_konversi = isset($value['nilai_konversi']) ? $value['nilai_konversi'] : 1;
            $hargajual = isset($value['hargajual']) ? $value['hargajual'] : 0;
            $signa_index = isset($value['signa_id']) ? array_search($value['signa_id'], array_column($rawSigna, 'signa_id')) : null;
            $signa_id = isset($value['signa_id']) ? $value['signa_id'] : null;
            $signa = isset($value['signa']) ? $value['signa'] : null;
            $obj_array_insert[$key] = [
                'ruangan_id' => $this->ruanganId,
                'is_kronis' => true,
                'carabayar_id' => $this->_requestData->post('carabayar_id'),
                'penjamin_id' => $this->_requestData->post('penjamin_id'),
                'pendaftaran_id' => $this->_requestData->post('pendaftaran_id', ""),
                'pasien_id' => $this->_requestData->post('pasien_id', ""),
                'pasienadmisi_id' => $this->_requestData->post('pasienadmisi_id', ""),
                'pegawai_id' => $this->_requestData->post('pegawai_id'),
                'satuankecil_id' => $value['satuankecil_id'],
                'racikan_id' => $value['racikan_id'],
                'rke' => isset($value['r_ke']) ? ($value['r_ke'] != '-') ? $value['r_ke'] : null  : null,
                'obatalkes_id' => $value['obatalkes_id'],
                'penjualanresep_id' => $this->resep->penjualanresep_id,
                'tglpelayanan' => $this->tglPenjualan,
                'kelaspelayanan_id' => $this->_requestData->post('kelaspelayanan_id', null),
                'qty_oa' => $qty_rounded,
                'qty_konversi' => $nilai_konversi * $qty_rounded,
                'hargajual_oa' => $hargajual * $qty_rounded,
                'harganetto_oa' => empty($value['harganetto']) ? 0 : $value['harganetto'],
                'hargasatuan_oa' => empty($value['harga']) ? 0 : $value['harga'],
                'signa_oa' => !is_null($signa_id) && isset($signaObat[$signa_id]) && $signa != $signa_id ? $signa_id : null,
                'created_by' => $this->userLogin,
                'signa' => !is_null($signa_id) && isset($signaObat[$signa_id]) && $signa != $signa_id ? 
                            json_encode([
                                'id' => $signa_id,
                                'text' => $rawSigna[$signa_index]['signa_nama'],
                                'kode' => $rawSigna[$signa_index]['signa_kode']
                            ]) : json_encode([
                                'id' => null,
                                'text' => $signa,
                                'kode' => null
                            ]),
                'created_by' => $this->userLogin,
                'etiket' => $value['etiket'],
                'biayaadministrasi' => $this->_requestData->post('biayaadministrasi'),
                'additional_data' => json_encode([
                    'posisi' => isset($value['posisi']) ? $value['posisi'] : 9999,
                    'qty_input' => $value['qty'],
                    'satuaninput_id' => isset($value['satuaninput_id']) ? $value['satuaninput_id'] : null,
                    'satuan_input' => isset($value['satuan_input']) ? $value['satuan_input'] : null,
                    'satuankonversi_id' => isset($value['satuankonversi_id']) ? $value['satuankonversi_id'] : null,
                    'satuan_konversi' => isset($value['satuan_konversi']) ? $value['satuan_konversi'] : null,
                    'harga_konversi' => isset($value['harga_konversi']) ? $value['harga_konversi'] : null,
                    'nilai_konversi' => isset($value['nilai_konversi']) ? $value['nilai_konversi'] : null,
                ]),
                'is_ditagihkan' => true,
                'nama_racikan' => !empty($value['nama_racikan']) ? $value['nama_racikan'] : null,
                'qty_racikan' => !empty($value['qty_racikan']) ? $value['qty_racikan'] : null,
                'satuan_racikan_id' => !empty($value['satuan_racikan_id']) ? $value['satuan_racikan_id'] : null,
                'qty_medis' => $value['qty'],
            ];

            if(array_key_exists($value['obatalkes_id'], $this->qtyKonversi)) {
                $qty_konversi = (int) ceil($value['qty_konversi']) + $this->qtyKonversi[$value['obatalkes_id']];
                $this->qtyKonversi[$value['obatalkes_id']] = $qty_konversi;
            } else {
                $this->qtyKonversi[$value['obatalkes_id']] = (int) ceil($value['qty_konversi']);
                $this->obatalkesIds[] = $value['obatalkes_id'];
                $this->ruanganIds[] = $this->ruanganId;
            }
            
            $index++;
        }

        $obatalkes_pasien = ObatAlkesPasien::batchInsert($obj_array_insert);
        if(!$obatalkes_pasien){
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_error, [
                'text' => 'Tidak dapat memproses transaksi obat alkes'
            ]);
        }
    }

    protected function afterSave() {
        $getData = PenjualanResep::findOne($this->resep->penjualanresep_id);
        $this->noresep = isset($getData['noresep']) ? $getData['noresep'] : '';
        IntegrasiAkunting::integrateByNoResep($this->noresep);
        FarmasiNotification::updateNotif();
    }

    protected function pesanStok() {
        $inCondition = "(" . implode(", ", $this->obatalkesIds) . ")";
        $str = "SELECT
                    qty_dipesan, qty_tersedia, obatalkes_id
                FROM stokobatalkes_r
                WHERE obatalkes_id IN $inCondition
                    AND ruangan_id = {$this->ruanganId}";
        $stok_obat = Yii::$app->db->createCommand($str)->queryAll();
        foreach ($stok_obat as $value) {
            $qty_dipesan[$value['obatalkes_id']] = $value['qty_dipesan'];
            $qty_tersedia[$value['obatalkes_id']] = $value['qty_tersedia'];
        }

        $index = 0;
        foreach ($this->obatalkesIds as $value) {
            $arr_qty_dipesan[] = (int) $qty_dipesan[$value] + (int) $this->qtyKonversi[$value];
            $arr_qty_tersedia[] = (int) $qty_tersedia[$value] - (int) $this->qtyKonversi[$value];
            $index++;
        }

        $obat_dipesan = [
            'qty_dipesan' => $arr_qty_dipesan,
            'qty_tersedia' => $arr_qty_tersedia
        ];

        $updateCondition = [
            'obatalkes_id' => $this->obatalkesIds,
            'ruangan_id' => $this->ruanganIds
        ];

        ApotekComponent::updateMultiple('stokobatalkes_r', $obat_dipesan, $updateCondition);
    }

    protected function updatereseptur(){
        $resepturAsal = $this->_requestData->post('reseptur_asal');
        ObatAlkesPasien::updateAll(['is_kronis'   => true], 'penjualanresep_id = '.$this->resep->penjualanresep_id);

        // generate hasil_resep_kronis_id di table penjualanresep_t
        $penjualanresep_Asal = $this->_requestData->post('penjualanresep_asal');
        if(!empty($penjualanresep_Asal)){
            $updatedata = PenjualanResep::find()->where(['penjualanresep_id' => $penjualanresep_Asal])->one();
            $updatedata->hasil_resep_kronis_id = $this->resep->penjualanresep_id;
            $updatedata->save();
        }

        // generate hasil_resep_kronis_id di table reseptur_t
        if(!empty($resepturAsal)){
            $update = Reseptur::find()->where(['reseptur_id' => $resepturAsal])->one();
            $update->hasil_resep_kronis_id = $this->resep->penjualanresep_id;
            $update->save();
        }
    }

    protected function handlingErrorDb($e)
    {
        $exception = preg_match('/(?<=ERROR:  )(.*)/', $e->getMessage(), $errorText);
        $message = $e->getMessage();
        if(preg_match('/\bduplicate\b/i', $errorText[1])) {
            if($this->processFailed < self::ATTEMPT) {
                $this->processFailed++;
                $this->execute();
            } else {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => $message,
                ];
            }
        } else {
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $message,
            ];
        }
    }
    
    protected function processFlow() {
        $this->validasiPayload();

        $this->startDBTransaction();
        $this->insertPenjualanResep();
        $this->insertBilling();
        $this->pesanStok();
        $this->updatereseptur();
        $this->commitDBTransaction();
        $this->afterSave();

        return [
            'message' => 'Data Berhasil di simpan',
            'id' => DocoHelpers::encrypt($this->resep->penjualanresep_id),
            'nomor' => $this->noresep
        ];
    }
}