<?php

use yii\db\Migration;

/**
 * Class m231127_153443_alter_sy_koreksidiagnosa
 */
class m231127_153443_alter_sy_koreksidiagnosa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE public.sy_koreksidiagnosa ADD IF NOT EXISTS is_inagrouper bool NULL DEFAULT false;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231127_153443_alter_sy_koreksidiagnosa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_153443_alter_sy_koreksidiagnosa cannot be reverted.\n";

        return false;
    }
    */
}
