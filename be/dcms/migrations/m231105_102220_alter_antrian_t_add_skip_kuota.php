<?php

use yii\db\Migration;

/**
 * Class m231105_102220_alter_antrian_t_add_skip_kuota
 */
class m231105_102220_alter_antrian_t_add_skip_kuota extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE antrian_t ADD COLUMN IF NOT EXISTS skip_kuota bool NULL DEFAULT false;");
        
        $upd_stokkuotadokter_from_antrian_try = file_get_contents(__DIR__ . '/definitions/upd_stokkuotadokter_from_antrian_try.fn.sql');
        $this->execute($upd_stokkuotadokter_from_antrian_try);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231105_102220_alter_antrian_t_add_skip_kuota cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231105_102220_alter_antrian_t_add_skip_kuota cannot be reverted.\n";

        return false;
    }
    */
}
