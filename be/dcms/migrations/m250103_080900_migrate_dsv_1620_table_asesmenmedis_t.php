<?php

use yii\db\Migration;

/**
 * Class m250103_080900_migrate_dsv_1620_table_asesmenmedis_t
 */
class m250103_080900_migrate_dsv_1620_table_asesmenmedis_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS pemeriksaan_spesialis TEXT;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS formasesmen_id int2;
        ');

        $this->execute('
            ALTER TABLE asesmenmedis_t ADD IF NOT EXISTS is_dokumen_eklaim BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250103_080900_migrate_dsv_1620_table_asesmenmedis_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250103_080900_migrate_dsv_1620_table_asesmenmedis_t cannot be reverted.\n";

        return false;
    }
    */
}
