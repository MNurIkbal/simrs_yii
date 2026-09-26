<?php

use yii\db\Migration;

/**
 * Class m221223_155654_migrate_gb_365_add_column_pada_programterapi_t_and_programterapi_t
 */
class m221223_155654_migrate_gb_365_add_column_pada_programterapi_t_and_programterapi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE programterapi_t ADD IF NOT EXISTS a_diag_penyerta json;
        ');
		
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221223_155654_migrate_gb_365_add_column_pada_programterapi_t_and_programterapi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221223_155654_migrate_gb_365_add_column_pada_programterapi_t_and_programterapi_t cannot be reverted.\n";

        return false;
    }
    */
}
