<?php

use yii\db\Migration;

/**
 * Class m220420_175023_migrate_sobarang_infoformsobarangdetail_v
 */
class m220420_175023_migrate_sobarang_infoformsobarangdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infoformsobarangdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoformsobarangdetail_v" AS  SELECT formsobarangdetail_t.formsobarangdetail_id,
    formsobarangdetail_t.formsobarang_id,
    formsobarangdetail_t.barang_id,
    kelompokbarang_m.kelompokbarang_id,
    kelompokbarang_m.kelompokbarang_nama AS kelompok_barang,
    subkelompokbarang_m.subkelompokbarang_id,
    subkelompokbarang_m.subkelompok_nama AS subkelompok_barang,
    formsobarang_t.ruangan_id,
    ruangan_m.instalasi_id,
    formsobarang_t.noformulir,
    formsobarang_t.tglformulir,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    barang_m.barang_nama,
    formsobarangdetail_t.stok,
    formsobarangdetail_t.nobatch,
    formsobarangdetail_t.stokbarang_id,
    stokopnamebarangdetail_t.volume_fisik,
    stokopnamebarangdetail_t.volume_sistem,
    stokopnamebarangdetail_t.kondisibarang,
    stokopnamebarangdetail_t.jmlselisihstok,
    formsobarangdetail_t.tgl_kadaluarsa,
    stokopnamebarangdetail_t.revisi_stok,
    stokopnamebarangdetail_t.stokopnamebarangdetail_id,
    formsobarangdetail_t.harganetto,
    formsobarangdetail_t.satuankecil_id
   FROM formsobarangdetail_t
     JOIN formsobarang_t ON formsobarangdetail_t.formsobarang_id = formsobarang_t.formsobarang_id
     JOIN barang_m ON formsobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     LEFT JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
     JOIN ruangan_m ON formsobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN stokopnamebarangdetail_t ON formsobarangdetail_t.stokopnamebarangdetail_id = stokopnamebarangdetail_t.stokopnamebarangdetail_id
  WHERE formsobarangdetail_t.is_deleted = false AND formsobarangdetail_t.is_active = true;        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220420_175023_migrate_sobarang_infoformsobarangdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220420_175023_migrate_sobarang_infoformsobarangdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
