<?php

use yii\db\Migration;

/**
 * Class m220331_160852_migrate_DHC308_table_verfikasibedah_r
 */
class m220331_160852_migrate_DHC308_table_verfikasibedah_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE verifikasibedah_r ADD IF NOT EXISTS kegiatanoperasi_id int4;
        ');

        $this->execute('
            ALTER TABLE verifikasibedah_r ADD IF NOT EXISTS golonganoperasi_id int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220331_160852_migrate_DHC308_table_verfikasibedah_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220331_160852_migrate_DHC308_table_verfikasibedah_r cannot be reverted.\n";

        return false;
    }
    */
}
