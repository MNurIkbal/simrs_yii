<?php

use yii\db\Migration;

/**
 * Class m230821_033959_migrate_GA_480_lookup_m
 */
class m230821_033959_migrate_GA_480_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m where lookup_id in (2115,2116,2117)");


        $this->execute('INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (2115, \'kategori_resep\', \'Resep UDD\', \'1\', 1, \'UDD\', NULL, \'2023-08-01 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);');

        $this->execute('INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (2116, \'kategori_resep\', \'Terapi Baru (TB)\', \'2\', 2, \'TB\', NULL, \'2023-08-01 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);');

        $this->execute('INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (2117, \'kategori_resep\', \'Obat Pulang (OP)\', \'3\', 3, \'OP\', NULL, \'2023-08-01 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230821_033959_migrate_GA_480_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230821_033959_migrate_GA_480_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
