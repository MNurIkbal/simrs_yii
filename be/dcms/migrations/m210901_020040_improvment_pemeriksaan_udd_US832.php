<?php

use yii\db\Migration;

/**
 * Class m210901_020040_improvment_pemeriksaan_udd_US832
 */
class m210901_020040_improvment_pemeriksaan_udd_US832 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id = 1040;
        ');

        $this->execute('
            INSERT INTO "lookup_m"("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (1040, \'jenis_instruksi\', \'UDD\', \'UDD\', 4, NULL, NULL, \'2021-07-30 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210901_020040_improvment_pemeriksaan_udd_US832 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210901_020040_improvment_pemeriksaan_udd_US832 cannot be reverted.\n";

        return false;
    }
    */
}
