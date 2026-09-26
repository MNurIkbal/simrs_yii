<?php

use yii\db\Migration;

/**
 * Class m190408_040424_laporanobatalkesexpired_v_update
 */
class m190408_040424_laporanobatalkesexpired_v_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $this->execute('
           DROP VIEW laporanobatalkesexpired_v;
        ');

        $this->execute("
        CREATE OR REPLACE VIEW laporanobatalkesexpired_v AS 
 SELECT proses.obatalkes_id,
    sum(proses.qtystok_in - proses.qtystok_out) AS stok,
    proses.obatalkes_nama,
    proses.s_kecil AS satuan_kecil,
    proses.tglkadaluarsa,
    proses.harganetto,
    proses.harganetto * sum(proses.qtystok_in - proses.qtystok_out) AS jumlah_harganetto,
    proses.instalasi_nama,
    proses.ruangan_nama,
    proses.periodestokobat_id,
    proses.tglperiodestok_awal AS tglperiodeposting_awal,
    proses.tglperiodestok_akhir AS tglperiodeposting_akhir,
    proses.ruangan_id,
    proses.instalasi_id,
    proses.id_stok
   FROM ( SELECT
                CASE
                    WHEN stokobatalkes_t.stokobatalkesasal_id IS NULL THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.tglkadaluarsa,
            obatalkes_m.obatalkes_nama,
            satuan_kecil.satuanunit_nama AS s_kecil,
            obatalkes_m.harganetto,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokobatalkes_r.periodestokobat_id,
            periodestokobat_m.tglperiodestok_awal,
            periodestokobat_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id
           FROM stokobatalkes_t
             JOIN obatalkes_m ON stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN formstokopname_t ON stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id
             JOIN stokobatalkes_r ON stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id
             LEFT JOIN periodestokobat_m ON stokobatalkes_r.periodestokobat_id = periodestokobat_m.periodestokobat_id
             JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
          WHERE stokobatalkes_t.stokoa_aktif = true) proses
  GROUP BY proses.obatalkes_id, proses.obatalkes_nama, proses.tglkadaluarsa, proses.instalasi_nama, proses.ruangan_nama, proses.harganetto, proses.periodestokobat_id, proses.tglperiodestok_awal, proses.tglperiodestok_akhir, proses.ruangan_id, proses.instalasi_id, proses.id_stok, proses.s_kecil;

               ");
        
        $this->execute('
        ALTER TABLE laporanobatalkesexpired_v
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190408_040424_laporanobatalkesexpired_v_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_040424_laporanobatalkesexpired_v_update cannot be reverted.\n";

        return false;
    }
    */
}
