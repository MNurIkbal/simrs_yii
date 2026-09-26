<?php

use yii\db\Migration;

/**
 * Class m251115_061842_migration_dsv_2163_master_upt
 */
class m251115_061842_migration_dsv_2163_master_upt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF exists unitpelaksanateknis_rm;');
        $this->execute("CREATE TABLE 
                public.unitpelaksanateknis_rm (
                upt_id int4 NOT NULL,
                upt_sync_id int4 NOT NULL,
                upt_nama varchar NOT NULL,
                created_date timestamp(6) DEFAULT 'now'::text::date NOT NULL,
                created_by int4 NULL,
                modified_count int4 NULL,
                last_modified_date timestamp(6) NULL,
                last_modified_by int4 NULL,
                is_deleted bool DEFAULT false NOT NULL,
                is_active bool DEFAULT true NOT NULL,
                deleted_date timestamp(6) NULL,
                deleted_by int4 NULL,
                CONSTRAINT unitpelaksanateknis_rm_pk PRIMARY KEY (upt_id)
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251115_061842_migration_dsv_2163_master_upt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251115_061842_migration_dsv_2163_master_upt cannot be reverted.\n";

        return false;
    }
    */
}
