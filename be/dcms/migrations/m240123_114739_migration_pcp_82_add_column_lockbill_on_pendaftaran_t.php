<?php

use yii\db\Migration;

/**
 * Class m240123_114739_migration_pcp_82_add_column_lockbill_on_pendaftaran_t
 */
class m240123_114739_migration_pcp_82_add_column_lockbill_on_pendaftaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.pendaftaran_t ADD IF NOT EXISTS is_close_bill bool NULL DEFAULT false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_114739_migration_pcp_82_add_column_lockbill_on_pendaftaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_114739_migration_pcp_82_add_column_lockbill_on_pendaftaran_t cannot be reverted.\n";

        return false;
    }
    */
}
