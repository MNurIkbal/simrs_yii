<?php

use yii\db\Migration;

/**
 * Class m210122_082607_migrate_sy_20210122_penananggungjawab_m
 */
class m210122_082607_migrate_sy_20210122_penananggungjawab_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."penanggungjawab_m" 
            ADD COLUMN IF NOT EXISTS "pj_namadepan" varchar(50) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "pj_propinsi_id" int4,
            ADD COLUMN IF NOT EXISTS "pj_kabupaten_id" int4,
            ADD COLUMN IF NOT EXISTS "pj_kecamatan_id" int4,
            ADD COLUMN IF NOT EXISTS "pj_kelurahan_id" int4,
            ADD COLUMN IF NOT EXISTS "pj_pekerjaan_id" int4,
            ADD COLUMN IF NOT EXISTS "pj_rt" varchar(15) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "pj_rw" varchar(15) COLLATE "pg_catalog"."default";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210122_082607_migrate_sy_20210122_penananggungjawab_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210122_082607_migrate_sy_20210122_penananggungjawab_m cannot be reverted.\n";

        return false;
    }
    */
}
