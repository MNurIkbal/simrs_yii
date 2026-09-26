<?php

use yii\db\Migration;

/**
 * Class m220830_080646_migrate_mhg_3522_altertable_validasipoobat_t_and_validasipobarang_t
 */
class m220830_080646_migrate_mhg_3522_altertable_validasipoobat_t_and_validasipobarang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE validasipoobat_t ADD IF NOT EXISTS is_cito bool DEFAULT false; ');
        $this->execute('ALTER  TABLE validasipoobat_t ADD IF NOT EXISTS is_admin bool DEFAULT false; ');
		
        $this->execute('ALTER TABLE validasipobarang_t ADD IF NOT EXISTS is_cito bool DEFAULT false; ');
        $this->execute('ALTER  TABLE validasipobarang_t ADD IF NOT EXISTS is_admin bool DEFAULT false; ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220830_080646_migrate_mhg_3522_altertable_validasipoobat_t_and_validasipobarang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220830_080646_migrate_mhg_3522_altertable_validasipoobat_t_and_validasipobarang_t cannot be reverted.\n";

        return false;
    }
    */
}
