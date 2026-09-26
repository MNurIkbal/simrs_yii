<?php

namespace Integrasi\Service\Mhg;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\Object\BslObject;
use Integrasi\Components\DocoConstansId;
use Integrasi\Service\Mhg\Models\InfoPasienLabDetailView;
use Integrasi\Service\Mhg\Models\RekapanBsl;

class BslBilling extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $listRekap = [];
        $idRd = (int) (new DocoConstansId)->actionGetId('RD');
        foreach ($this->getData() as $value) {
            $payload = new BslObject;
            $pelId = $value['tindakanpelayanan_id'];
            $billNo = $value['no_pembayaran'];
            $instalasiId = $value['instalasi_id'];

            $payload->RegistrationDate = $value['tgl_pendaftaran'];
            $payload->RegistrationNumber = $value['no_pendaftaran'];
            $payload->BillDate = $value['tgl_pembayaran'];
            $payload->BillNumber = $value['no_pembayaran'];
            $payload->MRNumber = $value['no_rekam_medik'];
            $payload->PatientName = $value['nama_pasien'];
            $payload->Gender = $value['jenis_kelamin'];
            $payload->DOB = $value['tanggal_lahir'];
            $payload->IdCardNumber = $value['no_identitas_pasien'];
            $payload->Address = $value['alamat_pasien'];
            $payload->Email = $value['alamatemail'];
            $payload->PhoneNumber = $value['no_telepon_pasien'];
            $payload->LabOrderNumber = !empty($value['no_masukpenunjang']) ? $value['no_masukpenunjang'] : $payload->RegistrationNumber;
            $payload->LabOrderDate = $value['tglmasukpenunjang'];
            $payload->LabOrderDetailId = $pelId;
            $payload->PcrServiceCode = $value['daftartindakan_kode'];
            $payload->IsCanceled = false;
            $payload->KodePenjamin = $value['penjamin_kode'];
            $payload->NamaPenjamin = $value['penjamin_nama'];
            $payload->Instalasi = $value['instalasi_nama'];
            $payload->Ruangan = $value['ruangan_nama'];
            $payload->BillableBedType = $value['kelaspelayanan_nama'];
            $payload->PaymentMethod = $value['carabayar_nama'];
            $payload->ServiceItemAmountCash = $value['dibayar_pasien'];
            $payload->ServiceItemAmountJaminan = $value['dibayar_penjamin'];
            $payload->UserIDKasir = $value['created_by'];
            $payload->TotalNetBillAmount = $value['tarif_tindakan'];
            $payload->PrimaryDoctorCode = $value['primary_dokter_code'];
            $payload->PrimaryDoctorName = $value['primary_dokter'];
            $payload->ReffererDoctorCode = $value['reff_dokter_code'];
            $payload->ReffererDoctorName = $value['reff_dokter'];
            // Case IPD

            $payload->IsDispatched = false;

            $reCheck = RekapanBsl::find()
                    ->andWhere([
                        'tindakanpelayanan_id' => $pelId,
                        'daftartindakan_id' => $value['daftartindakan_id'],
                        'is_sent' => false
                    ])
                    ->orderBy([
                        'id' => SORT_DESC
                    ])
                    ->asArray()
                    ->one();
            if (empty($reCheck)) {
                $listRekap[] = [
                    'pendaftaran_id' => $value['pendaftaran_id'],
                    'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
                    'tindakanpelayanan_id' => $pelId,
                    'daftartindakan_id' => $value['daftartindakan_id'],
                    'tipepaket_id' => $value['tipepaket_id'],
                    'no_pembayaran' => $billNo,
                    'payload' => json_encode($payload->buildArray()),
                ];
            } else {
                $payloadRekap = !empty($reCheck['payload']) ? json_decode($reCheck['payload'], true) : null;
                $idRekap = !empty($reCheck['id']) ? $reCheck['id'] : null;
                if (!empty($payloadRekap['IsCanceled'])) {
                    $listRekap[] = [
                        'pendaftaran_id' => $value['pendaftaran_id'],
                        'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
                        'tindakanpelayanan_id' => $pelId,
                        'daftartindakan_id' => $value['daftartindakan_id'],
                        'tipepaket_id' => $value['tipepaket_id'],
                        'no_pembayaran' => $billNo,
                        'payload' => json_encode($payload->buildArray()),
                    ];
                } else {
                    RekapanBsl::updateAll([
                        'no_pembayaran' => $billNo,
                        'payload' => json_encode($payload->buildArray())
                    ], ['id' => $idRekap]);
                }
            }
        }

        if (!empty($listRekap)) {
            RekapanBsl::batchInsert($listRekap);
        }

        return json_encode([
            'service' => 'Mhg-BslBilling',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getData()
    {
        $regisId = (int) $this->pendaftaran_id;
        $unitId = (int) $this->pasienkirimkeunitlain_id;
        $registNo = $this->no_pendaftaran;

        $pemeriksaanPcr = (int) (new DocoConstansId)->actionGetId('pemeriksaan_pcr');
        if (empty($regisId) && empty($unitId) && empty($registNo)) return [];

        $qLab = InfoPasienLabDetailView::find()
            ->select([
                'infopasienlabdetail_v.pendaftaran_id',
                'infopasienlabdetail_v.tindakanpelayanan_id',
                'infopasienlabdetail_v.pasienmasukpenunjang_id',
                'infopasienlabdetail_v.daftartindakan_id',
                'c.pasienadmisi_id',
                'infopasienlabdetail_v.tipepaket_id',
                'c.no_pendaftaran',
                'c.tgl_pendaftaran',
                'f.no_pembayaran',
                'f.tgl_pembayaran',
                'g.no_rekam_medik',
                'g.nama_pasien',
                'h.lookup_value as jenis_kelamin',
                'g.tanggal_lahir',
                'g.no_identitas_pasien',
                'g.alamat_pasien',
                'g.alamatemail',
                'g.no_telepon_pasien',
                'd.no_masukpenunjang',
                'd.tglmasukpenunjang',
                'i.daftartindakan_kode',
                'j.penjamin_kode',
                'j.penjamin_nama',
                'l.instalasi_nama',
                'k.ruangan_nama',
                'm.kelaspelayanan_nama',
                'n.carabayar_nama',
                'CASE 
                    WHEN (o.total_dijamin = 0 OR o.total_dijamin IS NULL) THEN
                        infopasienlabdetail_v.tarif_tindakan
                    ELSE
                        0
                END as dibayar_pasien',
                'CASE 
                    WHEN (o.total_dijamin > 0 OR o.total_dijamin IS NOT NULL) THEN
                        infopasienlabdetail_v.tarif_tindakan
                    ELSE
                        0
                END as dibayar_penjamin',
                'o.created_by',
                'infopasienlabdetail_v.tarif_tindakan',
                'p.nomorindukpegawai as primary_dokter_code',
                'p.nama_pegawai as primary_dokter',
                'r.nama_pegawai as reff_dokter',
                'r.nomorindukpegawai as reff_dokter_code',
                'c.instalasi_id',
            ])
            // ->alias('a')
            ->innerjoin('tindakanpelayanan_t b', 'infopasienlabdetail_v.tindakanpelayanan_id = b.tindakanpelayanan_id')
            ->innerjoin('pendaftaran_t c', 'c.pendaftaran_id = infopasienlabdetail_v.pendaftaran_id')
            ->leftjoin('pasienmasukpenunjang_t d', 'd.pasienmasukpenunjang_id = infopasienlabdetail_v.pasienmasukpenunjang_id')
            ->leftjoin('tindakansudahbayar_t e', 'e.tindakansudahbayar_id = b.tindakansudahbayar_id  AND e.is_deleted = FALSE')
            ->leftjoin('pembayaranpelayanan_t f', 'f.pembayaranpelayanan_id = e.pembayaranpelayanan_id AND f.is_deleted = FALSE')
            ->innerjoin('pasien_m g', 'g.pasien_id = c.pasien_id')
            ->innerjoin('lookup_m h', 'h.lookup_id = g.jeniskelamin::int')
            ->innerjoin('daftartindakan_m i', 'i.daftartindakan_id = infopasienlabdetail_v.daftartindakan_id')
            ->innerjoin('penjamin_m j', 'j.penjamin_id = b.penjamin_id')
            ->innerjoin('ruangan_m k', 'k.ruangan_id = b.ruangan_id')
            ->innerjoin('instalasi_m l', 'l.instalasi_id = k.instalasi_id')
            ->innerjoin('kelaspelayanan_m m', 'm.kelaspelayanan_id = b.kelaspelayanan_id')
            ->innerjoin('carabayar_m n', 'n.carabayar_id = j.carabayar_id')
            ->leftjoin('pembayaran_t o', 'o.pembayaran_id = f.pembayaran_id')
            ->leftjoin('pegawai_m p', 'p.pegawai_id = b.dokterpenanggungjawab_id')
            ->leftjoin('pasienkirimkeunitlain_t q', 'q.pasienkirimkeunitlain_id = d.pasienkirimkeunitlain_id')
            ->leftjoin('pegawai_m r', 'r.pegawai_id = q.pegawai_id')
            ->innerjoin('pemeriksaanlab_m s', 's.daftartindakan_id = infopasienlabdetail_v.daftartindakan_id')
            ->andWhere([
                'b.is_deleted' => false,
                's.jenispemeriksaanlab_id' => $pemeriksaanPcr,
            ]);

        if (!empty($regisId)) {
            $qLab->andWhere(['infopasienlabdetail_v.pendaftaran_id' => $regisId]);
        }

        if (!empty($unitId)) {
            $qLab->andWhere(['d.pasienkirimkeunitlain_id' => $unitId]);
        }

        if (!empty($registNo)) {
            $qLab->andWhere(['c.no_pendaftaran' => $registNo]);
        }

        return $qLab->getDataArray();
    }
}