<?php

use yii\db\Migration;

/**
 * Class m220531_102450_migrate_MHG_1743_obatalkes_m_manufaktur_ids_supplier_ids
 */
class m220531_102450_migrate_MHG_1743_obatalkes_m_manufaktur_ids_supplier_ids extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('
		            ALTER TABLE "public"."obatalkes_m" ADD IF NOT EXISTS "supplier_ids" text;
		        ');
		$this->execute('
		            ALTER TABLE "public"."obatalkes_m" ADD IF NOT EXISTS "manufacture_ids" text;
		        ');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220531_102450_migrate_MHG_1743_obatalkes_m_manufaktur_ids_supplier_ids cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220531_102450_migrate_MHG_1743_obatalkes_m_manufaktur_ids_supplier_ids cannot be reverted.\n";

        return false;
    }
    */
}
