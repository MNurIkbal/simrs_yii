<?php

use yii\db\Migration;

/**
 * Class m210115_061130_migrate_20200115_pesanbarangdetail_t
 */
class m210115_061130_migrate_20200115_pesanbarangdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."pesanbarangdetail_t" DISABLE TRIGGER "tigger_update_pesanbarangdetail_t";');
         $this->execute('ALTER TABLE "public"."pesanbarangdetail_t" DISABLE TRIGGER "trigger_delete_pemesanan";');
         $this->execute('ALTER TABLE "public"."pesanbarangdetail_t" DISABLE TRIGGER "trigger_delete_pesanbarangdetail_t";');
         $this->execute('ALTER TABLE "public"."pesanbarangdetail_t" DISABLE TRIGGER "trigger_insert_pesanbarangdetail_t";');
         $this->execute('ALTER TABLE "public"."pesanbarangdetail_t" DISABLE TRIGGER "trigger_update_pemesanan";');
    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210115_061130_migrate_20200115_pesanbarangdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210115_061130_migrate_20200115_pesanbarangdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
