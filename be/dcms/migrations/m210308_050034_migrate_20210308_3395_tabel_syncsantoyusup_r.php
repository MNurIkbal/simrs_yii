<?php

use yii\db\Migration;

/**
 * Class m210308_050034_migrate_20210308_3395_tabel_syncsantoyusup_r
 */
class m210308_050034_migrate_20210308_3395_tabel_syncsantoyusup_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE "public"."syncsantoyusup_r" (
            "syncsantoyusup_id" serial8,
            "pendaftaran_id" "pg_catalog"."int4",
            "pasien_id" "pg_catalog"."int4",
            "is_sync" "pg_catalog"."bool" DEFAULT false,
            "additional_data" "pg_catalog"."text" COLLATE "pg_catalog"."default",
            "created_date" "pg_catalog"."timestamp" NOT NULL DEFAULT (\'now\'::text)::date,
            "last_modified_date" "pg_catalog"."timestamp"
            )
            ;
        ');

        $this->execute('
            ALTER TABLE "public"."syncsantoyusup_r" 
            OWNER TO "postgres";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210308_050034_migrate_20210308_3395_tabel_syncsantoyusup_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210308_050034_migrate_20210308_3395_tabel_syncsantoyusup_r cannot be reverted.\n";

        return false;
    }
    */
}
