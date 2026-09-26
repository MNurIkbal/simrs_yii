<?php

use yii\db\Migration;

/**
 * Class m250114_075058_migrate_dsv_1635_alter_column_log_asuransitransaksi_t
 */
class m250114_075058_migrate_dsv_1635_alter_column_log_asuransitransaksi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.log_asuransitransaksi_t ADD IF NOT EXISTS response_pengesahan text NULL;");   
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250114_075058_migrate_dsv_1635_alter_column_log_asuransitransaksi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250114_075058_migrate_dsv_1635_alter_column_log_asuransitransaksi_t cannot be reverted.\n";

        return false;
    }
    */
}
