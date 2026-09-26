<?php

use yii\db\Migration;

/**
 * Class m220314_045246_migrate_ORDH21_lookup_m
 */
class m220314_045246_migrate_ORDH21_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE FROM lookup_m WHERE lookup_id = 1145;');
        $this->execute("INSERT INTO lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan) VALUES (1145, 'jenis_pendaftaran', 'Pendaftaran Semua', 'Pendaftaran Semua', 3);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220314_045246_migrate_ORDH21_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220314_045246_migrate_ORDH21_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
