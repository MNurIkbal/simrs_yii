<?php

use yii\db\Migration;

/**
 * Class m250514_070001_migrate_rpp_2125_create_functions
 */
class m250514_070001_migrate_rpp_2125_create_functions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Insert Process
        $penjualanresep_calc_insert = file_get_contents(__DIR__ . '/definitions/penjualanresep_calc_insert.sql');
        $this->execute($penjualanresep_calc_insert);

        $obatalkespasien_calc_insert = file_get_contents(__DIR__ . '/definitions/obatalkespasien_calc_insert.sql');
        $this->execute($obatalkespasien_calc_insert);

        $resepturdetail_calc_insert = file_get_contents(__DIR__ . '/definitions/resepturdetail_calc_insert.sql');
        $this->execute($resepturdetail_calc_insert);

        // Update Process
        $penjualanresep_calc_update = file_get_contents(__DIR__ . '/definitions/penjualanresep_calc_update.sql');
        $this->execute($penjualanresep_calc_update);

        $reseptur_calc_update = file_get_contents(__DIR__ . '/definitions/reseptur_calc_update.sql');
        $this->execute($reseptur_calc_update);

        $resepturdetail_calc_update = file_get_contents(__DIR__ . '/definitions/resepturdetail_calc_update.sql');
        $this->execute($resepturdetail_calc_update);

        $obatalkespasien_calc_update = file_get_contents(__DIR__ . '/definitions/obatalkespasien_calc_update.sql');
        $this->execute($obatalkespasien_calc_update);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250514_070001_migrate_rpp_2125_create_functions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250514_070001_migrate_rpp_2125_create_functions cannot be reverted.\n";

        return false;
    }
    */
}
