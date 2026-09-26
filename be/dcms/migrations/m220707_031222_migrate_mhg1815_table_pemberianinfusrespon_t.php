<?php

use yii\db\Migration;

/**
 * Class m220707_031222_migrate_mhg1815_table_pemberianinfusrespon_t
 */
class m220707_031222_migrate_mhg1815_table_pemberianinfusrespon_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."pemberianinfusrespon_t" (
                "pemberianinfusrespon_id" serial8 NOT NULL PRIMARY KEY,
                "pemberianinfus_id" int8 NOT NULL,
                "tgl_respon" timestamp(6),
                "pegawai_id" int4,
                "respon" text COLLATE "pg_catalog"."default",
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
        echo "m220707_031222_migrate_mhg1815_table_pemberianinfusrespon_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220707_031222_migrate_mhg1815_table_pemberianinfusrespon_t cannot be reverted.\n";

        return false;
    }
    */
}
