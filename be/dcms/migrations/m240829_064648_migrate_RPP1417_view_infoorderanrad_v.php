<?php

use yii\db\Migration;

/**
 * Class m240829_064648_migrate_RPP1417_view_infoorderanrad_v
 */
class m240829_064648_migrate_RPP1417_view_infoorderanrad_v extends Migration
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
        echo "m240829_064648_migrate_RPP1417_view_infoorderanrad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240829_064648_migrate_RPP1417_view_infoorderanrad_v cannot be reverted.\n";

        return false;
    }
    */
}
