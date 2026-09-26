<?php

use yii\db\Migration;

/**
 * Class m200303_063525_info_pasien_penatajasa
 */
class m200303_063525_info_pasien_penatajasa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienpenatajasa_v;');
        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienpenatajasa_v AS
                SELECT pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas,
                    pasien_m.no_identitas_pasien,
                    fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
                    pasien_m.nama_pasien,
                    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                    pasien_m.tanggal_lahir,
                    fgetnamalookup((pasien_m.statusperkawinan)::integer) AS statusperkawinan,
                    fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
                    fgetnamalookup((pasien_m.agama)::integer) AS agama,
                    pasien_m.no_telepon_pasien,
                    pasien_m.no_mobile_pasien,
                    fgetnamalookup((pasien_m.warga_negara)::integer) AS warga_negara,
                    pasien_m.alamat_pasien,
                    pendaftaran_t.umur,
                    pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    pendaftaran_t.tgl_pendaftaran,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.instalasi_id,
                    instalasi_m.instalasi_nama,
                    pendaftaran_t.ruangan_id,
                    ruangan_m.ruangan_nama,
                    (((instalasi_m.instalasi_nama)::text || ' - '::text) || (ruangan_m.ruangan_nama)::text) AS instalasi_ruangan,
                    pendaftaran_t.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    pendaftaran_t.carabayar_id,
                    carabayar_m.carabayar_nama,
                    pendaftaran_t.penjamin_id,
                    penjamin_m.penjamin_nama,
                    (((carabayar_m.carabayar_nama)::text || ' - '::text) || (penjamin_m.penjamin_nama)::text) AS carabayar_penjamin,
                    pendaftaran_t.jeniskasuspenyakit_id,
                    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                    (pendaftaran_t.status_periksa)::integer AS status_periksa_id,
                    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
                    pendaftaran_t.status_bayar AS status_bayar_id,
                    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
                    bpjs_t.klsrawat AS hak_kelas,
                    pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai AS dokter_dpjp,
                    asuransipasien.nokartuasuransi,
                    pendaftaran_t.catatan_penatajasa,
                    pendaftaran_t.is_stopakomodasi,
                    pasienpulang_t.tglpasienpulang,
                    carabayar_m.groupcarabayar_id
                   FROM (((((((((((pendaftaran_t
                     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
                     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
                     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                     LEFT JOIN ( SELECT asuransipasien_m.asuransipasien_id,
                            asuransipasien_m.pasien_id,
                            asuransipasien_m.penjamin_id,
                            asuransipasien_m.carabayar_id,
                            asuransipasien_m.nokartuasuransi
                           FROM (asuransipasien_m
                             LEFT JOIN ( SELECT min(asuransipasien_m_1.asuransipasien_id) AS asuransipasien_id,
                                    asuransipasien_m_1.carabayar_id,
                                    asuransipasien_m_1.penjamin_id
                                   FROM asuransipasien_m asuransipasien_m_1
                                  GROUP BY asuransipasien_m_1.carabayar_id, asuransipasien_m_1.penjamin_id) asuransipasien_min ON ((asuransipasien_m.asuransipasien_id = asuransipasien_min.asuransipasien_id)))) asuransipasien ON ((pendaftaran_t.asuransipasien_id = asuransipasien.asuransipasien_id)))
                  WHERE (pendaftaran_t.instalasi_id <> 3)
                UNION ALL
                 SELECT pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas,
                    pasien_m.no_identitas_pasien,
                    fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
                    pasien_m.nama_pasien,
                    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                    pasien_m.tanggal_lahir,
                    fgetnamalookup((pasien_m.statusperkawinan)::integer) AS statusperkawinan,
                    fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
                    fgetnamalookup((pasien_m.agama)::integer) AS agama,
                    pasien_m.no_telepon_pasien,
                    pasien_m.no_mobile_pasien,
                    fgetnamalookup((pasien_m.warga_negara)::integer) AS warga_negara,
                    pasien_m.alamat_pasien,
                    pendaftaran_t.umur,
                    pendaftaran_t.pendaftaran_id,
                    pasienadmisi_t.pasienadmisi_id,
                    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
                    pendaftaran_t.no_pendaftaran,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    pasienadmisi_t.ruangan_id,
                    ruangan_m.ruangan_nama,
                    (((instalasi_m.instalasi_nama)::text || ' - '::text) || (ruangan_m.ruangan_nama)::text) AS instalasi_ruangan,
                    pasienadmisi_t.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    pasienadmisi_t.carabayar_id,
                    carabayar_m.carabayar_nama,
                    pasienadmisi_t.penjamin_id,
                    penjamin_m.penjamin_nama,
                    (((carabayar_m.carabayar_nama)::text || ' - '::text) || (penjamin_m.penjamin_nama)::text) AS carabayar_penjamin,
                    pendaftaran_t.jeniskasuspenyakit_id,
                    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                    pasienadmisi_t.status_ranap AS status_periksa_id,
                    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
                    pendaftaran_t.status_bayar AS status_bayar_id,
                    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
                    bpjs_t.klsrawat AS hak_kelas,
                    pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai AS dokter_dpjp,
                    asuransipasien.nokartuasuransi,
                    pendaftaran_t.catatan_penatajasa,
                    pendaftaran_t.is_stopakomodasi,
                    pasienpulang_t.tglpasienpulang,
                    carabayar_m.groupcarabayar_id
                   FROM ((((((((((((pendaftaran_t
                     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                     JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
                     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
                     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
                     LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
                     LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
                     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
                     LEFT JOIN ( SELECT asuransipasien_m.asuransipasien_id,
                            asuransipasien_m.pasien_id,
                            asuransipasien_m.penjamin_id,
                            asuransipasien_m.carabayar_id,
                            asuransipasien_m.nokartuasuransi
                           FROM (asuransipasien_m
                             LEFT JOIN ( SELECT min(asuransipasien_m_1.asuransipasien_id) AS asuransipasien_id,
                                    asuransipasien_m_1.carabayar_id,
                                    asuransipasien_m_1.penjamin_id
                                   FROM asuransipasien_m asuransipasien_m_1
                                  GROUP BY asuransipasien_m_1.carabayar_id, asuransipasien_m_1.penjamin_id) asuransipasien_min ON ((asuransipasien_m.asuransipasien_id = asuransipasien_min.asuransipasien_id)))) asuransipasien ON ((pendaftaran_t.asuransipasien_id = asuransipasien.asuransipasien_id)));");
         $this->execute('ALTER TABLE public.infopasienpenatajasa_v OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200303_063525_info_pasien_penatajasa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200303_063525_info_pasien_penatajasa cannot be reverted.\n";

        return false;
    }
    */
}
