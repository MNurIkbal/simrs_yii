<?php

use yii\db\Migration;

/**
 * Class m220610_073015_migrate_MHG1808_table_skriningnrs_m
 */
class m220610_073015_migrate_MHG1808_table_skriningnrs_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS public.skriningnrs_m (
                skriningnrs_id serial4 NOT NULL PRIMARY KEY,
                skriningnrs_nama VARCHAR(150),
                skor int2,
                jenisskrining_id int4,
                additional_data text COLLATE pg_catalog.default,
                created_date timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
                created_by int4,
                modified_count int4,
                last_modified_date timestamp(6),
                last_modified_by int4,
                is_deleted bool NOT NULL DEFAULT false,
                is_active bool NOT NULL DEFAULT true,
                deleted_date timestamp(6),
                deleted_by int4
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220610_073015_migrate_MHG1808_table_skriningnrs_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220610_073015_migrate_MHG1808_table_skriningnrs_m cannot be reverted.\n";

        return false;
    }
    */
}
