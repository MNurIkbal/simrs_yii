<?php

use yii\db\Migration;

/**
 * Class m211213_031407_migrate_hotfix_unique_no_pr_13122021
 */
class m211213_031407_migrate_hotfix_unique_no_pr_13122021 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
  	   $this->execute('ALTER TABLE "public"."purchasereq_t" DROP CONSTRAINT IF EXISTS "unique_no_probat";');
	   
 	   $this->execute('ALTER TABLE "public"."purchasereq_t" ADD CONSTRAINT unique_no_probat UNIQUE ("no_pr");');
	   
 	   $this->execute('ALTER TABLE "public"."purchasereqbrg_t" DROP CONSTRAINT IF EXISTS "unique_no_prbrg";');
	   
	   $this->execute('ALTER TABLE "public"."purchasereqbrg_t" ADD CONSTRAINT unique_no_prbrg UNIQUE ("no_pr");');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211213_031407_migrate_hotfix_unique_no_pr_13122021 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211213_031407_migrate_hotfix_unique_no_pr_13122021 cannot be reverted.\n";

        return false;
    }
    */
}
