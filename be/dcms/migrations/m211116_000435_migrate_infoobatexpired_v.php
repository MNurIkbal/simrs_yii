<?php

use yii\db\Migration;

/**
 * Class m211116_000435_migrate_infoobatexpired_v
 */
class m211116_000435_migrate_infoobatexpired_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('DROP VIEW if exists "public"."infoobatexpired_v";');

       $this->execute("
        CREATE VIEW \"public\".\"infoobatexpired_v\" AS  SELECT array_agg(stokobatalkes_t.stokobatalkes_id) AS id_stok,
    stokobatalkes_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    stokobatalkes_t.tglkadaluarsa,
    sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) AS stok,
        CASE
            WHEN mutasi.status_mutasi = 401 THEN sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) - COALESCE(mutasi.jumlah, 0::double precision)
            ELSE sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)
        END AS stok_exp,
    COALESCE(mutasi.jumlah, 0::double precision) AS jumlah,
    mutasi.status_mutasi,
    obatalkes_m.harganetto,
    sum(obatalkes_m.harganetto * (stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) AS jumlah_harganetto,
    obatalkes_m.satuankecil_id,
    satuanunit_m.satuanunit_nama AS satuan_kecil,
    stokobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama
   FROM stokobatalkes_t
     JOIN obatalkes_m ON obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id
     JOIN ruangan_m ON ruangan_m.ruangan_id = stokobatalkes_t.ruangan_id
     JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
     LEFT JOIN satuanunit_m ON satuanunit_m.satuanunit_id = stokobatalkes_t.satuankecil_id
     LEFT JOIN ( SELECT mutasiobatdetail_t.obatalkes_id,
            mutasiobatdetail_t.tgl_kadaluarsa,
            sum(mutasiobatdetail_t.jumlah_mutasi) AS jumlah,
            mutasiobatruangan_t.status_mutasi,
            mutasiobatruangan_t.ruanganasal_id AS ruangan_id
           FROM mutasiobatdetail_t
             JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
          WHERE mutasiobatruangan_t.status_mutasi = 401 AND mutasiobatruangan_t.is_deleted IS FALSE
          GROUP BY mutasiobatdetail_t.obatalkes_id, mutasiobatdetail_t.tgl_kadaluarsa, mutasiobatruangan_t.status_mutasi, mutasiobatruangan_t.ruanganasal_id) mutasi ON stokobatalkes_t.obatalkes_id = mutasi.obatalkes_id AND stokobatalkes_t.tglkadaluarsa = mutasi.tgl_kadaluarsa AND stokobatalkes_t.ruangan_id = mutasi.ruangan_id
  GROUP BY stokobatalkes_t.obatalkes_id, obatalkes_m.obatalkes_nama, obatalkes_m.satuankecil_id, satuanunit_m.satuanunit_nama, stokobatalkes_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, stokobatalkes_t.tglkadaluarsa, obatalkes_m.harganetto, mutasi.jumlah, mutasi.status_mutasi
 HAVING sum(stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out) > 0::double precision;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211116_000435_migrate_infoobatexpired_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_000435_migrate_infoobatexpired_v cannot be reverted.\n";

        return false;
    }
    */
}
