<?php

namespace Integrasi\Service\Ris;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use yii\db\Query;
use Integrasi\Service\Ris\Models\PemeriksaanPasienRadiologiView;
use Integrasi\Service\Ris\Models\RekapRis;
use Integrasi\Components\Services\RadiologiService;
use Integrasi\Components\Object\RisObject;
use Integrasi\Components\Object\RekapRisObject;

class RisBroker extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $penunjangId = (int) $this->pasienmasukpenunjang_id;
        $regisId = (int) $this->pendaftaran_id;
        $unitId = (int) $this->pasienkirimkeunitlain_id;
        $isAps = $this->is_aps;
        $id = ArrayHelper::getValue($this->result, 'id');

        // Ini Trigger dari pendafatran penunjang
        if (!empty($id)) {
            $regisId = DocoHelpers::decrypt($id);
        }

        $integrasi = $this->getData($penunjangId, $regisId, $unitId);
        $listRekap = [];

        foreach ($integrasi as $value) {
            $status = ArrayHelper::getValue($value, 'status');
            $groupCaraBayar = ArrayHelper::getValue($value, 'groupcarabayar_id');
            $penjaminId = ArrayHelper::getValue($value, 'penjamin_id');
            $instalasi = ArrayHelper::getValue($value,'instalasi_id');
            $noPembayaran = ArrayHelper::getValue($value, 'no_pembayaran');

            /**
             * Kondisi Ketika Aps dan bayar Umum Harus Bayar Dulu
             */

            $pasienAps = ($groupCaraBayar == DocoConstants::GROUP_UMUM && empty($noPembayaran));

            if ($status == 'FINISH' || $pasienAps) continue;

            $diagnosa = isset($value['nama_diagnosa']) 
                            ? json_decode($value['nama_diagnosa'], true) : [];
            $idRekap = ArrayHelper::getValue($value, 'id');

            $diagnosaNama = null;
            if (!empty($diagnosa) || is_array($diagnosa)) {
                $diagnosaNama = isset($diagnosa['text']) ? $diagnosa['text'] : null;
            }

            $pasienClass = $instalasi == DocoConstants::INST_ID_RI 
                                        ? 'I' : (($instalasi == DocoConstants::INST_ID_RD) ? 'E' : 'O');
            $pelayananId = ArrayHelper::getValue($value, 'tindakanpelayanan_id');

            $rekapRis = new RekapRisObject;
            $rekapRis->pendaftaranId = ArrayHelper::getValue($value, 'pendaftaran_id');
            $rekapRis->pasienmasukpenunjangId = ArrayHelper::getValue($value, 'pasienmasukpenunjang_id');
            $rekapRis->daftartindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
            $rekapRis->tindakanpelayananId = $pelayananId;
            $rekapRis->noPembayaran = $noPembayaran;

            $obj = new RisObject;
            $obj->userId = ArrayHelper::getValue($value, 'created_by');
            $obj->userName = ArrayHelper::getValue($value, 'nama_pemakai');
            $obj->patientId = ArrayHelper::getValue($value, 'no_rekam_medik');
            $obj->caseNo = ArrayHelper::getValue($value, 'no_pendaftaran');
            $obj->patientName = ArrayHelper::getValue($value, 'nama_pasien');
            $obj->dateOfBirth = ArrayHelper::getValue($value, 'tanggal_lahir');
            $obj->gender = ArrayHelper::getValue($value, 'jeniskelamin');
            $obj->address = ArrayHelper::getValue($value, 'alamat_pasien');
            $obj->postalCode = ArrayHelper::getValue($value, 'kode_pos');
            $obj->phoneNo = ArrayHelper::getValue($value, 'no_telepon_pasien');
            $obj->patientClass = $pasienClass;
            $obj->admissionType = $pasienClass;
            $obj->chargePrice = $penjaminId;
            $obj->locationPoc = ArrayHelper::getValue($value, 'kode_ruangan');
            $obj->locationRoom = ArrayHelper::getValue($value, 'ruangan_nama');
            $obj->isVip = false;
            $obj->financialClass = $penjaminId;
            $obj->noMasukpenunjang = ArrayHelper::getValue($value, 'no_masukpenunjang');
            $obj->quantity = (int) ArrayHelper::getValue($value, 'qty_tindakan');
            $obj->isMcu = ArrayHelper::getValue($value, 'is_mcu');
            $obj->payerCode = ArrayHelper::getValue($value, 'penjamin_kode');
            $obj->packetName = ArrayHelper::getValue($value, 'tipepaket_nama');
            $obj->admitTime = ArrayHelper::getValue($value, 'tglmasukpenunjang');
            $obj->clinicalInfo = $diagnosaNama;
            $obj->patientRisk = ArrayHelper::getValue($value, 'catatan_dokterpengirim');
            $obj->billNo = $noPembayaran;
            $obj->procedureCode = ArrayHelper::getValue($value, 'daftartindakan_kode');
            $obj->procedureName = ArrayHelper::getValue($value, 'daftartindakan_nama');

            $orderNo = $obj->noMasukpenunjang . '-' . $pelayananId;

            $obj->orderNo = $orderNo;
            $obj->fillerOrder = $orderNo;
            $obj->refDoctorId = ArrayHelper::getValue($value, 'nomorindukpegawai');
            $obj->refDoctorName = ArrayHelper::getValue($value, 'dokter_perujuk_nama');
            $obj->priority = ArrayHelper::getValue($value, 'cyto_tindakan');
            $obj->groupNo = $pelayananId;
            $obj->modalityType = ArrayHelper::getValue($value, 'modality_kode');;
            $payload = $obj->buildArray();
            $rekapRis->payload = json_encode($payload);

            if ($status == 'UPDATE') {
                $response = (new RadiologiService)->orderRadiologi($payload, function ($data) {
                    return $data;
                }, 'PATCH');
                $prosesId = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                $responUpdate = json_encode($response);
                $update = [];
                $update['payload'] = $rekapRis->payload;
                $update['is_update'] = true;
                $update['id_sync_sercon_update'] = $prosesId;
                $update['no_pembayaran'] = $noPembayaran;
                $update['sync_respon_update'] = $responUpdate;
                RekapRis::updateAll($update,['id' => $idRekap]);
            } else {
                $response = (new RadiologiService)->orderRadiologi($payload, function ($data) {
                    return $data;
                }, 'POST');
                $rekapRis->idSyncSercon = isset($response['ProcessUID']) ? $response['ProcessUID'] : null;
                $rekapRis->syncRespon = json_encode($response);
                $rekapRis->isSending = true;
                $listRekap[] = $rekapRis->buildArray();
            }
        }
        RekapRis::batchInsert($listRekap);
        $result = [
            'service' => 'Ris-RisBroker',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        return json_encode($result);
    }

    private function getData($penunjangId, $regisId, $unitId)
    {
        if (!empty($penunjangId) || !empty($regisId) || !empty($unitId)) {
            $integrasi = (new \yii\db\Query())
                            ->select([
                                'a.pasienmasukpenunjang_id', 
                                'CASE
                                    WHEN (b.id IS NULL OR b.id_sync_sercon IS NULL) THEN
                                        \'INSERT\'
                                    WHEN (b.no_pembayaran IS NULL AND e.no_pembayaran IS NOT NULL) THEN
                                        \'UPDATE\'
                                    ELSE
                                        \'FINISH\'
                                END as status',
                                'a.created_by',
                                'f.nama_pemakai',
                                'a.no_rekam_medik',
                                'a.no_pendaftaran',
                                'a.nama_pasien',
                                'a.tanggal_lahir',
                                'a.jeniskelamin',
                                'a.pendaftaran_id',
                                'g.alamat_pasien',
                                new Expression('NULL as kode_pos'),
                                'a.no_telepon_pasien',
                                'a.tipe_pasien',
                                'a.asalrujukan_id',
                                'a.penjamin_id',
                                'h.ruangan_singkatan as kode_ruangan',
                                'h.ruangan_nama',
                                'a.no_masukpenunjang',
                                'a.penjamin_kode',
                                'a.is_mcu',
                                'a.tipepaket_nama',
                                'a.tglmasukpenunjang',
                                'a.catatan_dokterpengirim',
                                'a.daftartindakan_id',
                                'a.tindakanpelayanan_id',
                                'a.cyto_tindakan',
                                'i.daftartindakan_kode',
                                'a.daftartindakan_nama',
                                'a.qty_tindakan',
                                'e.no_pembayaran',
                                'a.dokter_perujuk_id',
                                'a.dokter_perujuk_nama',
                                'j.nomorindukpegawai',
                                'b.id',
                                'a.nama_diagnosa',
                                'h.instalasi_id',
                                'a.groupcarabayar_id',
                                'l.modality_kode'
                            ])
                            ->from('infopasienradiologi_v a')
                            ->leftjoin('rekapanris_r as b', 'a.daftartindakan_id = b.daftartindakan_id AND a.tindakanpelayanan_id = b.tindakanpelayanan_id AND a.pasienmasukpenunjang_id = b.pasienmasukpenunjang_id')
                            ->innerjoin('tindakanpelayanan_t c', 'c.tindakanpelayanan_id = a.tindakanpelayanan_id')
                            ->leftjoin('tindakansudahbayar_t d', 'd.tindakansudahbayar_id = c.tindakansudahbayar_id')
                            ->leftjoin('pembayaranpelayanan_t e', 'e.pembayaranpelayanan_id = d.pembayaranpelayanan_id')
                            ->leftjoin('loginpemakai_k f', 'f.loginpemakai_id = a.created_by')
                            ->innerjoin('pasien_m g', 'g.pasien_id = a.pasien_id')
                            ->innerjoin('ruangan_m h', 'h.ruangan_id = a.ruanganasal_id')
                            ->leftjoin('daftartindakan_m i', 'i.daftartindakan_id = a.daftartindakan_id')
                            ->leftjoin('pegawai_m j', 'a.dokter_perujuk_id = j.pegawai_id')
                            ->leftjoin('pemeriksaanrad_m k', 'a.daftartindakan_id = k.daftartindakan_id')
                            ->leftjoin('modalitytype_m l', 'k.modalitytype_id = l.modalitytype_id')
                            ->andWhere([
                                'b.no_pembayaran' => NULL,
                            ]);

            if (!empty($penunjangId)) {
                $integrasi->andWhere([
                    'a.pasienmasukpenunjang_id' => $penunjangId
                ]);
            }

            if (!empty($regisId)) {
                $integrasi->andWhere([
                    'a.pendaftaran_id' => $regisId
                ]);
            }

            if (!empty($unitId)) {
                $integrasi->andWhere([
                    'pasienkirimkeunitlain_id' => $unitId
                ]);
            }

            return $integrasi->all();
        }
        return [];
    }
}