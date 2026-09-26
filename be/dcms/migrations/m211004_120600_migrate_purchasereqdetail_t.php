<?php

use yii\db\Migration;

/**
 * Class m211004_120600_migrate_purchasereqdetail_t
 */
class m211004_120600_migrate_purchasereqdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "last_7" numeric;');
        $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "last_14" numeric;');
        $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists  "last_30" numeric;');
        $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "qty_outstanding" numeric;');      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211004_120600_migrate_purchasereqdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211004_120600_migrate_purchasereqdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
