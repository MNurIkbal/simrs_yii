<?php

use yii\db\Migration;

/**
 * Class m231127_220129_migrate_kamar_applicare
 */
class m231127_062840_migrate_kamar_applicare extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE public.kamarruangan_m ADD IF NOT EXISTS id_t_tt_rsonline int4 NULL;
        ");

        $this->execute("
            ALTER TABLE public.kamarruangan_m ADD IF NOT EXISTS kodekelas_aplicare varchar(50) NULL;
        ");

        $this->execute("
            ALTER TABLE public.kamarruangan_m ADD IF NOT EXISTS namakelas_aplicare varchar(100) NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231127_220129_migrate_kamar_applicare cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_220129_migrate_kamar_applicare cannot be reverted.\n";

        return false;
    }
    */
}
