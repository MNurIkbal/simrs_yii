<?php

use yii\db\Migration;

/**
 * Class m250305_042508_migrate_dsv_1688_penambahan_column
 */
class m250305_042508_migrate_dsv_1688_penambahan_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.pendaftaranol_t ADD IF NOT EXISTS benefit_code varchar NULL;");
        $this->execute("ALTER TABLE public.pendaftaranol_t ADD IF NOT EXISTS transaction_id varchar NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250305_042508_migrate_dsv_1688_penambahan_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250305_042508_migrate_dsv_1688_penambahan_column cannot be reverted.\n";

        return false;
    }
    */
}
