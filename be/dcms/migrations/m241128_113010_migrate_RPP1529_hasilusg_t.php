<?php

use yii\db\Migration;

/**
 * Class m241128_113010_migrate_RPP1529_hasilusg_t
 */
class m241128_113010_migrate_RPP1529_hasilusg_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.hasilusg_t ADD IF NOT EXISTS hasil_pemeriksaan_id int4 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241128_113010_migrate_RPP1529_hasilusg_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241128_113010_migrate_RPP1529_hasilusg_t cannot be reverted.\n";

        return false;
    }
    */
}
