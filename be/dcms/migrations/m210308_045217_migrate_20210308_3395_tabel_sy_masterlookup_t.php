<?php

use yii\db\Migration;

/**
 * Class m210308_045217_migrate_20210308_3395_tabel_sy_masterlookup_t
 */
class m210308_045217_migrate_20210308_3395_tabel_sy_masterlookup_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE "public"."sy_masterlookup_t" (
            "master_id" serial8,
            "kd_master" "pg_catalog"."varchar" COLLATE "pg_catalog"."default",
            "nm_master" "pg_catalog"."varchar" COLLATE "pg_catalog"."default",
            "m_default" "pg_catalog"."varchar" COLLATE "pg_catalog"."default",
            "lookup_id" "pg_catalog"."int4",
            "ket1" "pg_catalog"."varchar" COLLATE "pg_catalog"."default",
            "additional_data" "pg_catalog"."text" COLLATE "pg_catalog"."default",
            "created_date" "pg_catalog"."timestamp" NOT NULL DEFAULT (\'now\'::text)::date,
            "created_by" "pg_catalog"."int4",
            "modified_count" "pg_catalog"."int4",
            "last_modified_date" "pg_catalog"."timestamp",
            "last_modified_by" "pg_catalog"."int4",
            "is_deleted" "pg_catalog"."bool" NOT NULL DEFAULT false,
            "is_active" "pg_catalog"."bool" NOT NULL DEFAULT true,
            "deleted_date" "pg_catalog"."timestamp",
            "deleted_by" "pg_catalog"."int4"
            )
            ;
        ');

        $this->execute('
            ALTER TABLE "public"."sy_masterlookup_t" 
            OWNER TO "postgres";
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210308_045217_migrate_20210308_3395_tabel_sy_masterlookup_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210308_045217_migrate_20210308_3395_tabel_sy_masterlookup_t cannot be reverted.\n";

        return false;
    }
    */
}
