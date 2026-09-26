<?php

use yii\db\Migration;

/**
 * Class m220511_055808_migrate_pasienmasukpenunjang_t
 */
class m220511_055808_migrate_pasienmasukpenunjang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pasienmasukpenunjang_t" 
                            ADD COLUMN if not exists "programterapi_id" int4;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220511_055808_migrate_pasienmasukpenunjang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_055808_migrate_pasienmasukpenunjang_t cannot be reverted.\n";

        return false;
    }
    */
}
