<?php

use yii\db\Migration;

/**
 * Class m241212_073053_migrate_SKM_808_satusehat_loinc_tindakan_v
 */
class m241212_073053_migrate_SKM_808_satusehat_loinc_tindakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS satusehat_loinc_tindakan_v;");
        $satusehat_loinc_tindakan_v = file_get_contents(__DIR__ . '/definitions/satusehat_loinc_tindakan_v.sql');
        $this->execute($satusehat_loinc_tindakan_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_073053_migrate_SKM_808_satusehat_loinc_tindakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_073053_migrate_SKM_808_satusehat_loinc_tindakan_v cannot be reverted.\n";

        return false;
    }
    */
}
