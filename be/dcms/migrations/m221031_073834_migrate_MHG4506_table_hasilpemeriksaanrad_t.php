<?php

use yii\db\Migration;

/**
 * Class m221031_073834_migrate_MHG4506_table_hasilpemeriksaanrad_t
 */
class m221031_073834_migrate_MHG4506_table_hasilpemeriksaanrad_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE hasilpemeriksaanrad_t ADD IF NOT EXISTS is_read BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221031_073834_migrate_MHG4506_table_hasilpemeriksaanrad_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221031_073834_migrate_MHG4506_table_hasilpemeriksaanrad_t cannot be reverted.\n";

        return false;
    }
    */
}
