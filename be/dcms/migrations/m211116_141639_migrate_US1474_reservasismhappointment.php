<?php

use yii\db\Migration;

/**
 * Class m211116_141639_migrate_US1474_reservasismhappointment
 */
class m211116_141639_migrate_US1474_reservasismhappointment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."jadwaldokter_m" 
            ADD COLUMN IF NOT EXISTS "is_loaddokter" bool DEFAULT false,
            ADD COLUMN IF NOT EXISTS "jumlah_loaddokter" float4,
            ADD COLUMN IF NOT EXISTS "kuota_total" float4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211116_141639_migrate_US1474_reservasismhappointment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211116_141639_migrate_US1474_reservasismhappointment cannot be reverted.\n";

        return false;
    }
    */
}
