<?php

use yii\db\Migration;

/**
 * Class m240122_031919_migrate_DSV1034_infodatakunjungan_v
 */
class m240122_031919_migrate_DSV1034_infodatakunjungan_v extends Migration
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
        echo "m240122_031919_migrate_DSV1034_infodatakunjungan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240122_031919_migrate_DSV1034_infodatakunjungan_v cannot be reverted.\n";

        return false;
    }
    */
}
