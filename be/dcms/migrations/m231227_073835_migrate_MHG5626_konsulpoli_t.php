<?php

use yii\db\Migration;

/**
 * Class m231227_073835_migrate_MHG5626_konsulpoli_t
 */
class m231227_073835_migrate_MHG5626_konsulpoli_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.konsulpoli_t ADD IF NOT EXISTS tgl_setujui timestamp NULL;");
        $this->execute("ALTER TABLE public.konsulpoli_t ADD IF NOT EXISTS tgl_masukperiksa timestamp NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231227_073835_migrate_MHG5626_konsulpoli_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231227_073835_migrate_MHG5626_konsulpoli_t cannot be reverted.\n";

        return false;
    }
    */
}
