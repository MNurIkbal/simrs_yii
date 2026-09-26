<?php

use yii\db\Migration;

/**
 * Class m221224_034156_alter_pasienkirimunitlain_add_diagnosa
 */
class m221224_034156_alter_pasienkirimunitlain_add_diagnosa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pasienkirimkeunitlain_t ADD COLUMN IF NOT EXISTS diag_utama json null;
        ');
        $this->execute('
            ALTER TABLE pasienkirimkeunitlain_t ADD COLUMN IF NOT EXISTS diag_penyerta json null;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221224_034156_alter_pasienkirimunitlain_add_diagnosa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221224_034156_alter_pasienkirimunitlain_add_diagnosa cannot be reverted.\n";

        return false;
    }
    */
}
