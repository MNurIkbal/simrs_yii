<?php

use yii\db\Migration;

/**
 * Class m200902_091112_migrate_20200902_laporanhasillab
 */
class m200902_091112_migrate_20200902_laporanhasillab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pemeriksaanlab_m" ADD COLUMN "is_exception" bool DEFAULT false;');
        
        $this->execute('DROP VIEW if exists "public"."laporanhasillab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanhasillab_v\" AS  SELECT hasil_lab.hasilpemeriksaanlab_wynacom_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir AS dateofbirth,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin_nama,
    pasien_m.alamat_pasien,
    ruangan_m.ruangan_id AS lokasi_id,
    ruangan_m.ruangan_nama AS lokasi_nama,
    instalasi_m.instalasi_nama,
    dokter_pengirim.nama_pegawai AS dokter_pengirim,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_transaksi,
    pegawai_m.pegawai_id AS dokter_penunjangid,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    permintaankepenunjang_t.is_cyto,
    permintaankepenunjang_t.tglpermintaankepenunjang AS tglpenunjang,
    permintaankepenunjang_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    hasil_lab.test_group,
    hasil_lab.test_name AS test_nama_lis,
    hasil_lab.result AS hasil,
    hasil_lab.reference_value AS nilai_rujukan,
    hasil_lab.test_units_name AS satuan,
    hasil_lab.authorization_date AS tgl_pemeriksaan,
    pasienmasukpenunjang_t.catatan,
    hasil_lab.authorization_user AS petugas_pemeriksaan,
    hasil_lab.test_method,
    pasienmasukpenunjang_t.is_hasil,
    hasil_last.authorization_user,
    hasil_last.authorization_date AS tgl_hasil,
    CURRENT_TIMESTAMP AS tgl_cetak,
    hasil_lab.is_print,
    pemeriksaanlab_m.is_exception
   FROM (((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id)))
     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
     JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m dokter_pengirim ON ((pendaftaran_t.pegawai_id = dokter_pengirim.pegawai_id)))
     JOIN hasilpemeriksaanlab_wynacom_t hasil_lab ON ((((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_lab.his_reg_no)::text) AND ((daftartindakan_m.daftartindakan_kode)::text = (hasil_lab.his_test_id)::text))))
     LEFT JOIN ( SELECT hasilpemeriksaanlab_wynacom_t.his_reg_no,
            hasilpemeriksaanlab_wynacom_t.authorization_date,
            hasilpemeriksaanlab_wynacom_t.authorization_user
           FROM hasilpemeriksaanlab_wynacom_t
          ORDER BY hasilpemeriksaanlab_wynacom_t.hasilpemeriksaanlab_wynacom_id DESC
         LIMIT 1) hasil_last ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil_last.his_reg_no)::text)))
     LEFT JOIN pemeriksaanlab_m ON ((daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
  ORDER BY hasil_lab.hasilpemeriksaanlab_wynacom_id;");

        $this->execute('ALTER TABLE "public"."laporanhasillab_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200902_091112_migrate_20200902_laporanhasillab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200902_091112_migrate_20200902_laporanhasillab cannot be reverted.\n";

        return false;
    }
    */
}
