<?php

use yii\db\Migration;

/**
 * Class m190508_064727_stokobatalkes_r_update
 */
class m190508_064727_stokobatalkes_r_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      ALTER TABLE "public"."stokobatalkes_r" 
  ALTER COLUMN "periodestokobat_id" DROP NOT NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190508_064727_stokobatalkes_r_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190508_064727_stokobatalkes_r_update cannot be reverted.\n";

        return false;
    }
    */
}
