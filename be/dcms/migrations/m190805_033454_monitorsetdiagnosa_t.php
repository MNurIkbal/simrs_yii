<?php

use yii\db\Migration;

/**
 * Class m190805_033454_monitorsetdiagnosa_t
 */
class m190805_033454_monitorsetdiagnosa_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
        ALTER TABLE "public"."monitorsetdiagnosa_t" 
  ADD COLUMN "is_dokter" bool;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190805_033454_monitorsetdiagnosa_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190805_033454_monitorsetdiagnosa_t cannot be reverted.\n";

        return false;
    }
    */
}
