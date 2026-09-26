<?php

use yii\db\Migration;

/**
 * Class m221129_034450_migrate_skema_table_gabungpelayanandetail_t
 */
class m221129_034450_migrate_skema_table_gabungpelayanandetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."gabungpelayanandetail_t" (
              "gabungpelayanandetail_id" serial8 NOT NULL ,
              "pendaftaran_id" int4,
              "ref_pendaftaran_id" int4,
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
        echo "m221129_034450_migrate_skema_table_gabungpelayanandetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221129_034450_migrate_skema_table_gabungpelayanandetail_t cannot be reverted.\n";

        return false;
    }
    */
}
