<?php

use yii\db\Migration;

/**
 * Class m210203_080549_migrate_20210203_obatalkesdetail_v
 */
class m210203_080549_migrate_20210203_obatalkesdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."obatalkesdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"obatalkesdetail_v\" AS  SELECT obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.jenisobatalkes_id,
    jenisobatalkes_m.jenisobatalkes_nama,
    obatalkes_m.ven AS ven_id,
    fgetnamalookup(obatalkes_m.ven) AS ven,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.kekuatan_obat,
    obatalkes_m.satuankecil_id,
    satuankecil.satuanunit_nama AS satuan_kecil,
    obatalkes_m.satuansedang_id,
    satuan_1.satuanunit_nama AS satuan_1,
    obatalkes_m.satuanbesar_id,
    satuan_2.satuanunit_nama AS satuan_2,
    obatalkes_m.kemasan_sedang,
    obatalkes_m.kemasan_besar,
    obatalkes_m.is_generik,
    obatalkes_m.tglkadaluarsa,
    obatalkes_m.obatalkes_nobatch,
    obatalkes_m.obatalkes_kategori,
    fgetnamalookup(obatalkes_m.obatalkes_kategori::integer) AS kategori,
    obatalkes_m.maksimalstok,
    obatalkes_m.supplier_id,
    supplier_m.supplier_nama,
    obatalkes_m.harga_beli,
    obatalkes_m.discount,
    obatalkes_m.ppn_persen,
    obatalkes_m.harganetto,
    obatalkes_m.hargamaksimum,
    obatalkes_m.hargaminimum,
    obatalkes_m.hargaratarata,
    obatalkes_m.hargaterakhir,
    obatalkes_m.indikasi,
    obatalkes_m.interaksi,
    obatalkes_m.kontradiksi,
    obatalkes_m.efek_samping,
    obatalkes_m.satuankekuatan,
    fgetnamalookup(obatalkes_m.satuankekuatan::integer) AS lookup_name,
    obatalkes_m.groupinacbg_id,
    obatalkes_m.lead_time,
    obatalkes_m.minimalstok,
    obatalkes_m.avg_usage,
    obatalkes_m.min_order,
    obatalkes_m.max_order,
    obatalkes_m.nilai_ro,
    obatalkes_m.on_po,
    obatalkes_m.on_ro,
    obatalkes_m.is_oral,
    obatalkes_m.manufaktur_id,
    manufaktur_m.nama AS manufaktur_nama
   FROM obatalkes_m
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuanunit_m satuankecil ON obatalkes_m.satuankecil_id = satuankecil.satuanunit_id
     LEFT JOIN satuanunit_m satuan_1 ON obatalkes_m.satuansedang_id = satuan_1.satuanunit_id
     LEFT JOIN satuanunit_m satuan_2 ON obatalkes_m.satuanbesar_id = satuan_2.satuanunit_id
     LEFT JOIN supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
     LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
  WHERE obatalkes_m.is_active = true AND obatalkes_m.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."obatalkesdetail_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210203_080549_migrate_20210203_obatalkesdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210203_080549_migrate_20210203_obatalkesdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
