<?php

use yii\db\Migration;

/**
 * Class m220531_102539_migrate_MHG_1743_konfigfarmasi_k_manufaktur_ids_supplier_ids
 */
class m220531_102539_migrate_MHG_1743_konfigfarmasi_k_manufaktur_ids_supplier_ids extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->execute('
	               ALTER TABLE "public"."konfigfarmasi_k" ADD IF NOT EXISTS "is_mutiple_manufaktur" bool Default false;
	           ');
	    $this->execute('
	               ALTER TABLE "public"."konfigfarmasi_k" ADD IF NOT EXISTS "is_multiple_supplier" bool Default false;
	           ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220531_102539_migrate_MHG_1743_konfigfarmasi_k_manufaktur_ids_supplier_ids cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220531_102539_migrate_MHG_1743_konfigfarmasi_k_manufaktur_ids_supplier_ids cannot be reverted.\n";

        return false;
    }
    */
}
