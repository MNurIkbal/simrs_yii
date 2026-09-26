<?php

use yii\db\Migration;

/**
 * Class m221226_102209_migrate_gb_483_konfigsystem_k_is_reset_edit_tagihan
 */
class m221226_102209_migrate_gb_483_konfigsystem_k_is_reset_edit_tagihan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigsystem_k ADD IF NOT EXISTS reset_edit_tagihan bool default FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221226_102209_migrate_gb_483_konfigsystem_k_is_reset_edit_tagihan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221226_102209_migrate_gb_483_konfigsystem_k_is_reset_edit_tagihan cannot be reverted.\n";

        return false;
    }
    */
}
