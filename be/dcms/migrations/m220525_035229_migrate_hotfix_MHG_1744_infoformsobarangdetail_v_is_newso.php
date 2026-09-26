<?php

use yii\db\Migration;

/**
 * Class m220525_035229_migrate_hotfix_MHG_1744_infoformsobarangdetail_v_is_newso
 */
class m220525_035229_migrate_hotfix_MHG_1744_infoformsobarangdetail_v_is_newso extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infoformsobarangdetail_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"infoformsobarangdetail_v\" AS    SELECT formsobarangdetail_t.formsobarangdetail_id,
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
    formsobarangdetail_t.satuankecil_id,
    formsobarangdetail_t.is_newso
   FROM formsobarangdetail_t
     JOIN ( SELECT formsobarang_t_1.formsobarang_id,
            formsobarang_t_1.ruangan_id,
            formsobarang_t_1.noformulir,
            formsobarang_t_1.tglformulir
           FROM formsobarang_t formsobarang_t_1) formsobarang_t ON formsobarangdetail_t.formsobarang_id = formsobarang_t.formsobarang_id
     JOIN ( SELECT barang_m_1.barang_id,
            barang_m_1.kelompokbarang_id,
            barang_m_1.subkelompokbarang_id,
            barang_m_1.barang_nama
           FROM barang_m barang_m_1) barang_m ON formsobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN ( SELECT kelompokbarang_m_1.kelompokbarang_id,
            kelompokbarang_m_1.kelompokbarang_nama
           FROM kelompokbarang_m kelompokbarang_m_1) kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     LEFT JOIN ( SELECT subkelompokbarang_m_1.subkelompokbarang_id,
            subkelompokbarang_m_1.subkelompok_nama
           FROM subkelompokbarang_m subkelompokbarang_m_1) subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
     JOIN ( SELECT ruangan_m_1.ruangan_id,
            ruangan_m_1.ruangan_nama,
            ruangan_m_1.instalasi_id
           FROM ruangan_m ruangan_m_1) ruangan_m ON formsobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT instalasi_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama
           FROM instalasi_m instalasi_m_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT stokopnamebarangdetail_t_1.stokopnamebarangdetail_id,
            stokopnamebarangdetail_t_1.volume_fisik,
            stokopnamebarangdetail_t_1.volume_sistem,
            stokopnamebarangdetail_t_1.kondisibarang,
            stokopnamebarangdetail_t_1.jmlselisihstok,
            stokopnamebarangdetail_t_1.revisi_stok
           FROM stokopnamebarangdetail_t stokopnamebarangdetail_t_1) stokopnamebarangdetail_t ON formsobarangdetail_t.stokopnamebarangdetail_id = stokopnamebarangdetail_t.stokopnamebarangdetail_id
  WHERE formsobarangdetail_t.is_deleted = false AND formsobarangdetail_t.is_active = true;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220525_035229_migrate_hotfix_MHG_1744_infoformsobarangdetail_v_is_newso cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220525_035229_migrate_hotfix_MHG_1744_infoformsobarangdetail_v_is_newso cannot be reverted.\n";

        return false;
    }
    */
}
