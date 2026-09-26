<?php

use yii\db\Migration;

/**
 * Class m251024_085629_migrate_SBAR_sbar_t
 */
class m251024_085629_migrate_SBAR_sbar_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TABLE IF EXISTS sbar_t CASCADE;');
        $sbar_t = file_get_contents(__DIR__ . '/definitions/sbar_t.sql');
        $this->execute($sbar_t);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251024_085629_migrate_SBAR_sbar_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251024_085629_migrate_SBAR_sbar_t cannot be reverted.\n";

        return false;
    }
    */
}
