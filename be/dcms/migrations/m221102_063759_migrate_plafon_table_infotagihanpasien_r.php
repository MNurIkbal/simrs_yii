<?php

use yii\db\Migration;

/**
 * Class m221102_063759_migrate_plafon_table_infotagihanpasien_r
 */
class m221102_063759_migrate_plafon_table_infotagihanpasien_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE infotagihanpasien_r ADD IF NOT EXISTS "subPenjamin" text ;
        ');

        $this->execute('
            ALTER TABLE infotagihanpasien_r ADD IF NOT EXISTS dijamin_subpayer float8 ;
        ');

        $this->execute('
            ALTER TABLE infotagihanpasien_r ADD IF NOT EXISTS plafon_payer float8;
        ');

        $this->execute('
            ALTER TABLE infotagihanpasien_r ADD IF NOT EXISTS plafon_subpayer float8;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221102_063759_migrate_plafon_table_infotagihanpasien_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221102_063759_migrate_plafon_table_infotagihanpasien_r cannot be reverted.\n";

        return false;
    }
    */
}
