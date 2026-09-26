<?php

use yii\db\Migration;

/**
 * Class m251113_042025_GLKJ85_alter_noLPLength_bpjs
 */
class m251113_042025_GLKJ85_alter_noLPLength_bpjs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."bpjs_t" ALTER COLUMN "no_lp_manual" TYPE varchar(100);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251113_042025_GLKJ85_alter_noLPLength_bpjs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251113_042025_GLKJ85_alter_noLPLength_bpjs cannot be reverted.\n";

        return false;
    }
    */
}
