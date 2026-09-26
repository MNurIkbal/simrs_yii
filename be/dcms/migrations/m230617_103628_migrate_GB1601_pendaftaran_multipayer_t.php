<?php

use yii\db\Migration;

/**
 * Class m230617_103628_migrate_GB1601_pendaftaran_multipayer_t
 */
class m230617_103628_migrate_GB1601_pendaftaran_multipayer_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE pendaftaran_multipayer_t DROP CONSTRAINT IF EXISTS  pendaftaran_multipayer_t_pkey;');
        $this->execute('ALTER TABLE pendaftaran_multipayer_t ADD CONSTRAINT pendaftaran_multipayer_t_pkey PRIMARY KEY (pendaftaran_multipayer_id);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230617_103628_migrate_GB1601_pendaftaran_multipayer_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230617_103628_migrate_GB1601_pendaftaran_multipayer_t cannot be reverted.\n";

        return false;
    }
    */
}
