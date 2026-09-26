<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;

use SirsCore\features\FeatureTindakanBmhp;
use Doco\Notifications\FarmasiNotification;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\models\Lookup;
use Doco\models\KonfigFarmasi;
use Doco\Services\FarmasiService;

use app\components\ApotekComponent;
use app\modules\v1\businessLogic\ValidasiStok;
use app\modules\v1\entities\ObatAlkesPasien;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\InfoResepDetailView;
use app\modules\v1\models\InformasiResepDetailView;
use app\modules\v1\models\InfoResepView;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\WorklistView;
use app\modules\v1\models\Reseptur;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\ObatAlkesPasien as ModelOAPasien;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoConstansId;

use Doco\payload\SerahkanObat as PayloadSerahkanObat;
use Exception;
use SirsCore\businessLogic\PotongStokSerahkanResep;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\Services\PlafonBpjsService;

class SerahkanObatProcess extends \Doco\components\DocoBaseProcessExtension {
    protected $nomor;
    protected $ruanganId;
    protected $ruanganAsalId;
    protected $pegawaiMenyerahkan;

    protected $penjualanresepId;
    protected $bypassWorklist;
    protected $logWorklist;
    protected $worklist;

    protected $resep;
    protected $listObat;
    protected $logUser;

    protected $reseptur;
    protected $resepturId;
    protected $payloadStok;

    protected $arrObatalkesId;
    protected $arrQtyKonversi;
    protected $arrRuanganId;

    protected $konfigReseptur = [];
    protected $pendaftaranId;

    protected $konfigFarmasi;

