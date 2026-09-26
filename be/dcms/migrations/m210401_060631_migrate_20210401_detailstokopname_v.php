<?php

use yii\db\Migration;

/**
 * Class m210401_060631_migrate_20210401_detailstokopname_v
 */
class m210401_060631_migrate_20210401_detailstokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."detailstokopname_v";');


        $this->execute('CREATE VIEW "public"."detailstokopname_v" AS  SELECT stokopnamedetail.stokopnamedetail_id,
    stokopnamedetail.formstokopname_id,
    stokopnamedetail.formulirstokopname_id,
    stokopnamedetail.volume_sistem AS stok_sistem,
    stokopnamedetail.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS hargajual,
    obatalkes_m.harganetto,
    stokopnamedetail.stokobatalkes_id,
    obatalkes_m.satuankecil_id,
    sat_kecil.satuanunit_nama,
    rakobat_m.rakobat_nama,
        CASE
            WHEN stokobatalkes_t.tglkadaluarsa IS NULL THEN stokopnamedetail.tglkadaluarsa
            ELSE stokobatalkes_t.tglkadaluarsa
        END AS tglkadaluarsa,
    stokopnamedetail.volume_fisik AS stok_fisik
   FROM ( SELECT stokopnamedetail_t.stokopnamedetail_id,
            stokopnamedetail_t.formstokopname_id,
            stokopnamedetail_t.satuankecil_id,
            stokopnamedetail_t.sumberdana_id,
            stokopnamedetail_t.stokopname_id,
            stokopnamedetail_t.obatalkes_id,
            stokopnamedetail_t.volume_fisik,
            stokopnamedetail_t.volume_sistem,
            stokopnamedetail_t.hargasatuan,
            stokopnamedetail_t.jumlahharga,
            stokopnamedetail_t.harganetto,
            stokopnamedetail_t.jumlahnetto,
            stokopnamedetail_t.tglkadaluarsa,
            stokopnamedetail_t.kondisibarang,
            stokopnamedetail_t.tglperiksafisik,
            stokopnamedetail_t.jmlselisihstok,
            stokopnamedetail_t.stokobatalkes_id,
            stokopnamedetail_t.additional_data,
            stokopnamedetail_t.created_date,
            stokopnamedetail_t.created_by,
            stokopnamedetail_t.modified_count,
            stokopnamedetail_t.last_modified_date,
            stokopnamedetail_t.last_modified_by,
            stokopnamedetail_t.is_deleted,
            stokopnamedetail_t.is_active,
            stokopnamedetail_t.deleted_date,
            stokopnamedetail_t.deleted_by,
            stokopname_t_1.ruangan_id,
            stokopname_t_1.formulirstokopname_id
           FROM stokopnamedetail_t
             JOIN stokopname_t stokopname_t_1 ON stokopnamedetail_t.stokopname_id = stokopname_t_1.stokopname_id) stokopnamedetail
     JOIN obatalkes_m ON stokopnamedetail.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN stokopname_t ON stokopnamedetail.stokopname_id = stokopname_t.stokopname_id
     LEFT JOIN stokobatalkes_t ON stokopnamedetail.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
     LEFT JOIN satuanunit_m sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN konfigrak_m ON stokopnamedetail.ruangan_id = konfigrak_m.ruangan_id AND stokopnamedetail.obatalkes_id = konfigrak_m.obatalkes_id
     LEFT JOIN rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
  WHERE stokopnamedetail.is_active = true AND stokopnamedetail.is_deleted = false;');

        $this->execute('ALTER TABLE "public"."detailstokopname_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210401_060631_migrate_20210401_detailstokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210401_060631_migrate_20210401_detailstokopname_v cannot be reverted.\n";

        return false;
    }
    */
}
