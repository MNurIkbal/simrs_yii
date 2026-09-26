<?php

use yii\db\Migration;

/**
 * Class m250213_040414_migrate_or_6_laporankunjunganpenunjang_v
 */
class m250213_040414_migrate_or_6_laporankunjunganpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS laporankunjunganpenunjang_v");
        $laporankunjunganpenunjang_v = file_get_contents(__DIR__ . '/definitions/laporankunjunganpenunjang_v.sql');
        $this->execute($laporankunjunganpenunjang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250213_040414_migrate_or_6_laporankunjunganpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250213_040414_migrate_or_6_laporankunjunganpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
