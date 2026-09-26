<?php

use yii\db\Migration;

/**
 * Class m241121_073710_migrate_rpp19233_rencanakontrol_t
 */
class m241121_073710_migrate_rpp19233_rencanakontrol_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.rencanakontrol_t ADD IF NOT EXISTS konsulpoli_id int4 NULL;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241121_073710_migrate_rpp19233_rencanakontrol_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241121_073710_migrate_rpp19233_rencanakontrol_t cannot be reverted.\n";

        return false;
    }
    */
}
