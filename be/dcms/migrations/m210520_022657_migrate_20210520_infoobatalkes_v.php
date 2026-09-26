<?php

use yii\db\Migration;

/**
 * Class m210520_022657_migrate_20210520_infoobatalkes_v
 */
class m210520_022657_migrate_20210520_infoobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute('DROP VIEW if exists "public"."infoobatalkes_v";');

$this->execute("
    CREATE VIEW \"public\".\"infoobatalkes_v\" AS  SELECT obatalkes_m.obatalkes_id,
    obatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.jenisobatalkes_nama,
    obatalkes_m.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    obatalkes_m.satuansedang_id,
    satuan_sedang.satuanunit_nama AS satuan_sedang,
    obatalkes_m.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    obatalkes_m.kemasan_besar,
    obatalkes_m.kemasan_sedang,
    obatalkes_m.ven,
    fgetnamalookup(obatalkes_m.ven) AS ven_nama,
    obatalkes_m.obatalkes_barcode,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nobatch,
    obatalkes_m.kekuatan_obat,
    fgetnamalookup(obatalkes_m.satuankekuatan::integer) AS satuan_kekuatan,
    obatalkes_m.ppn_persen,
    obatalkes_m.harganetto,
    obatalkes_m.hargajual,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.discount,
    obatalkes_m.tglkadaluarsa,
    obatalkes_m.minimalstok,
    obatalkes_m.is_generik,
    obatalkes_m.is_formularium,
    obatalkes_m.supplier_id,
    supplier_m.supplier_nama,
    obatalkes_m.indikasi,
    obatalkes_m.kontradiksi,
    obatalkes_m.interaksi,
    obatalkes_m.efek_samping,
    obatalkes_m.on_po,
    obatalkes_m.on_ro,
    obatalkes_m.is_consigment
   FROM obatalkes_m
     LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN satuanunit_m satuan_sedang ON obatalkes_m.satuansedang_id = satuan_sedang.satuanunit_id
     LEFT JOIN satuanunit_m satuan_besar ON obatalkes_m.satuanbesar_id = satuan_besar.satuanunit_id
     LEFT JOIN supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
  WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false;");

$this->execute('ALTER TABLE "public"."infoobatalkes_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210520_022657_migrate_20210520_infoobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210520_022657_migrate_20210520_infoobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
