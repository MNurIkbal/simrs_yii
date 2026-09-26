<?php

use yii\db\Migration;

/**
 * Class m241212_072935_migrate_SKM_808_table_satusehat_loinc_m
 */
class m241212_072935_migrate_SKM_808_table_satusehat_loinc_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $satusehat_loinc_m = file_get_contents(__DIR__ . '/definitions/satusehat_loinc_m.sql');
        $this->execute($satusehat_loinc_m);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_072935_migrate_SKM_808_table_satusehat_loinc_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_072935_migrate_SKM_808_table_satusehat_loinc_m cannot be reverted.\n";

        return false;
    }
    */
}
