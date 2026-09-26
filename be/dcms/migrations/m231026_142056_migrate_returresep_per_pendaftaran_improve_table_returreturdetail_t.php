<?php

use yii\db\Migration;

/**
 * Class m231026_142056_migrate_returresep_per_pendaftaran_improve_table_returreturdetail_t
 */
class m231026_142056_migrate_returresep_per_pendaftaran_improve_table_returreturdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('ALTER TABLE returresepdetail_t ADD IF NOT EXISTS qty_pemberian_akhir int4;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231026_142056_migrate_returresep_per_pendaftaran_improve_table_returreturdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231026_142056_migrate_returresep_per_pendaftaran_improve_table_returreturdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
