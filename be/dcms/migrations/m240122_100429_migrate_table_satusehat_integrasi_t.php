<?php

use yii\db\Migration;

/**
 * Class m240122_100429_migrate_table_satusehat_integrasi_t
 */
class m240122_100429_migrate_table_satusehat_integrasi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.satusehat_integrasi_t ALTER COLUMN pendaftaran_id DROP NOT NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240122_100429_migrate_table_satusehat_integrasi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240122_100429_migrate_table_satusehat_integrasi_t cannot be reverted.\n";

        return false;
    }
    */
}
