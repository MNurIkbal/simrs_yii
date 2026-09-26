<?php

use yii\db\Migration;

/**
 * Class m211227_073316_migrate_konfigfarmasi_k_is_disabledfulfilled_so
 */
class m211227_073316_migrate_konfigfarmasi_k_is_disabledfulfilled_so extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD is_disabledfulfilled_so bool NOT NULL DEFAULT false;;');
		
		$this->execute('COMMENT ON COLUMN public.konfigfarmasi_k.is_disabledfulfilled_so IS \'disabled ketika selisih sudah 0\' ;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211227_073316_migrate_konfigfarmasi_k_is_disabledfulfilled_so cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211227_073316_migrate_konfigfarmasi_k_is_disabledfulfilled_so cannot be reverted.\n";

        return false;
    }
    */
}
