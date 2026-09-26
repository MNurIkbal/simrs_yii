<?php

use yii\db\Migration;

/**
 * Class m201128_191014_migrate_20201129_invoiceridetail_v
 */
class m201128_191014_migrate_20201129_invoiceridetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."invoiceridetail_v";');

         $this->execute("
            CREATE VIEW \"public\".\"invoiceridetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    layanan.layanan_jenis,
    layanan.tgl_pelayanan,
    layanan.tindakan_obat,
    layanan.kelompok,
    layanan.qty,
    layanan.harga_satuan::integer AS harga_satuan,
    layanan.tarif::integer AS tarif,
    layanan.uom,
    layanan.ruangan,
    layanan.dokter,
    layanan.is_akomodasi,
    layanan.is_konsultasi,
    layanan.additional_data,
    layanan.kamarruangan_nokamar AS kamar,
    layanan.no_tempattidur AS no_bed,
    layanan.kelaspelayanan_nama AS kelas,
    layanan.pembayaran_id,
    layanan.tarif_dijamin,
    layanan.tarif_dibayarkan
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT 'tindakan'::text AS layanan_jenis,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat,
                CASE
                    WHEN daftartindakan_m.is_konsultasi = true THEN 'Consultation'::character varying
                    ELSE kelompoktindakan_m.kelompoktindakan_nama
                END AS kelompok,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarif_satuan AS harga_satuan,
            tindakanpelayanan_t.tarif_tindakan AS tarif,
            NULL::character varying AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            daftartindakan_m.is_akomodasi,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.additional_data,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            kelaspelayanan_m.kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan
           FROM tindakanpelayanan_t
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m dok_dpjp ON tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN tindakansudahbayar_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
             JOIN pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT 'obat'::text AS layanan_jenis,
            obatalkespasien_t.pendaftaran_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkes_m.obatalkes_nama AS tindakan_obat,
            'Drugs & Consumables'::character varying AS kelompok,
            obatalkespasien_t.qty_oa AS qty,
            obatalkespasien_t.hargasatuan_oa AS harga_satuan,
            obatalkespasien_t.hargajual_oa AS tarif,
            satuanunit_m.satuanunit_nama AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            false AS is_akomodasi,
            false AS is_konsultasi,
            obatalkespasien_t.additional_data,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            NULL::character varying AS kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan
           FROM obatalkespasien_t
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
             LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN pegawai_m dok_dpjp ON obatalkespasien_t.pegawai_id = dok_dpjp.pegawai_id
             JOIN obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
             JOIN pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
          WHERE obatalkespasien_t.is_deleted = false) layanan ON pendaftaran_t.pendaftaran_id = layanan.pendaftaran_id;");

         $this->execute('ALTER TABLE "public"."invoiceridetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201128_191014_migrate_20201129_invoiceridetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201128_191014_migrate_20201129_invoiceridetail_v cannot be reverted.\n";

        return false;
    }
    */
}
