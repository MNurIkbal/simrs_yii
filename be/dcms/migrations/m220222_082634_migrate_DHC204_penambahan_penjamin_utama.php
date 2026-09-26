<?php

use yii\db\Migration;

/**
 * Class m220222_082634_migrate_DHC204_penambahan_penjamin_utama
 */
class m220222_082634_migrate_DHC204_penambahan_penjamin_utama extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."pembayaranpelayanan_t" ADD IF NOT EXISTS "is_penjaminutama" bool DEFAULT false;
        ');

        $this->execute('
            ALTER TABLE "public"."tindakanpelayanan_r" ADD IF NOT EXISTS "is_penjaminutama" bool;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220222_082634_migrate_DHC204_penambahan_penjamin_utama cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220222_082634_migrate_DHC204_penambahan_penjamin_utama cannot be reverted.\n";

        return false;
    }
    */
}
