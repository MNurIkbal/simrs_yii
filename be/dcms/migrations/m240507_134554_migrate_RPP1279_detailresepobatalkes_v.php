<?php

use yii\db\Migration;

/**
 * Class m240507_134554_migrate_RPP1279_detailresepobatalkes_v
 */
class m240507_134554_migrate_RPP1279_detailresepobatalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS detailresepobatalkes_v");
        $detailresepobatalkes_v = file_get_contents(__DIR__ . '/definitions/detailresepobatalkes_v.sql');
        $this->execute($detailresepobatalkes_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240507_134554_migrate_RPP1279_detailresepobatalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240507_134554_migrate_RPP1279_detailresepobatalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
