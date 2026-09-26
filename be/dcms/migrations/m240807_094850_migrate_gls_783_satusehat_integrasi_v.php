<?php

use yii\db\Migration;

/**
 * Class m240807_094850_migrate_gls_783_satusehat_integrasi_v
 */
class m240807_094850_migrate_gls_783_satusehat_integrasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS satusehat_integrasi_v");
        $satusehat_integrasi_v = file_get_contents(__DIR__ . '/definitions/satusehat_integrasi_v.sql');
        $this->execute($satusehat_integrasi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240807_094850_migrate_gls_783_satusehat_integrasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240807_094850_migrate_gls_783_satusehat_integrasi_v cannot be reverted.\n";

        return false;
    }
    */
}
