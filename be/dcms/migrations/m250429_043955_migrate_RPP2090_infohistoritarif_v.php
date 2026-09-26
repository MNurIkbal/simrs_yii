<?php

use yii\db\Migration;

/**
 * Class m250429_043955_migrate_RPP2090_infohistoritarif_v
 */
class m250429_043955_migrate_RPP2090_infohistoritarif_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS public.infohistoritarif_v;');
        $infohistoritarif_v = file_get_contents(__DIR__ . '/definitions/infohistoritarif_v.sql');
        $this->execute($infohistoritarif_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250429_043955_migrate_RPP2090_infohistoritarif_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250429_043955_migrate_RPP2090_infohistoritarif_v cannot be reverted.\n";

        return false;
    }
    */
}
