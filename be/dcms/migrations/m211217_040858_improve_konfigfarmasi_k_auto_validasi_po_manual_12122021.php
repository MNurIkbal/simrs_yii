<?php

use yii\db\Migration;

/**
 * Class m211217_040858_improve_konfigfarmasi_k_auto_validasi_po_manual_12122021
 */
class m211217_040858_improve_konfigfarmasi_k_auto_validasi_po_manual_12122021 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		   $this->execute('ALTER TABLE "public"."konfigfarmasi_k"  ADD COLUMN IF NOT EXISTS auto_validasi_po_manual boolean NOT NULL DEFAULT FALSE;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211217_040858_improve_konfigfarmasi_k_auto_validasi_po_manual_12122021 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211217_040858_improve_konfigfarmasi_k_auto_validasi_po_manual_12122021 cannot be reverted.\n";

        return false;
    }
    */
}
