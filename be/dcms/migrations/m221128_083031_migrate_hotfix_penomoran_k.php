<?php

use yii\db\Migration;

/**
 * Class m221128_083031_migrate_hotfix_penomoran_k
 */
class m221128_083031_migrate_hotfix_penomoran_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM penomoran_k WHERE penomoran_id = 193;");
        $this->execute("DELETE FROM penomoran_k WHERE penomoran_id = 194;");
        $this->execute("INSERT INTO public.penomoran_k
        (penomoran_id, penomoran_nama, prefix)
        VALUES(193, 'Masuk Penunjang FISIO', 'FIS');");
        $this->execute("INSERT INTO public.penomoran_k
        (penomoran_id, penomoran_nama, prefix)
        VALUES(194, 'konfig_antrian', 'FIS');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221128_083031_migrate_hotfix_penomoran_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221128_083031_migrate_hotfix_penomoran_k cannot be reverted.\n";

        return false;
    }
    */
}
