<?php

use yii\db\Migration;

/**
 * Class m221102_063514_migrate_plafon_table_tindakanpelayanan_t
 */
class m221102_063514_migrate_plafon_table_tindakanpelayanan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE tindakanpelayanan_t ADD IF NOT EXISTS dijamin_payer float8;
        ');

        $this->execute('
            ALTER TABLE tindakanpelayanan_t ADD IF NOT EXISTS dijamin_subpayer float8;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221102_063514_migrate_plafon_table_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221102_063514_migrate_plafon_table_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }
    */
}
