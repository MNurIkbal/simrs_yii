<?php

use yii\db\Migration;

/**
 * Class m221006_104836_migrate_MHG4399_numeric_detailformulirstokopname_v
 */
class m221006_104836_migrate_MHG4399_numeric_detailformulirstokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."detailformulirstokopname_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"detailformulirstokopname_v\" AS  SELECT formstokopname_t.formstokopname_id,
    formstokopname_t.formulirstokopname_id,
    round(formstokopname_t.volume_stok::numeric, 3) AS stok_sistem,
    formstokopname_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    formstokopname_t.nobatch,
        CASE
            WHEN stokobatalkes_t.tglkadaluarsa IS NULL THEN formstokopname_t.tglkadaluarsa
            ELSE stokobatalkes_t.tglkadaluarsa
        END AS tglkadaluarsa,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS hargajual,
    obatalkes_m.harganetto,
    formstokopname_t.stokobatalkes_id,
    obatalkes_m.satuankecil_id,
    sat_kecil.satuanunit_nama,
    obatalkes_m.obatalkes_kode,
    COALESCE(laci.rak_id::bigint, rak.rakobat_id) AS rakobat_id,
    COALESCE(laci.rak, rak.rakobat_nama) AS rak,
    COALESCE(laci.rak, rak.rakobat_nama) AS rakobat_nama,
    COALESCE(laci.rakobat_id, rak.rakobat_id) AS laciobat_id,
    COALESCE(laci.rakobat_nama, rak.rakobat_nama) AS laci,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    COALESCE(round(stokobatalkes.stok_in::numeric, 3), 0::numeric) - COALESCE(round(stokobatalkes.stok_out::numeric, 3), 0::numeric) + COALESCE(round(formstokopname_t.volume_stok::numeric, 3), 0::numeric) AS stok_saatini,
    round(stokobatalkes.stok_in::numeric, 3) AS stok_in,
    round(stokobatalkes.stok_out::numeric, 3) AS stok_out,
    jenisobatalkes_m.jenisobatalkes_nama
   FROM formstokopname_t
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.obatalkes_namalain,
            a.obatalkes_kode,
            a.harganetto,
            a.satuankecil_id,
            a.satuanbesar_id,
            a.jenisobatalkes_id
           FROM obatalkes_m a) obatalkes_m ON formstokopname_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.stokobatalkes_id,
            a.tglkadaluarsa
           FROM stokobatalkes_t a
          WHERE a.is_deleted = false) stokobatalkes_t ON formstokopname_t.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.ruangan_id,
            a.qty_sisa
           FROM stokobatalkes_r a) stokobatalkes_r ON formstokopname_t.obatalkes_id = stokobatalkes_r.obatalkes_id AND formstokopname_t.ruangan_id = stokobatalkes_r.ruangan_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.obatalkes_id,
            a.rakobat_id
           FROM konfigrak_m a) konfigrak_m ON formstokopname_t.ruangan_id = konfigrak_m.ruangan_id AND formstokopname_t.obatalkes_id = konfigrak_m.obatalkes_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.rakobat_nama
           FROM rakobat_m a) rak ON konfigrak_m.rakobat_id = rak.rakobat_id
     LEFT JOIN ( SELECT a.rakobat_id,
            a.parentrakobat_id AS rak_id,
            rak_1.rakobat_nama AS rak,
            a.rakobat_nama
           FROM rakobat_m a
             JOIN rakobat_m rak_1 ON a.parentrakobat_id = rak_1.rakobat_id
          WHERE a.parentrakobat_id IS NOT NULL) laci ON konfigrak_m.rakobat_id = laci.rakobat_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversi_m a
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT a2.satuanunit_id,
                    a2.satuanunit_nama
                   FROM satuanunit_m a2) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true
          GROUP BY a.obatalkes_id, a.satuankecil_id, a.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a.nilai_konversi) uom ON obatalkes_m.obatalkes_id = uom.obatalkes_id AND obatalkes_m.satuanbesar_id = uom.satuanbesar_id AND obatalkes_m.satuankecil_id = uom.satuankecil_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.ruangan_id,
            sum(a.qtystok_in) AS stok_in,
            sum(a.qtystok_out) AS stok_out,
            form_so.formulirstokopname_id
           FROM stokobatalkes_t a
             LEFT JOIN ( SELECT a1.created_date,
                    a1.formulirstokopname_id,
                    a1.ruangan_id
                   FROM formulirstokopname_t a1) form_so ON a.created_date > form_so.created_date
          WHERE a.ruangan_id = form_so.ruangan_id
          GROUP BY a.obatalkes_id, form_so.formulirstokopname_id, a.ruangan_id) stokobatalkes ON stokobatalkes.obatalkes_id = formstokopname_t.obatalkes_id AND stokobatalkes.formulirstokopname_id = formstokopname_t.formulirstokopname_id AND stokobatalkes.ruangan_id = formstokopname_t.ruangan_id
  WHERE formstokopname_t.is_active = true AND formstokopname_t.is_deleted = false;  ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221006_104836_migrate_MHG4399_numeric_detailformulirstokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221006_104836_migrate_MHG4399_numeric_detailformulirstokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
