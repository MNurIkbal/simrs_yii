<?php

use yii\db\Migration;

/**
 * Class m240212_084454_migrate_mcu_prima_periksatubuh_t
 */
class m240212_084454_migrate_mcu_prima_periksatubuh_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.periksatubuh_t ADD IF NOT EXISTS pemeriksaanfisikmcu_id int4 NULL;");
        $this->execute("ALTER TABLE public.periksatubuh_t ADD IF NOT EXISTS jenis_pemeriksaanfisikmcu varchar(50) NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_084454_migrate_mcu_prima_periksatubuh_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240212_084454_migrate_mcu_prima_periksatubuh_t cannot be reverted.\n";

        return false;
    }
    */
}
