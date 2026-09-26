<?php

use yii\db\Migration;

/**
 * Class m221027_122010_add_title_in_report_t
 */
class m221027_122010_add_title_in_report_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."report_t" 
            ADD COLUMN IF NOT EXISTS "title" varchar(200);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221027_122010_add_title_in_report_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221027_122010_add_title_in_report_t cannot be reverted.\n";

        return false;
    }
    */
}
