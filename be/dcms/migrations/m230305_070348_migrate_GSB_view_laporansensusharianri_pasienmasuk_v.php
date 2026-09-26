<?php

use yii\db\Migration;

/**
 * Class m230305_070348_migrate_GSB_view_laporansensusharianri_pasienmasuk_v
 */
class m230305_070348_migrate_GSB_view_laporansensusharianri_pasienmasuk_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporansensusharianri_pasienmasuk_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporansensusharianri_pasienmasuk_v" AS  SELECT to_char(pasienadmisi_t.tgl_admisi, \'YYYY-MM-DD\'::text)::date AS tgl_admisi,
    pasienadmisi_t.pasien_id,
    pasien_m.nama_pasien, 
    pasien_m.no_rekam_medik,
    masukkamar_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    masukkamar_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    kamar_asal.kamarruangan_nokamar AS kamar,
    tempattidur_asal.no_tempattidur AS tempattidur,
    penjamin_m.penjamin_nama,
    pegawai_m.nama_pegawai AS nama_dokter,
    cppt_t.a_diag_utama AS diagnosa_nama,
    pasienadmisi_t.status_ranap_id,
    pasienadmisi_t.status_ranap_nama,
    pasienadmisi_t.tgl_admisi::time without time zone AS jam_masuk,
    pasienpulang_t.tglpasienpulang::time without time zone AS jam_keluar
   FROM pendaftaran_t
     JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.pasien_id,
            a.kamartempattidur_id,
            a.penjamin_id,
            a.pegawai_id,
            a.status_ranap AS status_ranap_id,
            look_status_pasien.lookup_name AS status_ranap_nama,
            a.pasienbatalperiksa_id,
            a.ruangan_id,
            a.kelaspelayanan_id,
            a.status_ranap,
            a.pasienpulang_id
           FROM pasienadmisi_t a
             LEFT JOIN lookup_m look_status_pasien ON a.status_ranap = look_status_pasien.lookup_id) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.pasienadmisi_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kelaspelayanan_id,
            a.tgl_masukkamar,
            a.kamartempattidur_id
           FROM masukkamar_t a) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a
          WHERE a.ruangan_id <> (( SELECT lookuptransaksi_m.additional_value::integer AS additional_value
                   FROM lookuptransaksi_m
                  WHERE lookuptransaksi_m.kode_transaksi::text = \'set_kamar_sensus\'::text))) ruangan_m ON masukkamar_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kelaspelayanan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamar_asal ON masukkamar_t.kamarruangan_id = kamar_asal.kamarruangan_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) tempattidur_asal ON masukkamar_t.kamartempattidur_id = tempattidur_asal.kamartempattidur_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON masukkamar_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.diagnosa_id
           FROM asesmenmedis_t a) asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id
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
  WHERE pendaftaran_t.is_deleted IS FALSE AND masukkamar_t.tgl_masukkamar::time without time zone = \'00:00:00\'::time without time zone AND pasienadmisi_t.pasienbatalperiksa_id IS NULL AND pasienadmisi_t.status_ranap <> 453 AND to_char(pasienadmisi_t.tgl_admisi, \'YYYY-MM-DD\'::text)::date >= (( SELECT konfigsystem_k.set_tgl_sensus
           FROM konfigsystem_k))
  ORDER BY (to_char(pasienadmisi_t.tgl_admisi, \'YYYY-MM-DD\'::text)::date) DESC;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230305_070348_migrate_GSB_view_laporansensusharianri_pasienmasuk_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230305_070348_migrate_GSB_view_laporansensusharianri_pasienmasuk_v cannot be reverted.\n";

        return false;
    }
    */
}
