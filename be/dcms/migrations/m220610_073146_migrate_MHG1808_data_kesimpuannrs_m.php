<?php

use yii\db\Migration;

/**
 * Class m220610_073146_migrate_MHG1808_data_kesimpuannrs_m
 */
class m220610_073146_migrate_MHG1808_data_kesimpuannrs_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE kesimpuannrs_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO kesimpuannrs_m ("skor_awal", "skor_akhir", "keterangan") VALUES (0, 3, \'Pasien tidak beresiko malnutrisi\'),
            (3, 99, \'Pasien memiliki resiko malnutrisi, perlu perencanaan gizi dini\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220610_073146_migrate_MHG1808_data_kesimpuannrs_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220610_073146_migrate_MHG1808_data_kesimpuannrs_m cannot be reverted.\n";

        return false;
    }
    */
}
