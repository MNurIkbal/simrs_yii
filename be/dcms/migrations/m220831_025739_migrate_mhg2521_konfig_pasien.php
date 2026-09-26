<?php

use yii\db\Migration;

/**
 * Class m220831_025739_migrate_mhg2521_konfig_pasien
 */
class m220831_025739_migrate_mhg2521_konfig_pasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigtarif_k ADD IF NOT EXISTS "is_diskon_pasien" bool DEFAULT TRUE;
        ');
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220831_025739_migrate_mhg2521_konfig_pasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220831_025739_migrate_mhg2521_konfig_pasien cannot be reverted.\n";

        return false;
    }
    */
}
