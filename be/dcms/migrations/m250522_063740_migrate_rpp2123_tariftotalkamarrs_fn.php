<?php

use yii\db\Migration;

/**
 * Class m250522_063740_migrate_rpp2123_tariftotalkamarrs_fn
 */
class m250522_063740_migrate_rpp2123_tariftotalkamarrs_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS public.tariftotalkamarrs_fn;');
        $tariftotalkamarrs_fn = file_get_contents(__DIR__ . '/definitions/tariftotalkamarrs_fn.sql');
        $this->execute($tariftotalkamarrs_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250522_063740_migrate_rpp2123_tariftotalkamarrs_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250522_063740_migrate_rpp2123_tariftotalkamarrs_fn cannot be reverted.\n";

        return false;
    }
    */
}
