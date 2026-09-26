<?php

use yii\db\Migration;

/**
 * Class m221115_090212_migrate_MHG3755_laporansensusharianri_pasienpindahkan_v
 */
class m221115_090212_migrate_MHG3755_laporansensusharianri_pasienpindahkan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporansensusharianri_pasienpindahkan_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporansensusharianri_pasienpindahkan_v
        AS SELECT pasienadmisi_t.tgl_admisi,
            pindahkamar_t.tgl_pindahkamar,
            pasienadmisi_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            masukkamar_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            ruangan_skrg.ruangan_nama AS ruangan_skrg,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            cppt_t.a_diag_utama AS diagnosa_nama,
            to_char(masukkamar.tgl_masukkamar, 'YYYY-MM-DD'::text)::date AS tgl_masukkamar,
            masukkamar_t.lamadirawat_kamar AS lama_rawat,
            masukkamar_t.ruangan_id,
            ruangan_pindah.ruangan_nama AS ruangan_ke,
            instalasi_pindah.instalasi_id,
            instalasi_pindah.instalasi_nama AS instalasi_ke,
            kamar_pindah.kamarruangan_nokamar AS kamar_ke,
            kamar_skrg.kamarruangan_nokamar AS kamar_skrg,
            tempattidur_pindah.no_tempattidur AS tempattidur_ke,
            tempattidur_skrg.no_tempattidur AS tempattidur_skrg,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            masukkamar_t.tgl_masukkamar AS tgl_masukkamar_1,
            masukkamar_t.jam_masukkamar,
            masukkamar_t.tgl_keluarkamar,
            masukkamar_t.jam_keluarkamar,
            pasienadmisi_t.status_ranap AS status_ranap_id,
            look_status_pasien.lookup_name AS status_ranap_nama,
            pasienadmisi_t.tgl_admisi::time without time zone AS jam_masuk,
            pasienpulang_t.tglpasienpulang::time without time zone AS jam_keluar
           FROM pasienadmisi_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.pegawai_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.pendaftaran_id,
                    a.pasien_id
                   FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pasienadmisi_id,
                    a.pindahkamar_id,
                    a.kelaspelayanan_id,
                    a.ruangan_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.lamadirawat_kamar,
                    a.tgl_masukkamar,
                    a.jam_masukkamar,
                    a.tgl_keluarkamar,
                    a.jam_keluarkamar
                   FROM masukkamar_t a) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
             JOIN ( SELECT DISTINCT ON (a.pasienadmisi_id) a.pasienadmisi_id,
                    a.masukkamar_id,
                    a.tgl_masukkamar
                   FROM masukkamar_t a) masukkamar ON pasienadmisi_t.pasienadmisi_id = masukkamar.pasienadmisi_id
             JOIN ( SELECT a.pindahkamar_id,
                    a.ruangan_id,
                    a.kamarruangan_id,
                    a.is_active,
                    a.is_deleted,
                    a.tgl_pindahkamar
                   FROM pindahkamar_t a) pindahkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON masukkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_skrg ON masukkamar_t.ruangan_id = ruangan_skrg.ruangan_id
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_pindah ON pindahkamar_t.ruangan_id = ruangan_pindah.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_pindah ON ruangan_pindah.instalasi_id = instalasi_pindah.instalasi_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamar_pindah ON pindahkamar_t.kamarruangan_id = kamar_pindah.kamarruangan_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamar_skrg ON masukkamar_t.kamarruangan_id = kamar_skrg.kamarruangan_id
             JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) tempattidur_pindah ON masukkamar_t.kamartempattidur_id = tempattidur_pindah.kamartempattidur_id
             JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) tempattidur_skrg ON masukkamar_t.kamartempattidur_id = tempattidur_skrg.kamartempattidur_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.diagnosa_id
                   FROM asesmenmedis_t a) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id
             LEFT JOIN lookup_m look_status_pasien ON pasienadmisi_t.status_ranap = look_status_pasien.lookup_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.a_diag_utama,
                    a.tgl_cppt,
                    a.is_deleted
                   FROM cppt_t a
                  WHERE a.tgl_cppt = (( SELECT max(b.tgl_cppt) AS max
                           FROM cppt_t b
                          WHERE a.pasienadmisi_id = b.pasienadmisi_id AND b.is_deleted = false AND b.is_verifikasi = true)) AND a.is_deleted = false) cppt_t ON pasienadmisi_t.pasienadmisi_id = cppt_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
          WHERE pindahkamar_t.is_active = true AND pindahkamar_t.is_deleted = false
          ORDER BY (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text)::date) DESC;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221115_090212_migrate_MHG3755_laporansensusharianri_pasienpindahkan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221115_090212_migrate_MHG3755_laporansensusharianri_pasienpindahkan_v cannot be reverted.\n";

        return false;
    }
    */
}
