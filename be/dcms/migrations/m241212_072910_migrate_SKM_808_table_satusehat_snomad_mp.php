<?php

use yii\db\Migration;

/**
 * Class m241212_072910_migrate_SKM_808_table_satusehat_snomad_mp
 */
class m241212_072910_migrate_SKM_808_table_satusehat_snomad_mp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $satusehat_snomad_mp = file_get_contents(__DIR__ . '/definitions/satusehat_snomad_mp.sql');
        $this->execute($satusehat_snomad_mp);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_072910_migrate_SKM_808_table_satusehat_snomad_mp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_072910_migrate_SKM_808_table_satusehat_snomad_mp cannot be reverted.\n";

        return false;
    }
    */
}
