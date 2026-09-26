<?php

use yii\db\Migration;

/**
 * Class m250829_024806_migrate_dsv1980_infodatakunjungan_v
 */
class m250829_024806_migrate_dsv1980_infodatakunjungan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infodatakunjungan_v");
        $infodatakunjungan_v = file_get_contents(__DIR__ . '/definitions/infodatakunjungan_v.sql');
        $this->execute($infodatakunjungan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250829_024806_migrate_dsv1980_infodatakunjungan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250829_024806_migrate_dsv1980_infodatakunjungan_v cannot be reverted.\n";

        return false;
    }
    */
}
