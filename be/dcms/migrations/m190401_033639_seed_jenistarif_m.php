<?php

use yii\db\Migration;

/**
 * Class m190401_033639_seed_jenistarif_m
 */
class m190401_033639_seed_jenistarif_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE jenistarif_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."jenistarif_m"("jenistarif_id", "jenistarif_nama", "jenistarif_namalainnya", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by", "jenistarif_kode", "catatan") VALUES 
            (1, \'Tarif Pelayanan\', \'Tarif Pelayanan\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, NULL),
            (2, \'Tarif BPJS\', \'Tarif BPJS\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, NULL),
            (3, \'Tarif Perusahaan\', \'Tarif Perusahaan\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, NULL),
            (4, \'Tarif Jamkesmas\', \'Tarif Jamkesmas\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_033639_seed_jenistarif_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_033639_seed_jenistarif_m cannot be reverted.\n";

        return false;
    }
    */
}
