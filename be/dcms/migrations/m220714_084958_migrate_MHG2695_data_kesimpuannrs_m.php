<?php

use yii\db\Migration;

/**
 * Class m220714_084958_migrate_MHG2695_data_kesimpuannrs_m
 */
class m220714_084958_migrate_MHG2695_data_kesimpuannrs_m extends Migration
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
            INSERT INTO "kesimpuannrs_m" ("skor_awal", "skor_akhir", "keterangan", "is_anak") VALUES 
            (0, 3, \'Pasien tidak beresiko malnutrisi\', \'f\'),
            (3, 99, \'Pasien memiliki resiko malnutrisi, perlu perencanaan gizi dini\', \'f\'),
            (0, 3, \'Tidak bersiko\', \'t\'),
            (3, 6, \'Beresiko sedang\', \'t\'),
            (6, 99, \'Beresiko tinggi\', \'t\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220714_084958_migrate_MHG2695_data_kesimpuannrs_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220714_084958_migrate_MHG2695_data_kesimpuannrs_m cannot be reverted.\n";

        return false;
    }
    */
}
