<?php

use yii\db\Migration;

/**
 * Class m231028_045933_rpp_774_autokonsul
 */
class m231028_045933_rpp_774_autokonsul extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE konfigsystem_k ADD COLUMN IF NOT EXISTS is_auto_approve_konsul bool NULL DEFAULT false");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231028_045933_rpp_774_autokonsul cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231028_045933_rpp_774_autokonsul cannot be reverted.\n";

        return false;
    }
    */
}
