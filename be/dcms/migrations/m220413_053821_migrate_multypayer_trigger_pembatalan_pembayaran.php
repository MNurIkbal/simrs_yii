<?php

use yii\db\Migration;

/**
 * Class m220413_053821_migrate_multypayer_trigger_pembatalan_pembayaran
 */
class m220413_053821_migrate_multypayer_trigger_pembatalan_pembayaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP TRIGGER if exists "tandabuktikeluar_pembatalanpembayaran_insert" ON "public"."pembatalanpembayaran_t";
        ');

        $this->execute('
            CREATE TRIGGER "tandabuktikeluar_pembatalanpembayaran_insert" AFTER INSERT ON "public"."pembatalanpembayaran_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."tandabuktikeluar_pembatalanpembayaran_insert"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220413_053821_migrate_multypayer_trigger_pembatalan_pembayaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220413_053821_migrate_multypayer_trigger_pembatalan_pembayaran cannot be reverted.\n";

        return false;
    }
    */
}
