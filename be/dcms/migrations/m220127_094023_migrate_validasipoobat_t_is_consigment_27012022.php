<?php

use yii\db\Migration;

/**
 * Class m220127_094023_migrate_validasipoobat_t_is_consigment_27012022
 */
class m220127_094023_migrate_validasipoobat_t_is_consigment_27012022 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE "public"."validasipoobat_t" ADD COLUMN if not exists "is_consigment" bool DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220127_094023_migrate_validasipoobat_t_is_consigment_27012022 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220127_094023_migrate_validasipoobat_t_is_consigment_27012022 cannot be reverted.\n";

        return false;
    }
    */
}
