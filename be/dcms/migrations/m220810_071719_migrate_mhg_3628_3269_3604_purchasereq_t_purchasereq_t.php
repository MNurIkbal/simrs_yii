<?php

use yii\db\Migration;

/**
 * Class m220810_071719_migrate_mhg_3628_3269_3604_purchasereq_t_purchasereq_t
 */
class m220810_071719_migrate_mhg_3628_3269_3604_purchasereq_t_purchasereq_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."purchasereq_t" ADD IF NOT EXISTS "is_admin" bool DEFAULT false;
        ');
		
        $this->execute('
            ALTER TABLE "public"."purchasereqbrg_t" ADD IF NOT EXISTS "is_admin" bool DEFAULT false;
        ');
		
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220810_071719_migrate_mhg_3628_3269_3604_purchasereq_t_purchasereq_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220810_071719_migrate_mhg_3628_3269_3604_purchasereq_t_purchasereq_t cannot be reverted.\n";

        return false;
    }
    */
}
