<?php

use yii\db\Migration;

/**
 * Class m211105_141952_improvment_master_signa_alias_US2108
 */
class m211105_141952_improvment_master_signa_alias_US2108 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE signaobat_m ADD IF NOT EXISTS "alias" VARCHAR(30);
        ');

        $this->execute('
            UPDATE signaobat_m
            SET "alias" = TRIM(REPLACE(signa_kode, \'-\', \'\'));
        ');

        $this->execute('
            UPDATE signaobat_m
            SET "alias" = TRIM(REPLACE("alias", \'.\', \'\'));
        ');

        $this->execute('
            UPDATE signaobat_m
            SET "alias" = TRIM(REPLACE("alias", \' \', \'\'));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211105_141952_improvment_master_signa_alias_US2108 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211105_141952_improvment_master_signa_alias_US2108 cannot be reverted.\n";

        return false;
    }
    */
}
