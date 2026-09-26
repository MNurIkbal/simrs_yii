<?php

use yii\db\Migration;

/**
 * Class m240430_024807_migrate_dsv1191_dokumeneklaim_v
 */
class m240430_024807_migrate_dsv1191_dokumeneklaim_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS dokumeneklaim_v");
        $dokumeneklaim_v = file_get_contents(__DIR__ . '/definitions/dokumeneklaim_v.sql');
        $this->execute($dokumeneklaim_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240430_024807_migrate_dsv1191_dokumeneklaim_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240430_024807_migrate_dsv1191_dokumeneklaim_v cannot be reverted.\n";

        return false;
    }
    */
}
