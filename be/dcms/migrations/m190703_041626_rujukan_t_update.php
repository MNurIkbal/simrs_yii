<?php

use yii\db\Migration;

/**
 * Class m190703_041626_rujukan_t_update
 */
class m190703_041626_rujukan_t_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
          $this->execute('
   ALTER TABLE "public"."rujukan_t" 
  ALTER COLUMN "no_rujukan" DROP NOT NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190703_041626_rujukan_t_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190703_041626_rujukan_t_update cannot be reverted.\n";

        return false;
    }
    */
}
