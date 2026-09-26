<?php

use yii\db\Migration;

/**
 * Class m210826_041723_migrate_infopemakaianobatalkesdetail_v
 */
class m210826_041723_migrate_infopemakaianobatalkesdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopemakaianobatalkesdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopemakaianobatalkesdetail_v\" AS  SELECT pemakaianobatdetail_t.pemakaianobatdetail_id,
    pemakaianobatdetail_t.pemakaianobat_id,
    pemakaianobat_t.tglpemakaianobat,
    pemakaianobat_t.ruangan_id,
    pemakaianobat_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pemakaianobatdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    pemakaianobatdetail_t.qty_satuanpakai,
    pemakaianobatdetail_t.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuankecil_nama,
    pemakaianobat_t.nopemakaian_obat,
    pemakaianobatdetail_t.jumlah_input,
    pemakaianobatdetail_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuanbesar_nama,
    pemakaianobat_t.keterangan_pemakaianobat,
    pemakaianobatdetail_t.ket_obatpakai,
    obatalkes_m.obatalkes_nama
   FROM pemakaianobatdetail_t
     JOIN obatalkes_m ON pemakaianobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN pemakaianobat_t ON pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
     JOIN pegawai_m ON pemakaianobat_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN satuanunit_m satuan_kecil ON pemakaianobatdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN satuanunit_m satuan_besar ON pemakaianobatdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
  WHERE pemakaianobatdetail_t.is_active = true AND pemakaianobatdetail_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210826_041723_migrate_infopemakaianobatalkesdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210826_041723_migrate_infopemakaianobatalkesdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
