<?php

use yii\db\Migration;

/**
 * Class m190809_033132_asesmenawal_t
 */
class m190809_033132_asesmenawal_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."asesmenawal_t" 
                ALTER COLUMN "diagnosa_masuk" TYPE text USING "diagnosa_masuk"::text;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190809_033132_asesmenawal_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190809_033132_asesmenawal_t cannot be reverted.\n";

        return false;
    }
    */
}
