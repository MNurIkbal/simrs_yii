<?php

use yii\db\Migration;

/**
 * Class m230214_093550_migrate_GBD75_table_asesmenmedis_t 
 */
class m230214_093550_migrate_GBD75_table_asesmenmedis_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS sistem_saraf int4,
                ADD IF NOT EXISTS genetalia int4,
                ADD IF NOT EXISTS edema int4,
                ADD IF NOT EXISTS crt int4,
                ADD IF NOT EXISTS sistem_saraf_lainnya TEXT,
                ADD IF NOT EXISTS genetalia_lainnya TEXT,
                ADD IF NOT EXISTS edema_lainnya TEXT,
                ADD IF NOT EXISTS crt_lainnya TEXT,
                ADD IF NOT EXISTS pemeriksaan_fisik_lainnya TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230214_093550_migrate_GBD75_table_asesmenmedis_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230214_093550_migrate_GBD75_table_asesmenmedis_t cannot be reverted.\n";

        return false;
    }
    */
}
