<?php

use yii\db\Migration;

/**
 * Class m220411_064536_migrate_table_pembatalanuangmuka_t
 */
class m220408_065032_migrate_multypayer_table_pembatalanuangmuka_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."pembatalanuangmuka_t" (
              "pembatalanuangmuka_id" serial8 NOT NULL PRIMARY KEY,
              "bayaruangmuka_id" int4,
              "tandabuktikeluar_id" int4,
              "tandabuktibayar_id" int4,
              "ruangan_id" int4 NOT NULL,
              "tglpembatalan" timestamp(6) NOT NULL,
              "keterangan_batal" text COLLATE "pg_catalog"."default" NOT NULL,
              "jmlkaskeluarbatal" float8,
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_065032_migrate_multypayer_table_pembatalanuangmuka_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_065032_migrate_multypayer_table_pembatalanuangmuka_t cannot be reverted.\n";

        return false;
    }
    */
}
