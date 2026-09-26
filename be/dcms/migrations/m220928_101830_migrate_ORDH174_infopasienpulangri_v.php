<?php

use yii\db\Migration;

/**
 * Class m220928_101830_migrate_ORDH174_infopasienpulangri_v
 */
class m220928_101830_migrate_ORDH174_infopasienpulangri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienpulangri_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopasienpulangri_v
        AS SELECT pendaftaran_t.pendaftaran_id,
            pasienadmisi_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pasienadmisi_t.pasienadmisi_id,
            pasienadmisi_t.tgl_admisi,
                CASE
                    WHEN pasienadmisi_t.pasienpulang_id IS NULL THEN pendaftaran_t.tgl_stopakomodasi
                    ELSE pasienpulang_t.tglpasienpulang
                END AS tglpasienpulang,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran,
            pasien_m.nama_pasien,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasienadmisi_t.ruangan_id AS ruanganakhir_id,
            ruangan_m.ruangan_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasienadmisi_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            kondisikeluar_m.kondisikeluar_nama,
            pasienpulang_t.lama_rawat,
            pasienpulang_t.pasienpulang_id,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pendaftaran_t.status_bayar,
            look_statusbayar.lookup_name AS stat_bayar,
            pasien_m.jeniskelamin,
            look_jeniskelamin.lookup_name AS jns_kelamin,
            pasienadmisi_t.kamarruangan_id,
            kamarruangan_m.kamarruangan_nokamar,
            pasienadmisi_t.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur,
            pendaftaran_t.is_stopakomodasi,
            pasienadmisi_t.kamar_titipan_id,
            kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
            pasienadmisi_t.ruangan_titipan_id,
            ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
            pasienadmisi_t.is_stoptitipan,
            pasienadmisi_t.is_pasientitipan,
            pasienadmisi_t.kelas_ditagihkan_id,
            kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
            pk.pindahkamar_id,
            pk.is_stoptitipan AS pk_is_stoptitipan,
            pk.is_pasientitipan AS pk_is_pasientitipan,
            pk.kelas_ditagihkan_id AS pk_kelas_ditagihkan_id,
            pk.kelaspelayanan_nama AS pk_kelas_ditagihkan_nama,
            ( SELECT array_to_json(array_agg(row_to_json(x.*))) AS array_to_json
                   FROM ( SELECT konfirmasiunit_t.instalasi_id,
                            konfirmasiunit_t.is_konfirmasi
                           FROM konfirmasiunit_t
                          WHERE konfirmasiunit_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND konfirmasiunit_t.is_deleted = false) x) AS status_konfirmasi,
            petugas_pemulang.pegawai_id AS petugas_pemulang_id,
            petugas_pemulang.nama_pegawai AS petugas_pemulang_nama
           FROM pendaftaran_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.tgl_admisi,
                    a.pasienpulang_id,
                    a.ruangan_id,
                    a.pegawai_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.ruangan_titipan_id,
                    a.is_stoptitipan,
                    a.is_pasientitipan,
                    a.kelas_ditagihkan_id,
                    a.kelaspelayanan_id,
                    a.kamar_titipan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.pasienadmisi_id,
                    a.tglpasienpulang,
                    a.carakeluar_id,
                    a.lama_rawat,
                    a.kondisikeluar_id,
                    a.created_by
                   FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienadmisi_t.pasienadmisi_id = pasienpulang_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
             LEFT JOIN ( SELECT pindahkamar_t.pasienadmisi_id,
                    pindahkamar_t.pindahkamar_id,
                    pindahkamar_t.is_stoptitipan,
                    pindahkamar_t.is_pasientitipan,
                    pindahkamar_t.kelas_ditagihkan_id,
                    kelas_ditagihkan_1.kelaspelayanan_nama
                   FROM pindahkamar_t
                     JOIN ( SELECT pindahkamar_t_1.pasienadmisi_id,
                            max(pindahkamar_t_1.pindahkamar_id) AS pindahkamar_id
                           FROM pindahkamar_t pindahkamar_t_1
                          GROUP BY pindahkamar_t_1.pasienadmisi_id) nilai_max ON pindahkamar_t.pindahkamar_id = nilai_max.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = nilai_max.pasienadmisi_id
                     LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id) pk ON pasienadmisi_t.pasienadmisi_id = pk.pasienadmisi_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusbayar ON pendaftaran_t.status_bayar = look_statusbayar.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pasienpulang_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugas_pemulang ON loginpemakai_k.pegawai_id = petugas_pemulang.pegawai_id
          WHERE pendaftaran_t.is_stopakomodasi = true;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220928_101830_migrate_ORDH174_infopasienpulangri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220928_101830_migrate_ORDH174_infopasienpulangri_v cannot be reverted.\n";

        return false;
    }
    */
}
