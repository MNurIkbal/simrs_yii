<?php

use yii\db\Migration;

/**
 * Class m231127_062944_alter_klasifikasikamar_m_applicares
 */
class m231127_062944_alter_klasifikasikamar_m_applicares extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE public.klasifikasikamar_m ADD IF NOT EXISTS kodekelas_aplicare varchar(50) NULL;
        ");

        $this->execute("
            ALTER TABLE public.klasifikasikamar_m ADD IF NOT EXISTS namakelas_aplicare varchar(50) NULL;
        ");

        $this->execute("
            ALTER TABLE public.klasifikasikamar_m ADD IF NOT EXISTS kodett_rsonline varchar(50) NULL;
        ");

        $this->execute("
            ALTER TABLE public.klasifikasikamar_m ADD IF NOT EXISTS namatt_rsonline varchar(50) NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231127_062944_alter_klasifikasikamar_m_applicares cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_062944_alter_klasifikasikamar_m_applicares cannot be reverted.\n";

        return false;
    }
    */
}
