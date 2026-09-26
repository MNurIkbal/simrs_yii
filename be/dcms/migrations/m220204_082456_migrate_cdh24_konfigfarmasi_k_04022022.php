<?php

use yii\db\Migration;

/**
 * Class m220204_082456_migrate_cdh24_konfigfarmasi_k_04022022
 */
class m220204_082456_migrate_cdh24_konfigfarmasi_k_04022022 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN IF NOT EXISTS "konfig_carabayar_reseptur" text;');
		
		$this->execute('UPDATE konfigfarmasi_k set konfig_carabayar_reseptur = \'{"status": false,"carabayar": ["2","6"]}\' where konfigfarmasi_id = 1;');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220204_082456_migrate_cdh24_konfigfarmasi_k_04022022 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220204_082456_migrate_cdh24_konfigfarmasi_k_04022022 cannot be reverted.\n";

        return false;
    }
    */
}
