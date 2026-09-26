<?php

use yii\db\Migration;

/**
 * Class m221215_102606_migrate_odoo_table_pendaftaran_multipayer_t
 */
class m221215_102606_migrate_odoo_table_pendaftaran_multipayer_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pendaftaran_multipayer_t ADD IF NOT EXISTS bpjs_id int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221215_102606_migrate_odoo_table_pendaftaran_multipayer_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221215_102606_migrate_odoo_table_pendaftaran_multipayer_t cannot be reverted.\n";

        return false;
    }
    */
}
