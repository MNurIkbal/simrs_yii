<?php

use yii\db\Migration;

/**
 * Class m210312_040913_migrate_20210312_informasipemakaianbarang_v
 */
class m210312_040913_migrate_20210312_informasipemakaianbarang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."informasipemakaianbarang_v";');
       
        $this->execute("
            CREATE VIEW \"public\".\"informasipemakaianbarang_v\" AS  SELECT pemakaianbarangdetail_t.pemakaianbarangdetail_id,
    pemakaianbarang_t.pemakaianbarang_id,
    instalasi_m.instalasi_id,
    pemakaianbarang_t.ruangan_id,
    pemakaianbarang_t.pegawai_id,
    pemakaianbarangdetail_t.satuankecil_id,
    pemakaianbarangdetail_t.satuanbesar_id,
    barang_m.barang_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    pegawai_m.nama_pegawai,
    pemakaianbarang_t.tgl_pemakaianbarang,
    pemakaianbarang_t.no_pemakaianbarang,
    pemakaianbarang_t.untuk_keperluan,
    pemakaianbarang_t.keteranganpakai,
    barang_m.barang_nama,
    pemakaianbarangdetail_t.jumlah_pakai,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    pemakaianbarangdetail_t.harga_netto,
    pemakaianbarangdetail_t.jumlah_input,
    satuan_besar.satuanunit_nama AS satuan_besar
   FROM pemakaianbarangdetail_t
     JOIN pemakaianbarang_t ON pemakaianbarangdetail_t.pemakaianbarang_id = pemakaianbarang_t.pemakaianbarang_id
     JOIN barang_m ON pemakaianbarangdetail_t.barang_id = barang_m.barang_id
     JOIN ruangan_m ON ruangan_m.ruangan_id = pemakaianbarang_t.ruangan_id
     JOIN instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
     JOIN pegawai_m ON pegawai_m.pegawai_id = pemakaianbarang_t.pegawai_id
     LEFT JOIN satuanunit_m satuan_kecil ON pemakaianbarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN satuanunit_m satuan_besar ON pemakaianbarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
  WHERE pemakaianbarangdetail_t.is_active = true AND pemakaianbarangdetail_t.is_deleted = false;
");
        
        $this->execute('ALTER TABLE "public"."informasipemakaianbarang_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_040913_migrate_20210312_informasipemakaianbarang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_040913_migrate_20210312_informasipemakaianbarang_v cannot be reverted.\n";

        return false;
    }
    */
}