    /**
     * @return void
     * @throws Doco\exceptions\ValidationException
     */
    protected function validasiPayload() {
        $this->nomor = Yii::$app->request->post('nomor', null);
        $this->ruanganId = Yii::$app->request->post('ruangan_id', null);
        $this->ruanganAsalId = Yii::$app->request->post('instalasiasal_id', null);
        $this->pegawaiMenyerahkan = Yii::$app->request->post('pegawai_menyerahkan_id', null);
        
        $payload = new PayloadSerahkanObat();
        $payload->nomor = $this->nomor;
        $payload->ruangan_id = $this->ruanganId;
        $payload->ruanganasal_id = $this->ruanganAsalId;

        if (!$payload->validate()) {
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_errorValidation, [
                'data' => $payload->errors
            ]);
        }
    }

    protected function getKonfigWorklist() {
        $this->konfigFarmasi = Yii::$app->db->createCommand("SELECT is_bypassworklist, konfig_carabayar_reseptur FROM konfigfarmasi_k")->queryOne();
        $this->bypassWorklist = isset($this->konfigFarmasi["is_bypassworklist"]) ? $this->konfigFarmasi["is_bypassworklist"] : false;
    }

    protected function getInfoResep() {
        $inforesep = $this->worklist;
        /** worklist sama dengan query select penjualanresep_t / reseptur_t 
         * jika null baru query ulang 
        */
        if (empty($inforesep)) {
            $inforesep = Yii::$app->db->createCommand("SELECT 
                    pendaftaran_id, 
                    noresep
                    reseptur_id,
                    penjualanresep_id
                FROM penjualanresep_t 
                WHERE noresep = :noresep"
            )
            ->bindValue(":noresep", $this->nomor)
            ->queryOne();

            if (empty($inforesep)) {
                $inforesep = Yii::$app->db->createCommand("SELECT 
                        pendaftaran_id, 
                        noresep,
                        reseptur_id,
                        penjualanresep_id
                    FROM reseptur_t 
                    WHERE noresep = :noresep"
                )
                ->bindValue(":noresep", $this->nomor)
                ->queryOne();
            }
        }

        $this->resepturId = $inforesep['reseptur_id'];
        $this->nomor = $inforesep['noresep'];
        $this->pendaftaranId = $inforesep['pendaftaran_id'];
        $this->penjualanresepId = @$inforesep['penjualanresep_id'];
    }

    protected function validasiCloseBill()
    {
        if(!empty($this->pendaftaranId)) {
            $dataPendaftaran = Yii::$app->db->createCommand("SELECT pendaftaran_id, is_close_bill FROM pendaftaran_t WHERE pendaftaran_id = :pendaftaran_id")
            ->bindValue(":pendaftaran_id", $this->pendaftaranId)
            ->queryOne();
            $isCloseBill = isset($dataPendaftaran['is_close_bill']) ? $dataPendaftaran['is_close_bill'] : null;
            if($isCloseBill) {
                \Yii::$app->response->statusCode = 422;
                throw new Exception('Pasien sudah dilakukan proses Lock Bill.');
            }
        }
    }

    protected function validasiReseptur() {
        if (!empty($this->resepturId)) {
            $this->reseptur = InformasiResepturView::find()
            ->where(['reseptur_id' => $this->resepturId])
            ->one();
        } else {
            $this->reseptur = InformasiResepturView::find()
            ->where(['noresep' => $this->nomor])
            ->one();
        }
        $resepturId = $this->reseptur['reseptur_id'];

        if($this->reseptur['status_worklist'] != DocoConstants::WL_SIAP_DISERAHKAN){
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new Exception('Resep belum siap diserahkan. Silakan proses worklist terlebih dahulu.');
        }

        if($this->reseptur['status_reseptur_id'] == DocoConstants::RESEPTUR_DISERAHKAN) {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new Exception('Resep sudah diserahkan');
        }
        $this->listObat = ResepturDetail::find()->where(['reseptur_id' => $resepturId])->all();
    }

    protected function validasiResep() {
        $this->resep = Yii::$app->db->createCommand("SELECT * FROM penjualanresep_t WHERE penjualanresep_id = :penjualanresep_id")
            ->bindValue(":penjualanresep_id", $this->penjualanresepId)
            ->queryOne();

        if($this->resep['status_worklist'] != DocoConstants::WL_SIAP_DISERAHKAN) {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new Exception('Resep belum siap diserahkan. Silakan proses worklist terlebih dahulu.');
        }

        if($this->resep['status_reseptur'] == DocoConstants::RESEPTUR_DISERAHKAN) {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new Exception('Resep sudah diserahkan.');
        }

        $this->konfigReseptur = $this->konfigCarabayarReseptur();
        // validasi belum lunas jika bypass_worklist = true
        if($this->bypassWorklist && $this->resep['status_bayar'] == DocoConstants::BELUM_LUNAS) {
            $error_return = true;

            // jika resep dari penjualan langsung/rajal, cek status bayar
            if($this->resep['jenispenjualan'] == DocoConstants::PENJUALAN_RESEP_RS) {
                $pendaftaran = Pendaftaran::find()
                                ->select(['instalasi_id'])
                                ->where(['pendaftaran_id' => $this->resep['pendaftaran_id']])
                                ->asArray()->one();
                
                if(empty($pendaftaran)) {            
                    throw new Exception('Permintaan tidak bisa diproses! Nomor pendaftaran tidak ditemukan / dihapus.');
                }

                if(in_array($pendaftaran['instalasi_id'], [DocoConstants::VAR_I_RD, DocoConstants::VAR_I_RANAP])) {
                    $error_return = false;
                }
            }
            
            if ((new DocoConstansId)->actionGetId(DocoConstants::INSTALASI_RAWAT_JALAN) && 
                !empty($this->konfigReseptur['status']) && in_array($this->resep['carabayar_id'], $this->konfigReseptur['carabayar'])) {
                $error_return = false;
            }

            if($error_return) {
                $this->cancelDBTransaction();
                \Yii::$app->response->statusCode = 422;            
                throw new Exception('Resep belum dibayar.');
            }
        }
        
        $this->listObat = Yii::$app->db->createCommand("SELECT * FROM informasiresepdetail_v WHERE noresep = :noresep")
            ->bindValue(":noresep", $this->resep['noresep'])
            ->queryAll();

        $model = new PenjualanResep();
        $model->setAttributes($this->resep, false);
        $this->resep = $model;
    }

    protected function serahkanResep() {
        $this->arrQtyKonversi = $this->arrObatalkesId = [];
        
        if(empty($this->listObat)) {
            throw new Exception('Permintaan tidak bisa diproses! Data detail resep tidak ditemukan / dihapus.');
        }

        foreach ($this->listObat as $key => $value) {
            if(isset($value['det'])) {
                $qty = floatval($value['det']);
                $qty_konversi = floatval($value['det']) * floatval($value['nilai_konversi']);
            } else {
                $qty = !empty($value['qty_oa']) ? $value['qty_oa'] : 0;
                $qty_konversi = !empty($value['qty_konversi']) ? $value['qty_konversi'] : 0;
            }

            $detailTrans[] = [
                'obatalkes_id' => $value['obatalkes_id'],
                'qty_satuanpakai' => $qty_konversi,
                'satuankecil_id' => empty($value['satuankecil_id']) ? 0 : $value['satuankecil_id'],
                'harganetto' => empty($value['harga_netto']) ? 0 : $value['harga_netto'],
                'obatalkespasien_id' => $value['obatalkespasien_id'],
                'persendiscount' => empty($value['persendiscount']) ? 0 : $value['persendiscount'], 
                'persenppn' => empty($value['persenppn']) ? 0 : $value['persenppn'],
                'persenmargin' => empty($value['persenmargin']) ? 0 : $value['persenmargin'],
                'jmldiscount' => empty($value['disc']) ? 0 : $value['disc'],
                'jmlmargin' => empty($value['hn_margin']) ? 0 : $value['hn_margin'],
                'jmlppn' => empty($value['ppn']) ? 0 : $value['ppn'],
            ];

            if (!empty($value['r_ke'])) {
                $obj_array_insert[$key]['rke'] = $value['r_ke'];
            }

            $this->arrRuanganId[] = $this->ruanganId;
            if(array_key_exists($value['obatalkes_id'], $this->arrQtyKonversi)) {
                $qty_konversi = floatval($qty_konversi) + $this->arrQtyKonversi[$value['obatalkes_id']];
                $this->arrQtyKonversi[$value['obatalkes_id']] = $qty_konversi;
            } else {
                $this->arrQtyKonversi[$value['obatalkes_id']] = floatval($qty_konversi);
                $this->arrObatalkesId[] = $value['obatalkes_id'];
            }
        }

        $this->payloadStok = $detailTrans;
        $this->setLogUser($this->resep);
        $this->resep->log_user = $this->logUser;
        $this->resep->pegawai_menyerahkan_id = is_null($this->pegawaiMenyerahkan) ? Yii::$app->jwt->user->pegawai_id : $this->pegawaiMenyerahkan;
        $this->resep->tgl_menyerahkan = date('Y-m-d H:i:s');
        $this->resep->status_reseptur = DocoConstants::RESEPTUR_DISERAHKAN;
        $update = PenjualanResep::updateAll($this->resep->attributes, 'penjualanresep_id = '.$this->resep->penjualanresep_id.'');

        if(!$update) {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new Exception('Gagal update penjualan resep.');
        }
    }

    protected function insertPenjualanResep() {
        $now = date('Y-m-d H:i:s');
        $hasil = Yii::$app->db->createCommand("SELECT penjualanresep_id, reseptur_id FROM penjualanresep_t WHERE reseptur_kronis_asal_id = :reseptur_kronis_asal_id")
        ->bindValue(":reseptur_kronis_asal_id", $this->worklist['reseptur_id'])
        ->queryOne();
        
        $model = new PenjualanResep;
        $model->pegawai_id = $this->reseptur['pegawai_id'];
        $model->pendaftaran_id = $this->reseptur['pendaftaran_id'];
        $model->penjamin_id = $this->reseptur['penjamin_id'];
        $model->kelaspelayanan_id = $this->reseptur['kelaspelayanan_id'];
        $model->carabayar_id = $this->reseptur['carabayar_id'];
        $model->pasien_id = $this->reseptur['pasien_id'];
        $model->pasienadmisi_id = isset($this->reseptur['pasienadmisi_id']) ? $this->reseptur['pasienadmisi_id'] : null;
        $model->ruangan_id = $this->reseptur['ruangan_id'];
        $model->reseptur_id = $this->reseptur['reseptur_id'];
        $model->iter = $this->reseptur['iter'];
        $model->totharganetto = $this->reseptur['total_harganetto'];
        $model->totalhargajual = $this->reseptur['total_tagihan'];
        $model->jenispenjualan = DocoConstants::PENJUALAN_RESEP_RS;
        $model->tglpenjualan = $now;
        $model->tglresep = $now;
        $model->catatan = $this->reseptur['catatan'];
        $model->status_reseptur = DocoConstants::RESEPTUR_DISERAHKAN;
        $model->status_worklist = DocoConstants::WL_SIAP_DISERAHKAN;
        $model->biayaadministrasi = $this->reseptur['biaya_administrasi'];
        $model->pegawai_menyerahkan_id = is_null($this->pegawaiMenyerahkan) ? Yii::$app->jwt->user->pegawai_id : $this->pegawaiMenyerahkan;
        $model->tgl_menyerahkan = $now;
        $model->hasil_resep_kronis_id = !empty($hasil) ? $hasil['penjualanresep_id'] : null;

        // log user
        $this->setLogUser($model);
        $model->log_user = $this->logUser;
        if (!$model->validate() || !$model->save()) {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new Exception('Gagal menyimpan resep.');
        }

        $this->penjualanresepId = $model->getPrimaryKey();
        $model->refresh();
        $this->resep = $model->attributes;
        $this->nomor = $model->noresep;

    }
    
    protected function updateLogSerahkan() {
        if(!empty($this->resepturId) && $this->ruanganAsalId != DocoConstants::VAR_I_RJ){
            $this->resep = PenjualanResep::find()->where(['noresep' => $this->nomor])->one();
        }elseif(!empty($this->penjualanresepId)){
            $this->resep = PenjualanResep::find()->where(['penjualanresep_id' => $this->penjualanresepId])->one();
        }

        if(empty($this->resep) || !$this->resep instanceof PenjualanResep){
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new Exception('Gagal Update Status Resep');
        }
        
        if(isset($this->logUser)){
            $this->resep->log_user = $this->logUser;
        }else{
            $this->setLogUser($this->resep);
        }
        $this->resep->pegawai_menyerahkan_id = is_null($this->pegawaiMenyerahkan) ? Yii::$app->jwt->user->pegawai_id : $this->pegawaiMenyerahkan;
        $this->resep->tgl_menyerahkan = date('Y-m-d H:i:s');
        $this->resep->status_reseptur = DocoConstants::RESEPTUR_DISERAHKAN;
        $this->resep->save();
    }

    protected function insertBilling() {
        $now = date('Y-m-d H:i:s');
        $this->arrQtyKonversi = $this->arrObatalkesId = [];
        $obj_array_insert = $payload_oapasien = [];
        foreach ($this->listObat as $value) {
            $additional_reseptur = json_decode($value['additional_data'], true);
            if(isset($value['det'])) {
                $qty = floatval($value['det']);
                $qty_konversi = floatval($value['det']) * floatval($additional_reseptur['nilai_konversi']);
            } else {
                $qty = !empty($value['qty_reseptur']) ? $value['qty_reseptur'] : 0;
                $qty_konversi = !empty($value['qty_konversi']) ? $value['qty_konversi'] : 0;
            }

            $obj_array_insert[$value['obatalkes_id']] = [
                'ruangan_id' => $this->reseptur['ruangan_id'],
                'carabayar_id' => $this->reseptur['carabayar_id'],
                'penjamin_id' => $this->reseptur['penjamin_id'],
                'pendaftaran_id' => $this->reseptur['pendaftaran_id'],
                'pegawai_id' => $this->reseptur['pegawai_id'],
                'satuankecil_id' => !is_null($value['satuankecil_id']) ? $value['satuankecil_id'] : null,
                'racikan_id' => $value['racikan_id'],
                'rke' => $value['rke'],
                'obatalkes_id' => $value['obatalkes_id'],
                'penjualanresep_id' => $this->penjualanresepId,
                'tglpelayanan' => $now,
                'qty_oa' => $qty, // disimpan di obatalkespasien_t
                'qty_konversi' => $qty_konversi, // disimpan di obatalkespasien_t
                'qty_reseptur' => $qty, // disimpan di obatalkespasien_t
                'qty_satuanpakai' => $qty_konversi, // untuk potong stok di stokobatalkes_t
                'qty_medis' => @$value['qty_medis'],
                'det' => isset($value['det']) ? $value['det'] : null,
                'det_konversi' => isset($value['det_konversi']) ? $value['det_konversi'] : null,
                'det_medis' => isset($value['det_medis']) ? $value['det_medis'] : null,
                'hargajual_oa' => empty($value['hargasatuan_reseptur'])? 0 : $value['hargasatuan_reseptur'],
                'harganetto_oa' => empty($value['harganetto_reseptur'])? 0 : $value['harganetto_reseptur'],
                'hargasatuan_oa' => empty($value['hargasatuan_reseptur'])? 0 : $value['hargasatuan_reseptur'],
                'hargasatuan_reseptur' => empty($value['hargasatuan_reseptur'])? 0 : $value['hargasatuan_reseptur'],
                'harganetto_reseptur' => empty($value['harganetto_reseptur'])? 0 : $value['harganetto_reseptur'],
                'hargajual_reseptur' => empty($value['hargasatuan_reseptur'])? 0 : $value['hargasatuan_reseptur'],
                'signa_oa' => $value['signa_id'],
                'signa_id' => $value['signa_id'],
                'signa' => json_encode($value['signa']),
                // 'created_by' => $jwt->user->loginpemakai_id,
                'additional_data' => $value['additional_data'],
                'etiket' => $value['etiket'],
                'is_kronis' => isset($value['is_kronis']) ? $value['is_kronis'] : false,
                'is_ditagihkan' => true,
                'nama_racikan' => !empty($value['nama_racikan']) ? $value['nama_racikan'] : null,
                'qty_racikan' => !empty($value['qty_racikan']) ? $value['qty_racikan'] : null,
                'satuan_racikan_id' => !empty($value['satuan_racikan_id']) ? $value['satuan_racikan_id'] : null,
            ];

            if (!empty($value['r_ke'])) {
                $obj_array_insert[$value['obatalkes_id']]['rke'] = $value['r_ke'];
            }

            $payload_oapasien[] = $obj_array_insert[$value['obatalkes_id']];
            $this->arrRuanganId[] = $this->ruanganId;
            if(array_key_exists($value['obatalkes_id'], $this->arrQtyKonversi)) {
                $qty_konversi = floatval($qty_konversi) + $this->arrQtyKonversi[$value['obatalkes_id']];
                $this->arrQtyKonversi[$value['obatalkes_id']] = $qty_konversi;
            } else {
                $this->arrQtyKonversi[$value['obatalkes_id']] = floatval($qty_konversi);
                $this->arrObatalkesId[] = $value['obatalkes_id'];
            }
        }

        /*Parent Transaction OA*/
        $trx_oa = [
            'primary_key' => 'penjualanresep_id',
            'penjualanresep_id' => $this->penjualanresepId,
            'pendaftaran_id' => $this->reseptur['pendaftaran_id'],
            'carabayar_id' => $this->reseptur['carabayar_id'],
            'pasien_id' => $this->reseptur['pasien_id'],
            'penjamin_id' => $this->reseptur['penjamin_id'],
            'ruangan_id' => $this->reseptur['ruangan_id'],
            'pasienadmisi_id' => isset($this->reseptur['pasienadmisi_id']) ? $this->reseptur['pasienadmisi_id'] : null,
            'kelaspelayanan_id' => $this->reseptur['kelaspelayanan_id']
        ];

        $obatAlkesPasien = new ObatAlkesPasien();
        $obatAlkesPasien->loadFromReseptur($payload_oapasien, $trx_oa, $this->penjualanresepId);
        $totalTarif = $obatAlkesPasien->getTotalTarif();
        $validasiPlafon = new PlafonBpjsService($this->pendaftaranId, $totalTarif);
        $result = $validasiPlafon->validasiPlafon();
        if (!$result['isValid']) {
            \Yii::$app->response->statusCode = 422;
            throw new Exception($result['message'] ? $result['message'] : 'Validasi Plafon Gagal');
        }
        $obatAlkesPasien->save();
    }

    protected function updateStatusReseptur() {
        $update = true;
        if(!empty($this->ruanganAsalId) || (!empty($this->penjualanresepId) && !empty($this->resepturId))) {
            $update = Reseptur::updateAll([
                'status_reseptur' => DocoConstants::RESEPTUR_DISERAHKAN,
                'penjualanresep_id' => $this->penjualanresepId
            ],'reseptur_id = '.$this->resepturId.'');
        } else if($this->ruanganAsalId == DocoConstants::VAR_I_RJ || !empty($this->resepturId)) {
            $update = Reseptur::updateAll([
                'status_reseptur' => DocoConstants::RESEPTUR_DISERAHKAN
            ],'reseptur_id = '.$this->resepturId.'');
        }

        if(!$update) {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new Exception('Gagal update status reseptur.');
        }
    }

    protected function updateResepKronisAsal() {
        $resepAsal = $this->penjualanresepId;
        if(!empty($resepAsal)){
            $update = PenjualanResep::find()->where(['reseptur_kronis_asal_id' => $this->worklist['reseptur_id']])->one();
            if(!empty($update)){
                $update->resep_kronis_asal_id = $this->penjualanresepId;
                $update->save();
            }
        }
    }
    
    protected function potongStok() {
        return (new FarmasiService)->potongStok([
            'detail_obat' => $this->payloadStok
        ]);
    }
    
    protected function serahkanReseptur() {
        $dataOAPasien = ModelOAPasien::find()
                        ->select(['*', 'coalesce(det, qty_oa) AS qty_satuanpakai'])
                        ->where(['penjualanresep_id' => $this->penjualanresepId])
                        ->asArray()->all();
        $this->payloadStok = $dataOAPasien;
    }

    protected function setPayloadWorklist() {
        $jwt = Yii::$app->jwt;
        $user_profile = $jwt->user;
        // $pegawai = PegawaiView::find()
        //     ->select(['pegawai_id', 'nama_pegawai'])
        //     ->where(['pegawai_id' => $user_profile->pegawai_id])
        //     ->asArray()->one();
        
        $pegawai = Yii::$app->db->createCommand("SELECT pegawai_id, nama_pegawai FROM pegawai_m WHERE pegawai_id = :pegawai_id")
        ->bindValue(":pegawai_id", $user_profile->pegawai_id)
        ->queryOne();
        $nama_pegawai = is_null($pegawai) ? $user_profile->nama_pemakai : $pegawai['nama_pegawai'];
        
        $lookup_status_worklist = Yii::$app->db->createCommand("SELECT * FROM lookup_m WHERE lookup_id = :lookup_id")
        ->bindValue(":lookup_id", DocoConstants::WL_SIAP_DISERAHKAN)
        ->queryAll();
        $lookup_status_worklist = ArrayHelper::map($lookup_status_worklist, 'lookup_id', 'lookup_name');

        // $this->worklist = WorklistView::find()
        //     ->select(['penjualanresep_id', 'reseptur_id'])
        //     ->where(['no_reseptur' => $this->nomor])
        //     ->asArray()->one();

        $this->worklist = Yii::$app->db->createCommand("SELECT 
                penjualanresep_id,
                reseptur_id,
                pendaftaran_id, 
                noresep,
                additional_data, 
                status_worklist
            FROM penjualanresep_t 
            WHERE noresep = :noresep"
        )
        ->bindValue(":noresep", $this->nomor)
        ->queryOne();

        if(empty($this->worklist)){
            // $this->worklist = WorklistView::find()
            // ->select(['penjualanresep_id', 'reseptur_id'])
            // ->where(['no_resep' => $this->nomor])
            // ->asArray()->one();

            $this->worklist = Yii::$app->db->createCommand("SELECT 
                    penjualanresep_id,
                    reseptur_id,
                    pendaftaran_id, 
                    noresep,
                    additional_data, 
                    status_worklist
                FROM reseptur_t 
                WHERE noresep = :noresep"
            )
            ->bindValue(":noresep", $this->nomor)
            ->queryOne();
        }

        $this->logWorklist = [
            'tanggal' => date('d M Y H:i:s'),
            'pegawai_id' => $user_profile->pegawai_id,
            'nama_pegawai' => $nama_pegawai,
            'status_worklist_id' => DocoConstants::WL_SIAP_DISERAHKAN,
            'status_worklist' => $lookup_status_worklist[DocoConstants::WL_SIAP_DISERAHKAN],
        ];
    }

    protected function updateStatusWorklist() {
        if (!is_null($this->worklist['penjualanresep_id'])) {
            /** set dari $this->worklist tidak perlu query ulang*/
            $penjualan_resep = $this->worklist;

            $status_history = json_decode($penjualan_resep['additional_data'], true);
            if(is_null($status_history)) {
                $status_history['log_status'] = [];
            }
            $status_history['log_status'][date('YmdHis')] = $this->logWorklist;
            $penjualan_resep = PenjualanResep::updateAll(
                [
                    'additional_data' => json_encode($status_history),
                    'status_worklist' => DocoConstants::WL_SIAP_DISERAHKAN,
                ], 'penjualanresep_id = '.$this->worklist['penjualanresep_id'].'');
        }
        if (!is_null($this->worklist['reseptur_id'])) {
            /** set dari $this->worklist  tidak perlu query ulang*/
            $reseptur = $this->worklist;
            $status_history = json_decode($reseptur['additional_data'], true);
            if(is_null($status_history)) {
                $status_history['log_status'] = [];
            }
            $status_history['log_status'][date('YmdHis')] = $this->logWorklist;
            $reseptur = Reseptur::updateAll(
                [
                    'additional_data' => json_encode($status_history),
                    'status_worklist' => DocoConstants::WL_SIAP_DISERAHKAN,
                ], 'reseptur_id = '.$this->worklist['reseptur_id'].'');
        }
    }

    protected function setLogUser($model) {
        $jwt = Yii::$app->jwt;
        $log_user = json_decode($model->log_user,true);
        if(ArrayHelper::getValue($log_user, 'audit', null) == null) $log_user['audit'] = [];
        $log_user['audit'][time()] = [
            'service_name' => Yii::$app->params['service'],
            'detail' => !empty($jwt->activity) ? json_encode($jwt->activity) : null,
            'action' => !empty($jwt->action) ? $jwt->action : null,
            'stamp' => date('Y-m-d H:i:s'),
            'pegawai_id' => $model->pegawai_id,
            'pegawai_menyerahkan' => $this->pegawaiMenyerahkan,
            'loginpemakai_id' => !empty($jwt->user->loginpemakai_id) ? $jwt->user->loginpemakai_id : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'url_referer' => $_SERVER['REQUEST_URI'],
            'browser' => @$_SERVER['HTTP_USER_AGENT'],
            'http_method' => $_SERVER['REQUEST_METHOD']
        ];

        $this->logUser = $log_user;
    }

    protected function updateInfoStok() {
        // untuk mengembalikan stok dipesan
        $inCondition = "(" . implode(", ", $this->arrObatalkesId) . ")";
        $str = "SELECT
                    qty_dipesan, qty_tersedia, qty_sisa, obatalkes_id
                FROM stokobatalkes_r
                WHERE obatalkes_id IN $inCondition
                    AND ruangan_id = $this->ruanganId";
        $stok_obat = Yii::$app->db->createCommand($str)->queryAll();
        foreach ($stok_obat as $value) {
            $qty_dipesan[$value['obatalkes_id']] = floatval($value['qty_dipesan']);
            $qty_tersedia[$value['obatalkes_id']] = floatval($value['qty_tersedia']);
        }

        foreach ($this->arrObatalkesId as $key => $value) {
            $arr_qty_dipesan[] = $qty_dipesan[$value] - floatval($this->arrQtyKonversi[$value]);
            if($qty_tersedia[$value] != 0) {
                $arr_qty_tersedia[] = $qty_tersedia[$value] + floatval($this->arrQtyKonversi[$value]);
            } else {
                $arr_qty_tersedia[] = $qty_tersedia[$value];
            }
        }

        $obat_dipesan = [
            'qty_dipesan' => $arr_qty_dipesan,
            'qty_tersedia' => $arr_qty_tersedia
        ];

        $updateCondition = [
            'obatalkes_id' => $this->arrObatalkesId,
            'ruangan_id' => $this->arrRuanganId
        ];

        $updateStok = ApotekComponent::updateMultiple('stokobatalkes_r', $obat_dipesan, $updateCondition);
        if(!$updateStok) {
            $this->cancelDBTransaction();
            \Yii::$app->response->statusCode = 422;
            throw new Exception('Gagal update info stok.');
        }
    }

    protected function processFlow() {
        $time1 = microtime(true);
        try {
            $this->validasiPayload();
            $this->getKonfigWorklist();
            if($this->bypassWorklist) {
                $this->setPayloadWorklist();
                $this->startDBTransaction();
                $this->updateStatusWorklist();
                $this->commitDBTransaction();
            } 
            
            $this->startDBTransaction();
            $this->getInfoResep();
            if ($this->ruanganAsalId != DocoConstants::VAR_I_RJ && !empty($this->resepturId)) { // Skip proses validasi lock-bill untuk penjualan langsung & resep doker RJ
                $this->validasiCloseBill();
            }
            if(is_null($this->resepturId)) { // penjualan langsung
                $this->validasiResep();
                $this->serahkanResep();
            } else if(!empty($this->resepturId) && $this->ruanganAsalId == DocoConstants::VAR_I_RJ && !empty($this->penjualanresepId)) { // resep dokter RJ
                $this->validasiResep();
                $this->serahkanResep();
                $this->updateStatusReseptur();
            } else { // resep dokter RI/RD
                $this->validasiReseptur();
                $this->insertPenjualanResep();
                $this->insertBilling();
                $this->serahkanReseptur();
                $this->updateResepKronisAsal();
                $this->updateStatusReseptur();
            }

            $this->updateLogSerahkan();
            $potongStok = $this->potongStokNew();

            if(isset($potongStok['meta']['code']) && $potongStok['meta']['code'] != 200 ) {
                \Yii::$app->response->statusCode = 422;
                return $potongStok;
            }

            $this->commitDBTransaction();
            (new RabbitBgProcess())->send([], 'trigger_update_notif_farmasi', 'push_notif_farmasi');
            return $this->responseJson(200, 'Resep berhasil diserahkan.', [
                'pendaftaran_id' => ArrayHelper::getValue($this->resep, 'pendaftaran_id'),
                'reseptur_id' => $this->resepturId,
                'from' => 'serahkan_obat_process',
                'time' => number_format(microtime(true)-$time1, 2),
            ]);
        } catch(\Exception $e) {
            $this->logError($e);
            return $this->responseJson(422, $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
        } catch(\yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, $e->getMessage());
        }
    }

    protected function konfigCarabayarReseptur() {
        $konfig = isset($this->konfigFarmasi['konfig_carabayar_reseptur']) ? $this->konfigFarmasi['konfig_carabayar_reseptur'] : null;
        return json_decode($konfig, true); 
    }

    protected function potongStokNew() {
        return (new PotongStokSerahkanResep)->potongStokSerahkanObat($this->payloadStok);
    }
}
