<?php

use yii\db\Migration;

/**
 * Class m210921_054703_improve_infoobatpemusnahan_v
 */
class m210921_054703_improve_infoobatpemusnahan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infoobatpemusnahan_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infoobatpemusnahan_v\" AS  SELECT array_agg(stokobatalkes_t.stokobatalkes_id) AS id_stok,
    stokobatalkes_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    stokobatalkes_t.tglkadaluarsa,
    sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) AS stok,
        CASE
            WHEN pemusnahan.is_verifikasi = false THEN sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) - COALESCE(pemusnahan.jumlah, 0::double precision)
            ELSE sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)
        END AS stok_exp,
    COALESCE(pemusnahan.jumlah, 0::double precision) AS jumlah,
    pemusnahan.is_verifikasi,
    obatalkes_m.harganetto,
    sum(obatalkes_m.harganetto * (stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) AS jumlah_harganetto,
    obatalkes_m.satuankecil_id,
    satuanunit_m.satuanunit_nama AS satuan_kecil,
    stokobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    NULL::text AS periodestokobat_id,
    NULL::text AS tglperiodeposting_awal,
    NULL::text AS tglperiodeposting_akhir,
    NULL::text AS nobatch,
    NULL::text AS margin,
    NULL::text AS ppn,
    NULL::text AS disc,
    obatalkes_m.obatalkes_kode
   FROM stokobatalkes_t
     JOIN obatalkes_m ON obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id
     JOIN ruangan_m ON ruangan_m.ruangan_id = stokobatalkes_t.ruangan_id
     JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
     LEFT JOIN satuanunit_m ON satuanunit_m.satuanunit_id = stokobatalkes_t.satuankecil_id
     LEFT JOIN ( SELECT pemusnahanobatdetail_t.obatalkes_id,
            pemusnahanobatdetail_t.tglkadaluarsa,
            sum(pemusnahanobatdetail_t.jumlah) AS jumlah,
            pemusnahanobat_t.is_verifikasi,
            pemusnahanobat_t.ruangan_id
           FROM pemusnahanobatdetail_t
             JOIN pemusnahanobat_t ON pemusnahanobatdetail_t.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id
          WHERE pemusnahanobat_t.is_verifikasi = false AND pemusnahanobat_t.is_deleted = false
          GROUP BY pemusnahanobatdetail_t.obatalkes_id, pemusnahanobatdetail_t.tglkadaluarsa, pemusnahanobat_t.is_verifikasi, pemusnahanobat_t.ruangan_id) pemusnahan ON stokobatalkes_t.obatalkes_id = pemusnahan.obatalkes_id AND stokobatalkes_t.tglkadaluarsa = pemusnahan.tglkadaluarsa AND stokobatalkes_t.ruangan_id = pemusnahan.ruangan_id
  GROUP BY stokobatalkes_t.obatalkes_id, obatalkes_m.obatalkes_nama, obatalkes_m.satuankecil_id, satuanunit_m.satuanunit_nama, stokobatalkes_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, stokobatalkes_t.tglkadaluarsa, obatalkes_m.harganetto, pemusnahan.jumlah, pemusnahan.is_verifikasi, obatalkes_m.obatalkes_kode
 HAVING sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) > 0::double precision;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210921_054703_improve_infoobatpemusnahan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210921_054703_improve_infoobatpemusnahan_v cannot be reverted.\n";

        return false;
    }
    */
}
