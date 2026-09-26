<?php

use yii\db\Migration;

/**
 * Class m240521_104621_migrate_rpp1166_optimasi_retur_resep_v
 */
class m240521_104621_migrate_rpp1166_optimasi_retur_resep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS pasien_retur_v");
                
        $pasien_retur_v = file_get_contents(__DIR__ . '/definitions/pasien_retur_v.sql');
        $this->execute($pasien_retur_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240521_104621_migrate_rpp1166_optimasi_retur_resep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240521_104621_migrate_rpp1166_optimasi_retur_resep_v cannot be reverted.\n";

        return false;
    }
    */
}
