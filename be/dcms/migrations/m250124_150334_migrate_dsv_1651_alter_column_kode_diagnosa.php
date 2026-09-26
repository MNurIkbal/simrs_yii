<?php

use yii\db\Migration;

/**
 * Class m250124_150334_migrate_dsv_1651_alter_column_kode_diagnosa
 */
class m250124_150334_migrate_dsv_1651_alter_column_kode_diagnosa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.log_asuransitransaksi_t ADD IF NOT EXISTS kode_diagnosa varchar NULL;");   
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250124_150334_migrate_dsv_1651_alter_column_kode_diagnosa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250124_150334_migrate_dsv_1651_alter_column_kode_diagnosa cannot be reverted.\n";

        return false;
    }
    */
}
