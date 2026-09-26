<?php

use yii\db\Migration;

/**
 * Class m220927_071201_migrate_MHG3788_table_permintaankepenunjang_t
 */
class m220927_071201_migrate_MHG3788_table_permintaankepenunjang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE permintaankepenunjang_t ADD IF NOT EXISTS "is_exception" bool DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220927_071201_migrate_MHG3788_table_permintaankepenunjang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220927_071201_migrate_MHG3788_table_permintaankepenunjang_t cannot be reverted.\n";

        return false;
    }
    */
}
