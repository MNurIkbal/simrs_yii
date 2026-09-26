<?php

use yii\db\Migration;

/**
 * Class m231025_154314_antrianjkn_jenispasien
 */
class m231025_154314_antrianjkn_jenispasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS antrianjkn_v");
        $antrianjkn_v = file_get_contents(__DIR__ . '/definitions/antrianjkn_v.sql');
        $this->execute($antrianjkn_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231025_154314_antrianjkn_jenispasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231025_154314_antrianjkn_jenispasien cannot be reverted.\n";

        return false;
    }
    */
}
