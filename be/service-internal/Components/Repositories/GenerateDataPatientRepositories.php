<?php

namespace Integrasi\Components\Repositories;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use yii\db\Query;

class GenerateDataPatientRepositories {

    public function getData($penunjangId = null, $regisId = null, $unitId = null, $pembayaranIds = null)
    {
        if (!empty($penunjangId) || !empty($regisId) || !empty($unitId)) {
            $integrasi = (new \yii\db\Query())
                            ->select([
                                'a.pasienmasukpenunjang_id', 
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
                                'a.dokter_perujuk_id',
                                'a.dokter_perujuk_nama',
                                'j.nomorindukpegawai',
                                'j.notelp_pegawai',
                                'j.alamat_pegawai',
                                // 'b.id',
                                'a.nama_diagnosa',
                                'h.instalasi_id',
                                'a.groupcarabayar_id',
                                'l.modality_kode',
                                'a.pegawai_id',
                                'a.dokter_penunjang',
                                'x.nomorindukpegawai as penunjang_nomorindukpegawai',
                                'x.notelp_pegawai as penunjang_notelp_pegawai',
                                'x.alamat_pegawai as penunjang_alamat_pegawai',
                                'religion.lookup_value as agama',
                                'g.alergi',
                                'state.lookup_value as warga_negara',
                                'sex.additional_data as jeniskelamin',
                                'e.no_pembayaran',
                                'd.pembayaran_id',
                                'a.pasien_id'                            
                            ])
                            ->from('infopasienradiologi_v a')
                            ->innerjoin('tindakanpelayanan_t c', 'c.tindakanpelayanan_id = a.tindakanpelayanan_id')
                            ->leftjoin('tindakansudahbayar_t d', 'd.tindakansudahbayar_id = c.tindakansudahbayar_id')
                            ->leftjoin('pembayaranpelayanan_t e', 'e.pembayaranpelayanan_id = d.pembayaranpelayanan_id')
                            ->leftjoin('loginpemakai_k f', 'f.loginpemakai_id = a.created_by')
                            ->innerjoin('pasien_m g', 'g.pasien_id = a.pasien_id')
                            ->leftjoin('lookup_m religion', 'g.agama::int = religion.lookup_id')
                            ->leftjoin('lookup_m state', 'g.warga_negara::int = state.lookup_id')
                            ->leftjoin('lookup_m sex', 'g.jeniskelamin::int = sex.lookup_id')
                            ->innerjoin('ruangan_m h', 'h.ruangan_id = a.ruanganasal_id')
                            ->leftjoin('daftartindakan_m i', 'i.daftartindakan_id = a.daftartindakan_id')
                            ->leftjoin('pegawai_m j', 'a.dokter_perujuk_id = j.pegawai_id')
                            ->leftjoin('pegawai_m x', 'a.pegawai_id = x.pegawai_id')
                            ->leftjoin('pemeriksaanrad_m k', 'a.daftartindakan_id = k.daftartindakan_id')
                            ->leftjoin('modalitytype_m l', 'k.modalitytype_id = l.modalitytype_id')
                            ->andWhere([
                                'a.is_referred' => false,
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

            if (!empty($pembayaranIds)) {
                $integrasi->andWhere([
                    'd.pembayaran_id' => $pembayaranIds
                ]);
            }

            return $integrasi->all();
        }
        return [];
    }
}