<?php

use yii\db\Migration;

/**
 * Class m240619_063245_migrate_app_diskon_table_carabayar_m
 */
class m240619_063245_migrate_app_diskon_table_carabayar_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           ALTER TABLE carabayar_m ADD IF NOT EXISTS limit_diskon float4;
       ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240619_063245_migrate_app_diskon_table_carabayar_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240619_063245_migrate_app_diskon_table_carabayar_m cannot be reverted.\n";

        return false;
    }
    */
}
