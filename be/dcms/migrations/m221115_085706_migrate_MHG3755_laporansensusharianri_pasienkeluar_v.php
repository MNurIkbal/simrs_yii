<?php

use yii\db\Migration;

/**
 * Class m221115_085706_migrate_MHG3755_laporansensusharianri_pasienkeluar_v
 */
class m221115_085706_migrate_MHG3755_laporansensusharianri_pasienkeluar_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporansensusharianri_pasienkeluar_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporansensusharianri_pasienkeluar_v
        AS SELECT pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasienpulang_t.ruanganakhir_id AS ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            kamar_keluar.kamarruangan_nokamar AS kamar,
            tempattidur_keluar.no_tempattidur AS tempattidur,
            cppt_t.a_diag_utama AS diagnosa_nama,
            penjamin_m.penjamin_nama,
            to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text)::date AS tgl_masukkamar,
            masukkamar_t.lamadirawat_kamar AS lama_rawat,
            pegawai_m.nama_pegawai AS nama_dokter,
            pasienadmisi_t.tgl_admisi AS tgl_masukkamar_1,
            pasienpulang_t.tglpasienpulang AS tgl_pasienplg,
            masukkamar_t.jam_masukkamar,
            masukkamar_t.tgl_keluarkamar,
            masukkamar_t.jam_keluarkamar,
            pasienadmisi_t.status_ranap_id,
            pasienadmisi_t.status_ranap_nama,
            masukkamar_t.masukkamar_id,
            pasienadmisi_t.tgl_admisi::time without time zone AS jam_masuk,
            pasienpulang_t.tglpasienpulang::time without time zone AS jam_keluar
           FROM pendaftaran_t
             JOIN ( SELECT a.pasienadmisi_id,
                    a.tgl_admisi,
                    a.kelaspelayanan_id,
                    a.pasienpulang_id,
                    a.pegawai_id,
                    a.penjamin_id,
                    a.status_ranap AS status_ranap_id,
                    look_status_pasien.lookup_name AS status_ranap_nama
                   FROM pasienadmisi_t a
                     LEFT JOIN lookup_m look_status_pasien ON a.status_ranap = look_status_pasien.lookup_id) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.pasienpulang_id,
                    a.ruanganakhir_id,
                    a.carakeluar_id,
                    a.kondisikeluar_id,
                    a.pasienadmisi_id,
                    a.tglpasienpulang
                   FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.carakeluar_id
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT a.kondisikeluar_id
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ( SELECT a.pasienadmisi_id,
                    a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.tgl_masukkamar,
                    a.lamadirawat_kamar,
                    a.jam_masukkamar,
                    a.tgl_keluarkamar,
                    a.jam_keluarkamar,
                    a.masukkamar_id
                   FROM masukkamar_t a
                     JOIN ( SELECT max(masukkamar_t_1.masukkamar_id) AS maxmasukkamar_id,
                            masukkamar_t_1.pasienadmisi_id
                           FROM masukkamar_t masukkamar_t_1
                          GROUP BY masukkamar_t_1.pasienadmisi_id) maxmasukkamar ON a.masukkamar_id = maxmasukkamar.maxmasukkamar_id AND a.pasienadmisi_id = maxmasukkamar.pasienadmisi_id) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
             JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamar_keluar ON masukkamar_t.kamarruangan_id = kamar_keluar.kamarruangan_id
             JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) tempattidur_keluar ON masukkamar_t.kamartempattidur_id = tempattidur_keluar.kamartempattidur_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.a_diag_utama,
                    a.tgl_cppt,
                    a.is_deleted
                   FROM cppt_t a
                  WHERE a.tgl_cppt = (( SELECT max(b.tgl_cppt) AS max
                           FROM cppt_t b
                          WHERE a.pasienadmisi_id = b.pasienadmisi_id AND b.is_deleted = false AND b.is_verifikasi = true)) AND a.is_deleted = false) cppt_t ON pasienadmisi_t.pasienadmisi_id = cppt_t.pasienadmisi_id
          WHERE pendaftaran_t.is_deleted IS FALSE AND pasienpulang_t.pasienadmisi_id IS NOT NULL AND (pasienpulang_t.carakeluar_id = ANY (ARRAY[1, 3, 5, 6, 7])) AND to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text)::date >= (( SELECT konfigsystem_k.set_tgl_sensus
                   FROM konfigsystem_k));");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221115_085706_migrate_MHG3755_laporansensusharianri_pasienkeluar_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221115_085706_migrate_MHG3755_laporansensusharianri_pasienkeluar_v cannot be reverted.\n";

        return false;
    }
    */
}
