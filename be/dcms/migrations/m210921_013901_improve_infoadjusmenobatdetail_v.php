<?php

use yii\db\Migration;

/**
 * Class m210921_013901_improve_infoadjusmenobatdetail_v
 */
class m210921_013901_improve_infoadjusmenobatdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infoadjusmenobatdetail_v;');
        
        $this->execute("CREATE VIEW \"public\".\"infoadjusmenobatdetail_v\" AS  SELECT 'masuk'::text AS jenis,
    adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobatmasuk_t.adjusmenobatmasuk_id AS detail_id,
    adjusmenobatmasuk_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    adjusmenobatmasuk_t.qty AS qty_input,
    adjusmenobatmasuk_t.qty_konversi,
    adjusmenobatmasuk_t.tgl_kadaluarsa,
    adjusmenobatmasuk_t.harga_netto,
    adjusmenobatmasuk_t.no_batch,
    adjusmenobatmasuk_t.keterangan,
    NULL::text AS alasan,
    adjusmenobatmasuk_t.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    adjusmenobatmasuk_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    obatalkes_m.obatalkes_kode
   FROM adjusmenobat_t
     JOIN ( SELECT a.adjusmenobat_id,
            a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            a.adjusmenobatmasuk_id,
            a.qty,
            a.qty_konversi,
            a.tgl_kadaluarsa,
            a.harga_netto,
            a.no_batch,
            a.keterangan
           FROM adjusmenobatmasuk_t a
          WHERE a.is_deleted = false) adjusmenobatmasuk_t ON adjusmenobat_t.adjusmenobat_id = adjusmenobatmasuk_t.adjusmenobat_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.obatalkes_nama
           FROM obatalkes_m a) obatalkes_m ON adjusmenobatmasuk_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_kecil ON adjusmenobatmasuk_t.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_besar ON adjusmenobatmasuk_t.satuanbesar_id = satuan_besar.satuanunit_id
  WHERE adjusmenobat_t.is_deleted = false
UNION ALL
 SELECT 'keluar'::text AS jenis,
    adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobatkeluar_t.adjusmenobatkeluar_id AS detail_id,
    adjusmenobatkeluar_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    adjusmenobatkeluar_t.qty AS qty_input,
    adjusmenobatkeluar_t.qty_konversi,
    NULL::timestamp without time zone AS tgl_kadaluarsa,
    NULL::double precision AS harga_netto,
    adjusmenobatkeluar_t.no_batch,
    adjusmenobatkeluar_t.keterangan,
    adjusmenobatkeluar_t.alasan,
    adjusmenobatkeluar_t.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    adjusmenobatkeluar_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    obatalkes_m.obatalkes_kode
   FROM adjusmenobat_t
     JOIN ( SELECT b.adjusmenobat_id,
            b.obatalkes_id,
            b.satuanbesar_id,
            b.satuankecil_id,
            b.adjusmenobatkeluar_id,
            b.qty,
            b.qty_konversi,
            b.no_batch,
            b.keterangan,
            b.alasan
           FROM adjusmenobatkeluar_t b
          WHERE b.is_deleted = false) adjusmenobatkeluar_t ON adjusmenobat_t.adjusmenobat_id = adjusmenobatkeluar_t.adjusmenobat_id
     JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_kode,
            b.obatalkes_nama
           FROM obatalkes_m b) obatalkes_m ON adjusmenobatkeluar_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT b.satuanunit_id,
            b.satuanunit_nama
           FROM satuanunit_m b) satuan_kecil ON adjusmenobatkeluar_t.satuankecil_id = satuan_kecil.satuanunit_id
     JOIN ( SELECT b.satuanunit_id,
            b.satuanunit_nama
           FROM satuanunit_m b) satuan_besar ON adjusmenobatkeluar_t.satuanbesar_id = satuan_besar.satuanunit_id
  WHERE adjusmenobat_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210921_013901_improve_infoadjusmenobatdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210921_013901_improve_infoadjusmenobatdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
