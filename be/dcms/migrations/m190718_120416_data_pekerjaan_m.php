<?php

use yii\db\Migration;

/**
 * Class m190718_120416_data_pekerjaan_m
 */
class m190718_120416_data_pekerjaan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
          UPDATE pekerjaan_m SET is_active=false where pekerjaan_id in (3,4,5,8,9,12,13,14,15,16,17,18);
        ');

          $this->execute('
          UPDATE pekerjaan_m SET 
                pekerjaan_nama=\'Lainnya\',
                pekerjaan_namalainnya=\'Lainnya\'
                where pekerjaan_id =6
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190718_120416_data_pekerjaan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190718_120416_data_pekerjaan_m cannot be reverted.\n";

        return false;
    }
    */
}
