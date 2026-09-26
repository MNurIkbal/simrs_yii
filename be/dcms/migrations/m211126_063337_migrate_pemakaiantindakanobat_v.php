<?php

use yii\db\Migration;

/**
 * Class m211126_063337_migrate_pemakaiantindakanobat_v
 */
class m211126_063337_migrate_pemakaiantindakanobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('DROP VIEW if exists "public"."pemakaiantindakanobat_v";');

       $this->execute("
        CREATE VIEW \"public\".\"pemakaiantindakanobat_v\" AS  SELECT 'RAD'::text AS tipe,
    'tindakan'::text AS jenis,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_detail.tindakanpelayananasal_id,
    tindakanpelayanan_detail.tindakanpelayanan_id AS tindakanpeldetail_id,
    tindakanpelayanan_detail.created_date AS tgl_tindakan,
    daftartindakan_m.daftartindakan_nama AS nama_pemeriksaan,
    tindakan_detail.daftartindakan_nama AS nama_tindakan,
    NULL::character varying AS obat,
    dok_dpjp.nama_pegawai AS dok_dpjp,
    pegawai_1.nama_pegawai AS petugas_1,
    pegawai_2.nama_pegawai AS petugas_2,
    tindakanpelayanan_detail.qty_tindakan AS qty,
        CASE
            WHEN tindakanpelayanan_t.tarif_tindakan = 0::double precision THEN false
            ELSE true
        END AS is_ditagihkan,
        CASE
            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN false
            ELSE false
        END AS is_sudahbayar,
    NULL::integer AS obatalkespasien_id,
    NULL::character varying AS depo,
    tindakanpelayanan_t.tarif_tindakan AS harga_tindakan
   FROM tindakanpelayanan_t
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT b.daftartindakan_id
           FROM pemeriksaanrad_m b
          WHERE b.is_deleted = false) rad ON tindakanpelayanan_t.daftartindakan_id = rad.daftartindakan_id
     JOIN ( SELECT c.tindakanpelayanan_id,
            c.tindakanpelayananasal_id,
            c.daftartindakan_id,
            c.qty_tindakan,
            c.created_date,
            c.perawat1_id,
            c.perawat2_id
           FROM tindakanpelayanan_t c
          WHERE c.is_deleted = false) tindakanpelayanan_detail ON tindakanpelayanan_t.tindakanpelayanan_id = tindakanpelayanan_detail.tindakanpelayananasal_id
     JOIN ( SELECT d.daftartindakan_id,
            d.daftartindakan_nama
           FROM daftartindakan_m d) tindakan_detail ON tindakanpelayanan_detail.daftartindakan_id = tindakan_detail.daftartindakan_id
     LEFT JOIN ( SELECT e.pegawai_id,
            e.nama_pegawai
           FROM pegawai_m e) dok_dpjp ON dok_dpjp.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN ( SELECT f.pegawai_id,
            f.nama_pegawai
           FROM pegawai_m f) pegawai_1 ON pegawai_1.pegawai_id = tindakanpelayanan_detail.perawat1_id
     LEFT JOIN ( SELECT g.pegawai_id,
            g.nama_pegawai
           FROM pegawai_m g) pegawai_2 ON pegawai_2.pegawai_id = tindakanpelayanan_detail.perawat2_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'RAD'::text AS tipe,
    'obat'::text AS jenis,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    NULL::integer AS tindakanpelayananasal_id,
    NULL::integer AS tindakanpeldetail_id,
    obatalkespasien_t.created_date AS tgl_tindakan,
    daftartindakan_m.daftartindakan_nama AS nama_pemeriksaan,
    NULL::character varying AS nama_tindakan,
    obatalkes_m.obatalkes_nama AS obat,
    dok_dpjp.nama_pegawai AS dok_dpjp,
    pegawai_1.nama_pegawai AS petugas_1,
    pegawai_2.nama_pegawai AS petugas_2,
    obatalkespasien_t.qty_oa AS qty,
        CASE
            WHEN obatalkespasien_t.hargajual_oa = 0::double precision THEN false
            ELSE true
        END AS is_ditagihkan,
        CASE
            WHEN obatalkespasien_t.obatsudahbayar_id IS NULL THEN false
            ELSE false
        END AS is_sudahbayar,
    obatalkespasien_t.obatalkespasien_id,
    ruangan_m.ruangan_nama AS depo,
    tindakanpelayanan_t.tarif_tindakan AS harga_tindakan
   FROM tindakanpelayanan_t
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT b.daftartindakan_id
           FROM pemeriksaanrad_m b
          WHERE b.is_deleted = false) rad ON tindakanpelayanan_t.daftartindakan_id = rad.daftartindakan_id
     JOIN ( SELECT c.obatalkespasien_id,
            c.tindakanpelayanan_id,
            c.obatalkes_id,
            c.perawat1_id,
            c.perawat2_id,
            c.qty_oa,
            c.hargajual_oa,
            c.created_date,
            c.obatsudahbayar_id,
            c.ruangan_id,
            c.pendaftaran_id
           FROM obatalkespasien_t c
          WHERE c.is_deleted = false) obatalkespasien_t ON tindakanpelayanan_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN ( SELECT d.obatalkes_id,
            d.obatalkes_nama
           FROM obatalkes_m d) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT c.tindakanpelayanan_id,
            c.tindakanpelayananasal_id,
            c.daftartindakan_id,
            c.qty_tindakan,
            c.created_date,
            c.perawat1_id,
            c.perawat2_id
           FROM tindakanpelayanan_t c
          WHERE c.is_deleted = false) tindakanpelayanan_detail ON tindakanpelayanan_t.tindakanpelayanan_id = tindakanpelayanan_detail.tindakanpelayananasal_id
     JOIN ( SELECT d.daftartindakan_id,
            d.daftartindakan_nama
           FROM daftartindakan_m d) tindakan_detail ON tindakanpelayanan_detail.daftartindakan_id = tindakan_detail.daftartindakan_id
     LEFT JOIN ( SELECT e.pegawai_id,
            e.nama_pegawai
           FROM pegawai_m e) dok_dpjp ON dok_dpjp.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN ( SELECT f.pegawai_id,
            f.nama_pegawai
           FROM pegawai_m f) pegawai_1 ON pegawai_1.pegawai_id = obatalkespasien_t.perawat1_id
     LEFT JOIN ( SELECT g.pegawai_id,
            g.nama_pegawai
           FROM pegawai_m g) pegawai_2 ON pegawai_2.pegawai_id = obatalkespasien_t.perawat2_id
     LEFT JOIN ( SELECT h.ruangan_id,
            h.ruangan_nama
           FROM ruangan_m h) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LAB'::text AS tipe,
    'tindakan'::text AS jenis,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_detail.tindakanpelayananasal_id,
    tindakanpelayanan_detail.tindakanpelayanan_id AS tindakanpeldetail_id,
    tindakanpelayanan_detail.created_date AS tgl_tindakan,
    daftartindakan_m.daftartindakan_nama AS nama_pemeriksaan,
    tindakan_detail.daftartindakan_nama AS nama_tindakan,
    NULL::character varying AS obat,
    dok_dpjp.nama_pegawai AS dok_dpjp,
    pegawai_1.nama_pegawai AS petugas_1,
    pegawai_2.nama_pegawai AS petugas_2,
    tindakanpelayanan_detail.qty_tindakan AS qty,
        CASE
            WHEN tindakanpelayanan_t.tarif_tindakan = 0::double precision THEN false
            ELSE true
        END AS is_ditagihkan,
        CASE
            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL THEN false
            ELSE false
        END AS is_sudahbayar,
    NULL::integer AS obatalkespasien_id,
    NULL::character varying AS depo,
    tindakanpelayanan_t.tarif_tindakan AS harga_tindakan
   FROM tindakanpelayanan_t
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT b.daftartindakan_id
           FROM pemeriksaanlab_m b
          WHERE b.is_deleted = false) lab ON tindakanpelayanan_t.daftartindakan_id = lab.daftartindakan_id
     JOIN ( SELECT c.tindakanpelayanan_id,
            c.tindakanpelayananasal_id,
            c.daftartindakan_id,
            c.qty_tindakan,
            c.created_date,
            c.perawat1_id,
            c.perawat2_id
           FROM tindakanpelayanan_t c
          WHERE c.is_deleted = false) tindakanpelayanan_detail ON tindakanpelayanan_t.tindakanpelayanan_id = tindakanpelayanan_detail.tindakanpelayananasal_id
     JOIN ( SELECT d.daftartindakan_id,
            d.daftartindakan_nama
           FROM daftartindakan_m d) tindakan_detail ON tindakanpelayanan_detail.daftartindakan_id = tindakan_detail.daftartindakan_id
     LEFT JOIN ( SELECT e.pegawai_id,
            e.nama_pegawai
           FROM pegawai_m e) dok_dpjp ON dok_dpjp.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN ( SELECT f.pegawai_id,
            f.nama_pegawai
           FROM pegawai_m f) pegawai_1 ON pegawai_1.pegawai_id = tindakanpelayanan_detail.perawat1_id
     LEFT JOIN ( SELECT g.pegawai_id,
            g.nama_pegawai
           FROM pegawai_m g) pegawai_2 ON pegawai_2.pegawai_id = tindakanpelayanan_detail.perawat2_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'LAB'::text AS tipe,
    'obat'::text AS jenis,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    NULL::integer AS tindakanpelayananasal_id,
    NULL::integer AS tindakanpeldetail_id,
    obatalkespasien_t.created_date AS tgl_tindakan,
    daftartindakan_m.daftartindakan_nama AS nama_pemeriksaan,
    NULL::character varying AS nama_tindakan,
    obatalkes_m.obatalkes_nama AS obat,
    dok_dpjp.nama_pegawai AS dok_dpjp,
    pegawai_1.nama_pegawai AS petugas_1,
    pegawai_2.nama_pegawai AS petugas_2,
    obatalkespasien_t.qty_oa AS qty,
        CASE
            WHEN obatalkespasien_t.hargajual_oa = 0::double precision THEN false
            ELSE true
        END AS is_ditagihkan,
        CASE
            WHEN obatalkespasien_t.obatsudahbayar_id IS NULL THEN false
            ELSE false
        END AS is_sudahbayar,
    obatalkespasien_t.obatalkespasien_id,
    ruangan_m.ruangan_nama AS depo,
    tindakanpelayanan_t.tarif_tindakan AS harga_tindakan
   FROM tindakanpelayanan_t
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT b.daftartindakan_id
           FROM pemeriksaanlab_m b
          WHERE b.is_deleted = false) lab ON tindakanpelayanan_t.daftartindakan_id = lab.daftartindakan_id
     JOIN ( SELECT c.obatalkespasien_id,
            c.tindakanpelayanan_id,
            c.obatalkes_id,
            c.perawat1_id,
            c.perawat2_id,
            c.qty_oa,
            c.hargajual_oa,
            c.created_date,
            c.obatsudahbayar_id,
            c.ruangan_id
           FROM obatalkespasien_t c
          WHERE c.is_deleted = false) obatalkespasien_t ON tindakanpelayanan_t.tindakanpelayanan_id = obatalkespasien_t.tindakanpelayanan_id
     JOIN ( SELECT d.obatalkes_id,
            d.obatalkes_nama
           FROM obatalkes_m d) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT e.pegawai_id,
            e.nama_pegawai
           FROM pegawai_m e) dok_dpjp ON dok_dpjp.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN ( SELECT f.pegawai_id,
            f.nama_pegawai
           FROM pegawai_m f) pegawai_1 ON pegawai_1.pegawai_id = obatalkespasien_t.perawat1_id
     LEFT JOIN ( SELECT g.pegawai_id,
            g.nama_pegawai
           FROM pegawai_m g) pegawai_2 ON pegawai_2.pegawai_id = obatalkespasien_t.perawat2_id
     LEFT JOIN ( SELECT h.ruangan_id,
            h.ruangan_nama
           FROM ruangan_m h) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
  WHERE tindakanpelayanan_t.is_deleted = false;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211126_063337_migrate_pemakaiantindakanobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211126_063337_migrate_pemakaiantindakanobat_v cannot be reverted.\n";

        return false;
    }
    */
}
