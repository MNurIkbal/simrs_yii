<?php

use yii\db\Migration;

/**
 * Class m210701_074317_migrate_improve_jadwaldokter
 */
class m210701_074317_migrate_improve_jadwaldokter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."jadwaldokter_m" ADD COLUMN if not exists "is_loaddokter" bool DEFAULT false;');

        $this->execute('ALTER TABLE "public"."jadwaldokter_m" ADD COLUMN if not exists "jumlah_loaddokter" float4;');

           $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN if not exists "is_reservasi" bool DEFAULT false;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210701_074317_migrate_improve_jadwaldokter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210701_074317_migrate_improve_jadwaldokter cannot be reverted.\n";

        return false;
    }
    */
}
