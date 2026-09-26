<?php

use yii\db\Migration;

/**
 * Class m200702_101127_migrate_mhkn_20200702_1
 */
class m200702_101127_migrate_mhkn_20200702_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanhasillab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanhasillab_v\" AS  SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
    pasien_m.tanggal_lahir AS dateofbirth,
    (hasil_lab.age_year)::character varying(10) AS umur,
    pegawai_m.pegawai_id AS dokter_id,
    pegawai_m.nama_pegawai AS dokter_nama,
    ruangan_m.ruangan_id AS poli_id,
    ruangan_m.ruangan_nama AS poli_nama,
    pasienmasukpenunjang_t.no_masukpenunjang,
    permintaankepenunjang_t.is_cyto,
    permintaankepenunjang_t.tglpermintaankepenunjang AS tglpenunjang,
    permintaankepenunjang_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    hasil_lab.test_name AS test_nama_lis,
    hasil_lab.result AS hasil,
    hasil_lab.reference_value AS nilai_rujukan,
    hasil_lab.test_units_name AS satuan,
    hasil_lab.authorization_date AS tgl_pemeriksaan,
    pasienmasukpenunjang_t.catatan,
    hasil_lab.test_group,
    hasil_lab.authorization_user AS petugas_pemeriksaan,
    hasil_lab.test_method,
    pasienmasukpenunjang_t.is_hasil,
    pendaftaran_t.pendaftaran_id
   FROM ((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id)))
     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
     JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN hasilpemeriksaanlab_wynacom_t hasil_lab ON ((((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_lab.his_reg_no)::text) AND ((daftartindakan_m.daftartindakan_kode)::text = (hasil_lab.his_test_id)::text))))
  ORDER BY hasil_lab.hasilpemeriksaanlab_wynacom_id;");

        $this->execute('ALTER TABLE "public"."laporanhasillab_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infokonsulpoli_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infokonsulpoli_v\" AS  SELECT konsulpoli_t.konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    konsulpoli_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    konsulpoli_t.status_periksa,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status,
    konsulpoli_t.catatan_dokter_konsul,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    konsulpoli_t.tgl_konsulpoli,
    konsulpoli_t.tgl_selesaikonsul,
    konsulpoli_t.jawaban_konsul,
    pendaftaran_t.pegawai_id AS dok_mengkonsul_id,
    dok_mengkonsul.nama_pegawai AS dok_mengkonsul,
    konsulpoli_t.status_konsul AS status_konsul_id,
    fgetnamalookup((konsulpoli_t.status_konsul)::integer) AS status_konsul,
    konsulpoli_t.status_approve AS status_approve_id,
    fgetnamalookup((konsulpoli_t.status_approve)::integer) AS status_approve
   FROM ((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaranbaru_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((konsulpoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m dok_mengkonsul ON ((pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
  WHERE ((konsulpoli_t.is_active = true) AND (konsulpoli_t.is_deleted = false) AND (konsulpoli_t.status_approve = 565))
UNION ALL
 SELECT konsulpoli_t.konsulpoli_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    konsulpoli_t.pasien_id,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    konsulpoli_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    konsulpoli_t.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    konsulpoli_t.status_periksa,
    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status,
    konsulpoli_t.catatan_dokter_konsul,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama AS ruangan_asal,
    konsulpoli_t.tgl_konsulpoli,
    konsulpoli_t.tgl_selesaikonsul,
    konsulpoli_t.jawaban_konsul,
    pendaftaran_t.pegawai_id AS dok_mengkonsul_id,
    dok_mengkonsul.nama_pegawai AS dok_mengkonsul,
    konsulpoli_t.status_konsul AS status_konsul_id,
    fgetnamalookup((konsulpoli_t.status_konsul)::integer) AS status_konsul,
    konsulpoli_t.status_approve AS status_approve_id,
    fgetnamalookup((konsulpoli_t.status_approve)::integer) AS status_approve
   FROM ((((((konsulpoli_t
     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((konsulpoli_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m dok_mengkonsul ON ((pendaftaran_t.pegawai_id = dok_mengkonsul.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id)))
  WHERE ((konsulpoli_t.is_active = true) AND (konsulpoli_t.is_deleted = false));
");

        $this->execute('ALTER TABLE "public"."infokonsulpoli_v" OWNER TO "postgres";');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200702_101127_migrate_mhkn_20200702_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200702_101127_migrate_mhkn_20200702_1 cannot be reverted.\n";

        return false;
    }
    */
}
