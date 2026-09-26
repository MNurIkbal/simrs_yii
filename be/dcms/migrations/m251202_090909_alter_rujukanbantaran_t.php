<?php

use yii\db\Migration;

/**
 * Class m251202_090909_alter_rujukanbantaran_t
 */
class m251202_090909_alter_rujukanbantaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.rujukanbantaran_t ADD COLUMN IF NOT EXISTS no_tahanan varchar(150);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251202_090909_alter_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251202_090909_alter_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }
    */
}
