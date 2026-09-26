<?php

use yii\db\Migration;

/**
 * Class m211222_022643_migrate_pemakaiantindakanobatpenunjang_v
 */
class m211222_022643_migrate_pemakaiantindakanobatpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.pemakaiantindakanobatpenunjang_v;');
        
        $this->execute("
            CREATE VIEW \"public\".\"pemakaiantindakanobatpenunjang_v\" AS  SELECT 'PENUNJANG'::text AS tipe,
    'tindakan'::text AS jenis,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    tindakanpelayanan_detail.tindakanpelayanan_id,
    tindakanpelayanan_detail.tindakanpelayananasal_id,
    NULL::integer AS tindakanpeldetail_id,
    tindakanpelayanan_t.created_date AS tgl_tindakan,
    tindakanpelayanan_detail.daftartindakan_id,
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
    tindakanpelayanan_detail.tarif_tindakan AS harga_tindakan,
    tindakanpelayanan_t.instalasi_id
   FROM tindakanpelayanan_t
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT a.tindakanpelayanan_id,
            a.tindakanpelayananasal_id,
            a.daftartindakan_id,
            a.qty_tindakan,
            a.created_date,
            a.perawat1_id,
            a.perawat2_id,
            a.tarif_tindakan
           FROM tindakanpelayanan_t a
          WHERE a.is_deleted = false) tindakanpelayanan_detail ON tindakanpelayanan_t.tindakanpelayanan_id = tindakanpelayanan_detail.tindakanpelayananasal_id
     LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) tindakan_detail ON tindakanpelayanan_detail.daftartindakan_id = tindakan_detail.daftartindakan_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dok_dpjp ON dok_dpjp.pegawai_id = tindakanpelayanan_t.dokterpenanggungjawab_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_1 ON pegawai_1.pegawai_id = tindakanpelayanan_detail.perawat1_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_2 ON pegawai_2.pegawai_id = tindakanpelayanan_detail.perawat2_id
  WHERE tindakanpelayanan_t.is_deleted = false
UNION ALL
 SELECT 'PENUNJANG'::text AS tipe,
    'obat'::text AS jenis,
    obatalkespasien_t.pendaftaran_id,
    obatalkespasien_t.pasienmasukpenunjang_id,
    obatalkespasien_t.tindakanpelayanan_id,
    0 AS tindakanpelayananasal_id,
    NULL::integer AS tindakanpeldetail_id,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    obatalkespasien_t.daftartindakan_id,
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
    NULL::character varying AS depo,
    obatalkespasien_t.hargajual_oa AS harga_tindakan,
    tindakanpelayanan_t.instalasi_id
   FROM obatalkespasien_t
     LEFT JOIN tindakanpelayanan_t ON obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
     LEFT JOIN ( SELECT b.daftartindakan_id,
            b.daftartindakan_nama
           FROM daftartindakan_m b) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama
           FROM obatalkes_m b) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) dok_dpjp ON dok_dpjp.pegawai_id = obatalkespasien_t.pegawai_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_1 ON pegawai_1.pegawai_id = obatalkespasien_t.perawat1_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_2 ON pegawai_2.pegawai_id = obatalkespasien_t.perawat2_id
  WHERE obatalkespasien_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211222_022643_migrate_pemakaiantindakanobatpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211222_022643_migrate_pemakaiantindakanobatpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
