<?php

use yii\db\Migration;

/**
 * Class m250812_071901_migrate_restriction_restriction_aksesobat_v
 */
class m250812_071901_migrate_restriction_restriction_aksesobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS restriction_aksesobat_v");
        $restriction_aksesobat_v = file_get_contents(__DIR__ . '/definitions/restriction_aksesobat_v.sql');
        $this->execute($restriction_aksesobat_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250812_071901_migrate_restriction_restriction_aksesobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250812_071901_migrate_restriction_restriction_aksesobat_v cannot be reverted.\n";

        return false;
    }
    */
}
