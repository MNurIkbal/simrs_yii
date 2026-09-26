<?php

use yii\db\Migration;

/**
 * Class m190807_235502_rujukan_t_modify
 */
class m190807_235502_rujukan_t_modify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
     ALTER TABLE "public"."rujukan_t" 
  ALTER COLUMN "created_date" DROP NOT NULL,
  ALTER COLUMN "last_modified_date" DROP NOT NULL,
  ALTER COLUMN "last_modified_date" DROP DEFAULT,
  ALTER COLUMN "deleted_date" DROP NOT NULL,
  ALTER COLUMN "deleted_date" DROP DEFAULT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190807_235502_rujukan_t_modify cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190807_235502_rujukan_t_modify cannot be reverted.\n";

        return false;
    }
    */
}
