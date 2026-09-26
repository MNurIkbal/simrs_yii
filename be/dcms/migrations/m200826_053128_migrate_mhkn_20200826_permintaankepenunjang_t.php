<?php

use yii\db\Migration;

/**
 * Class m200826_053128_migrate_mhkn_20200826_permintaankepenunjang_t
 */
class m200826_053128_migrate_mhkn_20200826_permintaankepenunjang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute('ALTER TABLE "public"."permintaankepenunjang_t" ADD COLUMN "dokter_id" int4;');
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200826_053128_migrate_mhkn_20200826_permintaankepenunjang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200826_053128_migrate_mhkn_20200826_permintaankepenunjang_t cannot be reverted.\n";

        return false;
    }
    */
}
