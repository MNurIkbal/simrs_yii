<?php

use yii\db\Migration;

/**
 * Class m201119_082751_3084_improvement_partograf
 */
class m201119_082751_3084_improvement_partograf extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute('
            DELETE FROM lookupkeperawatan_m
            WHERE lookup_type = \'jenis_persalinan\';
        '); 
     
         $this->execute('INSERT INTO "public"."lookupkeperawatan_m"("lookupkeperawatan_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (121, \'jenis_persalinan\', \'Brojol\', \'Brojol\', 1, NULL, NULL, \'2020-11-13 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);'); 

          $this->execute('INSERT INTO "public"."lookupkeperawatan_m"("lookupkeperawatan_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (122, \'jenis_persalinan\', \'ILA\', \'ILA\', 2, NULL, NULL, \'2020-11-13 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);'); 

          $this->execute('INSERT INTO "public"."lookupkeperawatan_m"("lookupkeperawatan_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (123, \'jenis_persalinan\', \'Komplikasi\', \'Komplikasi\', 3, NULL, NULL, \'2020-11-13 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);'); 
          $this->execute('INSERT INTO "public"."lookupkeperawatan_m"("lookupkeperawatan_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (124, \'jenis_persalinan\', \'Normal\', \'Normal\', 4, NULL, NULL, \'2020-11-13 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);'); 

          $this->execute(' INSERT INTO "public"."lookupkeperawatan_m"("lookupkeperawatan_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (125, \'jenis_persalinan\', \'ELA\', \'ELA\', 5, NULL, NULL, \'2020-11-13 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);'); 
         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201119_082751_3084_improvement_partograf cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201119_082751_3084_improvement_partograf cannot be reverted.\n";

        return false;
    }
    */
}
