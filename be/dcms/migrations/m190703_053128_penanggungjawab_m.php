<?php

use yii\db\Migration;

/**
 * Class m190703_053128_penanggungjawab_m
 */
class m190703_053128_penanggungjawab_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
            truncate table penanggungjawab_m restart identity;
        ');

        $this->execute('
  ALTER TABLE "public"."penanggungjawab_m" 
  ALTER COLUMN "pengantar" SET NOT NULL,
  ALTER COLUMN "penanggungjawab_nama" SET NOT NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190703_053128_penanggungjawab_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190703_053128_penanggungjawab_m cannot be reverted.\n";

        return false;
    }
    */
}
