<?php

use yii\db\Migration;

/**
 * Class m200729_083903_migrate_mhkn_20200729_2
 */
class m200729_083903_migrate_mhkn_20200729_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopasienpenunjang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienpenunjang_v\" AS  SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_penunjang,
    ruangasal.ruangan_nama AS ruangan_asal,
    pasienmasukpenunjang_t.no_masukpenunjang,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    kelaspelayanan_m.kelaspelayanan_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienmasukpenunjang_t.status_periksa,
    ruangan_m.instalasi_id,
    pendaftaran_t.created_by,
    ruangan_m.ruangan_nama,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.umur,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pegawai_m.nama_pegawai,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.pasienadmisi_id
   FROM ((((((((((pasienmasukpenunjang_t
     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN ruangan_m ruangasal ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangasal.ruangan_id)))
     JOIN kelaspelayanan_m ON ((pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN jeniskasuspenyakit_m ON ((pasienmasukpenunjang_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
  WHERE ((pasienmasukpenunjang_t.is_active = true) AND (pasienmasukpenunjang_t.is_deleted = false));");

        $this->execute('DROP VIEW if exists "public"."laporanhasillab_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanhasillab_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
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
    hasil_lab.is_print
   FROM ((((((((((((pendaftaran_t
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
  ORDER BY hasil_lab.hasilpemeriksaanlab_wynacom_id;");

        $this->execute('DROP VIEW if exists "public"."invoiceobat_v";');

        $this->execute("
            CREATE VIEW \"public\".\"invoiceobat_v\" AS  SELECT pembayaranpelayanan_t.pembayaran_id,
    pembayaranpelayanan_t.no_pembayaran,
    pembayaranpelayanan_t.tgl_pembayaran,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nama_pegawai
            ELSE penjualanresep_t.nama_pembeli
        END AS nama_pasien,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.no_rekam_medik
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nomorindukpegawai
            ELSE NULL::character varying
        END AS no_rekam_medik,
    pendaftaran_t.umur,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.tanggal_lahir
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.tgl_lahirpegawai
            ELSE NULL::date
        END AS tgl_lahir,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN fgetnamalookup((pasien_m.jeniskelamin)::integer)
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN fgetnamalookup((resep_karyawan.jeniskelamin)::integer)
            ELSE NULL::character varying
        END AS jenis_kelamin,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_1.ruangan_nama
            ELSE ruangan_2.ruangan_nama
        END AS ruangan_nama,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    dok_admisi.nama_pegawai AS dok_admisi,
    dok_resep.nama_pegawai AS dok_resep,
    pembayaranpelayanan_t.total_biayaoa AS total_tagihan_obat,
        CASE
            WHEN (pembayaran_diskon.komponen IS NULL) THEN 'discount RS'::character varying
            ELSE pembayaran_diskon.komponen
        END AS komponen,
    pembayaran_t.total_discountpembayaran AS total_diskon
   FROM ((((((((((((((pembayaranpelayanan_t
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN obatsudahbayar_t ON ((pembayaranpelayanan_t.pembayaranpelayanan_id = obatsudahbayar_t.pembayaranpelayanan_id)))
     JOIN ( SELECT obatalkespasien_t_1.obatsudahbayar_id,
            obatalkespasien_t_1.penjualanresep_id,
            obatalkespasien_t_1.obatalkespasien_id
           FROM obatalkespasien_t obatalkespasien_t_1
          WHERE (obatalkespasien_t_1.is_deleted = false)
          GROUP BY obatalkespasien_t_1.obatsudahbayar_id, obatalkespasien_t_1.penjualanresep_id, obatalkespasien_t_1.obatalkespasien_id) obatalkespasien_t ON (((obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id) AND (obatsudahbayar_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id))))
     JOIN penjualanresep_t ON ((obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m resep_karyawan ON ((penjualanresep_t.karyawan_id = resep_karyawan.pegawai_id)))
     LEFT JOIN pegawai_m dok_pendaftaran ON ((pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id)))
     LEFT JOIN pegawai_m dok_admisi ON ((pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id)))
     LEFT JOIN pegawai_m dok_resep ON ((penjualanresep_t.pegawai_id = dok_resep.pegawai_id)))
     LEFT JOIN ruangan_m ruangan_1 ON ((pendaftaran_t.ruangan_id = ruangan_1.ruangan_id)))
     LEFT JOIN ruangan_m ruangan_2 ON ((pasienadmisi_t.ruangan_id = ruangan_2.ruangan_id)))
     LEFT JOIN ( SELECT pembayarandiskon_t.pembayaran_id,
            komponentarif_m.komponentarif_nama AS komponen,
            sum(pembayarandiskon_t.total_diskon) AS total_diskon
           FROM (pembayarandiskon_t
             LEFT JOIN komponentarif_m ON ((pembayarandiskon_t.komponentarif_id = komponentarif_m.komponentarif_id)))
          GROUP BY pembayarandiskon_t.pembayaran_id, komponentarif_m.komponentarif_nama) pembayaran_diskon ON ((pembayaran_t.pembayaran_id = pembayaran_diskon.pembayaran_id)))
  GROUP BY pembayaranpelayanan_t.pembayaran_id, pembayaranpelayanan_t.no_pembayaran, pembayaranpelayanan_t.tgl_pembayaran, penjualanresep_t.noresep, pendaftaran_t.no_pendaftaran,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.nama_pasien
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nama_pegawai
            ELSE penjualanresep_t.nama_pembeli
        END,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.no_rekam_medik
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.nomorindukpegawai
            ELSE NULL::character varying
        END, pendaftaran_t.umur,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN pasien_m.tanggal_lahir
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN resep_karyawan.tgl_lahirpegawai
            ELSE NULL::date
        END,
        CASE
            WHEN (penjualanresep_t.pendaftaran_id IS NOT NULL) THEN fgetnamalookup((pasien_m.jeniskelamin)::integer)
            WHEN (penjualanresep_t.karyawan_id IS NOT NULL) THEN fgetnamalookup((resep_karyawan.jeniskelamin)::integer)
            ELSE NULL::character varying
        END,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_1.ruangan_nama
            ELSE ruangan_2.ruangan_nama
        END, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, dok_resep.nama_pegawai, pembayaranpelayanan_t.total_biayaoa, pembayaran_diskon.komponen, pembayaran_t.total_discountpembayaran;");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200729_083903_migrate_mhkn_20200729_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_083903_migrate_mhkn_20200729_2 cannot be reverted.\n";

        return false;
    }
    */
}
