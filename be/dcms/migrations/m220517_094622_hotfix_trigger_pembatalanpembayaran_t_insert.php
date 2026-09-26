<?php

use yii\db\Migration;

/**
 * Class m220517_094622_hotfix_trigger_pembatalanpembayaran_t_insert
 */
class m220517_094622_hotfix_trigger_pembatalanpembayaran_t_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TRIGGER IF EXISTS "pembatalanpembayaran_t_insert" ON "public"."pembayaran_t";
        ');

        $this->execute('
            CREATE TRIGGER "pembatalanpembayaran_t_insert" AFTER UPDATE OF "is_deleted" ON "public"."pembayaran_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."pembatalanpembayaran_t_insert"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220517_094622_hotfix_trigger_pembatalanpembayaran_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220517_094622_hotfix_trigger_pembatalanpembayaran_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
