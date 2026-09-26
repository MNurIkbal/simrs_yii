<?php

use yii\db\Migration;

/**
 * Class m190401_034148_seed_rujukandari_m
 */
class m190401_034148_seed_rujukandari_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE rujukandari_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."rujukandari_m"("rujukandari_id", "asalrujukan_id", "nama_perujuk", "spesialis", "alamatlengkap", "no_telp", "kode_ppk", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (1, 1, \'Datang Sendiri\', NULL, NULL, NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_034148_seed_rujukandari_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_034148_seed_rujukandari_m cannot be reverted.\n";

        return false;
    }
    */
}
