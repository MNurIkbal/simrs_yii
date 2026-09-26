<?php

use yii\db\Migration;

/**
 * Class m190401_032630_seed_bank_m
 */
class m190401_032630_seed_bank_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE bank_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."bank_m"("bank_id", "propinsi_id", "kabupaten_id", "nama_bank", "cabang", "no_rekening", "nama_pemilikrek", "no_tlp", "no_fax", "email", "alamat_bank", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (1, 12, 179, \'BANK BNI\', \'Bandung\', \'1234567890\', \'RS. SIRS\', \'12345678\', NULL, \'rs@sirs.co.id\', \'Jl. Sukahaji\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_032630_seed_bank_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_032630_seed_bank_m cannot be reverted.\n";

        return false;
    }
    */
}
