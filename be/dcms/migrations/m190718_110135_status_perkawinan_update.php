<?php

use yii\db\Migration;

/**
 * Class m190718_110135_status_perkawinan_update
 */
class m190718_110135_status_perkawinan_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          UPDATE lookup_m SET lookup_type=\'null\',
                              lookup_name = \'-\',
                              lookup_value = \'-\',
                              is_deleted=FALSE
                               WHERE lookup_id in (304,305,306,307)
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190718_110135_status_perkawinan_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190718_110135_status_perkawinan_update cannot be reverted.\n";

        return false;
    }
    */
}
