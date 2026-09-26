<?php

use yii\db\Migration;

/**
 * Class m231026_141600_migrate_returresep_per_pendaftaran_improve_table_rerurresep_t
 */
class m231026_141600_migrate_returresep_per_pendaftaran_improve_table_rerurresep_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE returresep_t ADD IF NOT EXISTS user_verifikator int4;');
		$this->execute('ALTER TABLE returresep_t ADD IF NOT EXISTS tgl_verif timestamp;');
		$this->execute('ALTER TABLE returresep_t ADD IF NOT EXISTS status_retur int4 DEFAULT 2118;');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231026_141600_migrate_returresep_per_pendaftaran_improve_table_rerurresep_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231026_141600_migrate_returresep_per_pendaftaran_improve_table_rerurresep_t cannot be reverted.\n";

        return false;
    }
    */
}
