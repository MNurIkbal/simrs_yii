<?php

use yii\db\Migration;

/**
 * Class m220726_031547_migrate_cdh_461_infoformsobarangdetail_v
 */
class m220726_031547_migrate_cdh_461_infoformsobarangdetail_v extends Migration
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
    formsobarangdetail_t.satuankecil_id,
    formsobarangdetail_t.is_newso,
    concat(\'1 \', uom.uom_besar, \' = \', uom.nilai_konversi, \' \', uom.uom_kecil) AS uom,
    satuan_kecil.satuanunit_nama AS satuankecil_nama,
    barang_m.barang_kode
   FROM formsobarangdetail_t
     JOIN ( SELECT formsobarang_t_1.formsobarang_id,
            formsobarang_t_1.ruangan_id,
            formsobarang_t_1.noformulir,
            formsobarang_t_1.tglformulir
           FROM formsobarang_t formsobarang_t_1) formsobarang_t ON formsobarangdetail_t.formsobarang_id = formsobarang_t.formsobarang_id
     JOIN ( SELECT a.barang_id,
            a.kelompokbarang_id,
            a.subkelompokbarang_id,
            a.barang_nama,
            a.satuan1_id,
            a.satuankecil_id,
            a.satuan2_id,
            a.barang_kode
           FROM barang_m a) barang_m ON formsobarangdetail_t.barang_id = barang_m.barang_id
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
     LEFT JOIN ( SELECT a.barang_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversibrg_m a
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT a2.satuanunit_id,
                    a2.satuanunit_nama
                   FROM satuanunit_m a2) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true
          GROUP BY a.barang_id, a.satuankecil_id, a.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a.nilai_konversi) uom ON barang_m.barang_id = uom.barang_id AND barang_m.satuan1_id = uom.satuanbesar_id AND barang_m.satuankecil_id = uom.satuankecil_id
     LEFT JOIN ( SELECT a.satuanunit_nama,
            a.satuanunit_id
           FROM satuanunit_m a) satuan_kecil ON formsobarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
  WHERE formsobarangdetail_t.is_deleted = false AND formsobarangdetail_t.is_active = true;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220726_031547_migrate_cdh_461_infoformsobarangdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220726_031547_migrate_cdh_461_infoformsobarangdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
