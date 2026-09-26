<?php

use yii\db\Migration;

/**
 * Class m220613_092112_migrate_MHG2523_table_tindakanluarbedah_mp
 */
class m220613_092112_migrate_MHG2523_table_tindakanluarbedah_mp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."tindakanluarbedah_mp" (
                "daftartindakan_id" int4 NOT NULL,
                "tindakanluarbedah_id" int4 NOT NULL,
                "qty" float8,
                "is_ditagihkan" bool DEFAULT true,
                "additional_data" text COLLATE "pg_catalog"."default",
                "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4,
                "modified_count" int4,
                "last_modified_date" timestamp(6),
                "last_modified_by" int4,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6),
                "deleted_by" int4,
                CONSTRAINT "tindakanluarbedah_mp_pkey" PRIMARY KEY ("daftartindakan_id", "tindakanluarbedah_id")
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220613_092112_migrate_MHG2523_table_tindakanluarbedah_mp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220613_092112_migrate_MHG2523_table_tindakanluarbedah_mp cannot be reverted.\n";

        return false;
    }
    */
}
