<?php

use yii\db\Migration;

/**
 * Class m190719_092731_komponentarif_m
 */
class m190719_092731_komponentarif_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         ALTER TABLE "public"."komponentarif_m" 
  ALTER COLUMN "last_modified_date" DROP DEFAULT;
        ');

        $this->execute('
         ALTER TABLE "public"."komponentarif_m" 
  ALTER COLUMN "deleted_date" DROP DEFAULT;
        ');

      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190719_092731_komponentarif_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190719_092731_komponentarif_m cannot be reverted.\n";

        return false;
    }
    */
}
