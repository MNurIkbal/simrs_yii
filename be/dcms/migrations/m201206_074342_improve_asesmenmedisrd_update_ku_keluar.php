<?php

use yii\db\Migration;

/**
 * Class m201206_074342_improve_asesmenmedisrd_update_ku_keluar
 */
class m201206_074342_improve_asesmenmedisrd_update_ku_keluar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."asesmenmedisrd_t" ALTER COLUMN "ku_keluar" TYPE text USING "ku_keluar"::text;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201206_074342_improve_asesmenmedisrd_update_ku_keluar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201206_074342_improve_asesmenmedisrd_update_ku_keluar cannot be reverted.\n";

        return false;
    }
    */
}
