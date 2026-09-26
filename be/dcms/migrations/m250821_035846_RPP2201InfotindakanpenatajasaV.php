<?php

use yii\db\Migration;

/**
 * Class m250821_035846_RPP2201InfotindakanpenatajasaV
 */
class m250821_035846_RPP2201InfotindakanpenatajasaV extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS infotindakanpenatajasa_v');

        $infotindakanpenatajasa_v = file_get_contents(__DIR__ . '/definitions/infotindakanpenatajasa_v.sql');
        $this->execute($infotindakanpenatajasa_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250821_035846_RPP2201InfotindakanpenatajasaV cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250821_035846_RPP2201InfotindakanpenatajasaV cannot be reverted.\n";

        return false;
    }
    */
}
