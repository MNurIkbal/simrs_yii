<?php

use yii\db\Migration;

/**
 * Class m240122_031911_migrate_DSV1034_satusehat_pasien_v
 */
class m240122_031911_migrate_DSV1034_satusehat_pasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS satusehat_pasien_v");
        $satusehat_pasien_v = file_get_contents(__DIR__ . '/definitions/satusehat_pasien_v.sql');
        $this->execute($satusehat_pasien_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240122_031911_migrate_DSV1034_satusehat_pasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240122_031911_migrate_DSV1034_satusehat_pasien_v cannot be reverted.\n";

        return false;
    }
    */
}
