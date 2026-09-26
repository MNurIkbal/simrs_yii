<?php

use yii\db\Migration;

/**
 * Class m231220_131614_hotfix_MHG5425_satusehat_integrasi_t
 */
class m231220_131614_hotfix_MHG5425_satusehat_integrasi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE public.satusehat_integrasi_t ADD IF NOT EXISTS tgl_resend timestamp(6) NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231220_131614_hotfix_MHG5425_satusehat_integrasi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231220_131614_hotfix_MHG5425_satusehat_integrasi_t cannot be reverted.\n";

        return false;
    }
    */
}
