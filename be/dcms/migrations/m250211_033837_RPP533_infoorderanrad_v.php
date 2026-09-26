<?php

use yii\db\Migration;

/**
 * Class m250211_033837_RPP533_infoorderanrad_v
 */
class m250211_033837_RPP533_infoorderanrad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infoorderanrad_v");
        $infoorderanrad_v = file_get_contents(__DIR__ . '/definitions/infoorderanrad_v.sql');
        $this->execute($infoorderanrad_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250211_033837_RPP533_infoorderanrad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250211_033837_RPP533_infoorderanrad_v cannot be reverted.\n";

        return false;
    }
    */
}
