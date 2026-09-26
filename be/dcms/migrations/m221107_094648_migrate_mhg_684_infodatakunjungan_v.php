<?php

use yii\db\Migration;

/**
 * Class m221107_094648_migrate_mhg_684_infodatakunjungan_v
 */
class m221107_094648_migrate_mhg_684_infodatakunjungan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infodatakunjungan_v";');
        $this->execute("
			CREATE OR REPLACE VIEW public.infodatakunjungan_v
        AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.pasienpulang_id,
    pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS dokter_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN dok_rj.nama_pegawai
            ELSE dok_ri.nama_pegawai
        END AS dokter_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
            ELSE COALESCE(pasienadmisi_t.kelas_ditagihkan_id, pasienadmisi_t.kelaspelayanan_id)
        END AS kelaspelayanan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pendaftaran_t.instalasi_id
            ELSE ruang_ri.instalasi_id
        END AS instalasi_id,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN instalasi_rj.instalasi_nama
            ELSE instalasi_ri.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN ruang_rj.ruangan_nama
            ELSE ruang_ri.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN carabayar_rj.carabayar_nama
            ELSE carabayar_ri.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN penjamin_rj.penjamin_nama
            ELSE penjamin_ri.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN int_freezebill_r.status = 1 THEN true
            ELSE false
        END AS is_freezebill,
        CASE
            WHEN pendaftaran_t.instalasi_id = 2 THEN true
            ELSE false
        END AS is_rd,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.bb
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.bb::double precision
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN periksa_fisik_ri.bb::double precision
            ELSE NULL::double precision
        END AS berat_badan,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.tb
            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.tb::double precision
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN periksa_fisik_ri.tb::double precision
            ELSE NULL::double precision
        END AS tinggi_badan,
    pendaftaran_t.is_stopakomodasi,
    pendaftaran_t.status_periksa,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.status_bayar,
        CASE
            WHEN pasienadmisi_t.is_pasientitipan = true THEN pasienadmisi_t.kelas_ditagihkan_id
            ELSE NULL::integer
        END AS kelas_titipan
   FROM pendaftaran_t
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.pasienpulang_id,
            a.pegawai_id,
            a.kelaspelayanan_id,
            a.carabayar_id,
            a.penjamin_id,
            a.ruangan_id,
            a.is_pasientitipan,
            a.kelas_ditagihkan_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) dok_rj ON pendaftaran_t.pegawai_id = dok_rj.pegawai_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
           FROM pegawai_m c) dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
     LEFT JOIN ( SELECT d.ruangan_id,
            d.instalasi_id,
            d.ruangan_nama
           FROM ruangan_m d) ruang_rj ON pendaftaran_t.ruangan_id = ruang_rj.ruangan_id
     LEFT JOIN ( SELECT d.ruangan_id,
            d.instalasi_id,
            d.ruangan_nama
           FROM ruangan_m d) ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
     JOIN ( SELECT e.pasien_id,
            e.nama_pasien,
            e.no_rekam_medik,
            e.tanggal_lahir
           FROM pasien_m e) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT f.instalasi_id,
            f.instalasi_nama
           FROM instalasi_m f) instalasi_rj ON pendaftaran_t.instalasi_id = instalasi_rj.instalasi_id
     LEFT JOIN ( SELECT g.instalasi_id,
            g.instalasi_nama
           FROM instalasi_m g) instalasi_ri ON ruang_ri.instalasi_id = instalasi_ri.instalasi_id
     LEFT JOIN ( SELECT h.carabayar_id,
            h.carabayar_nama
           FROM carabayar_m h) carabayar_rj ON pendaftaran_t.carabayar_id = carabayar_rj.carabayar_id
     LEFT JOIN ( SELECT i.carabayar_id,
            i.carabayar_nama
           FROM carabayar_m i) carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
     LEFT JOIN ( SELECT j.penjamin_id,
            j.penjamin_nama
           FROM penjamin_m j) penjamin_rj ON pendaftaran_t.penjamin_id = penjamin_rj.penjamin_id
     LEFT JOIN ( SELECT k.penjamin_id,
            k.penjamin_nama
           FROM penjamin_m k) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
     LEFT JOIN ( SELECT DISTINCT ON ((int_freezebill_r_1.pendaftaran_id::integer)) int_freezebill_r_1.pendaftaran_id::integer AS pendaftaran_id,
            int_freezebill_r_1.status
           FROM int_freezebill_r int_freezebill_r_1) int_freezebill_r ON pendaftaran_t.pendaftaran_id = int_freezebill_r.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (pemeriksaanfisik_t.pendaftaran_id) pemeriksaanfisik_t.pendaftaran_id,
            pemeriksaanfisik_t.tinggibadan_cm AS tb,
            pemeriksaanfisik_t.beratbadan_kg AS bb
           FROM pemeriksaanfisik_t
          WHERE pemeriksaanfisik_t.is_deleted = false) periksa_fisik_rj ON pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (asesmenperawatrd_t.pendaftaran_id) asesmenperawatrd_t.pendaftaran_id,
            asesmenperawatrd_t.tinggi_badan AS tb,
            asesmenperawatrd_t.berat_badan AS bb
           FROM asesmenperawatrd_t
          WHERE asesmenperawatrd_t.is_deleted = false) periksa_fisik_rd ON pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id
     LEFT JOIN ( SELECT DISTINCT ON (asesmenmedis_t.pendaftaran_id) asesmenmedis_t.pendaftaran_id,
            asesmenmedis_t.tinggi_badan AS tb,
            asesmenmedis_t.berat_badan AS bb
           FROM asesmenmedis_t
          WHERE asesmenmedis_t.is_deleted = false) periksa_fisik_ri ON pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id
  WHERE pendaftaran_t.is_deleted = false
  ORDER BY pendaftaran_t.created_date DESC ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221107_094648_migrate_mhg_684_infodatakunjungan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221107_094648_migrate_mhg_684_infodatakunjungan_v cannot be reverted.\n";

        return false;
    }
    */
}
