<?php

use yii\db\Migration;

/**
 * Class m210403_084221_migrate_20210403_infotarifbedah_v
 */
class m210403_084221_migrate_20210403_infotarifbedah_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('DROP VIEW if exists public.infotarifbedah_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infotarifbedah_v\" AS  SELECT tarifbedah_m.tarifbedah_id,
    tarifbedah_m.kegiatanoperasi_id,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    tarifbedah_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tarifbedah_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tarifbedah_m.persen_cyto,
    tarifbedah_m.tarif,
    tarifbedah_m.is_active
   FROM tarifbedah_m
     JOIN kegiatanoperasi_m ON tarifbedah_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
     JOIN kelaspelayanan_m ON tarifbedah_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tarifbedah_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false AND perdatarif_m.is_active = true
  WHERE tarifbedah_m.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."infotarifbedah_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210403_084221_migrate_20210403_infotarifbedah_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210403_084221_migrate_20210403_infotarifbedah_v cannot be reverted.\n";

        return false;
    }
    */
}
