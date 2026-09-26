<?php

use yii\db\Migration;

/**
 * Class m211216_075542_migrate_function_tariftindakan_r
 */
class m211216_075542_migrate_function_tariftindakan_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER if exists "tariftindakan_r_insert" ON "public"."tariftindakan_m";');    
        
        $this->execute('DROP TRIGGER if exists "tariftindakan_r_update" ON "public"."tariftindakan_m";');


        $this->execute('DROP FUNCTION if exists "public"."tariftindakan_r_insert";');

        $this->execute('DROP FUNCTION if exists "public"."tariftindakan_r_update";');
        
       
        $this->execute('CREATE OR REPLACE FUNCTION "public"."tariftindakan_r_update"()
  RETURNS "pg_catalog"."trigger" AS $BODY$
DECLARE

    
BEGIN
------------------------------->INSERT table history tariftindakan_r<----------------------------------
        INSERT INTO tariftindakan_r (       
            tariftindakan_id,
            kelaspelayanan_id,
            komponentarif_id,
            daftartindakan_id,
            jenistarif_id,
            perdatarif_id,
            harga_tariftindakan,
            persendiskon_tindakan,
            hargadiskon_tindakan,
            persencyto_tindakan,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_date,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            tipepaket_id,
            penjamin_id,
            is_clone,
            tarifparent_id,
            kamarruangan_id,
            persen_penyulit,
            dokter_id,
            ruangan_id,
            keterangan_rekap,
            tgl_proses
        )VALUES(
            NEW.tariftindakan_id,
            NEW.kelaspelayanan_id,
            NEW.komponentarif_id,
            NEW.daftartindakan_id,
            NEW.jenistarif_id,
            NEW.perdatarif_id,
            NEW.harga_tariftindakan,
            NEW.persendiskon_tindakan,
            NEW.hargadiskon_tindakan,
            NEW.persencyto_tindakan,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_date,
            NEW.last_modified_by,
            NEW.is_deleted,
            NEW.is_active,
            NEW.deleted_date,
            NEW.deleted_by,
            NEW.tipepaket_id,
            NEW.penjamin_id,
            NEW.is_clone,
            NEW.tarifparent_id,
            NEW.kamarruangan_id,
            NEW.persen_penyulit,
            NEW.dokter_id,
            NEW.ruangan_id,
            \'EDIT\',
            NOW()
            );

    RETURN NEW;
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');
    

        $this->execute('CREATE OR REPLACE FUNCTION "public"."tariftindakan_r_insert"()
  RETURNS "pg_catalog"."trigger" AS $BODY$
DECLARE

    
BEGIN
------------------------------->INSERT table history tariftindakan_r<----------------------------------
        INSERT INTO tariftindakan_r (       
            tariftindakan_id,
            kelaspelayanan_id,
            komponentarif_id,
            daftartindakan_id,
            jenistarif_id,
            perdatarif_id,
            harga_tariftindakan,
            persendiskon_tindakan,
            hargadiskon_tindakan,
            persencyto_tindakan,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_date,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            tipepaket_id,
            penjamin_id,
            is_clone,
            tarifparent_id,
            kamarruangan_id,
            persen_penyulit,
            dokter_id,
            ruangan_id,
            keterangan_rekap,
            tgl_proses
        )VALUES(
            NEW.tariftindakan_id,
            NEW.kelaspelayanan_id,
            NEW.komponentarif_id,
            NEW.daftartindakan_id,
            NEW.jenistarif_id,
            NEW.perdatarif_id,
            NEW.harga_tariftindakan,
            NEW.persendiskon_tindakan,
            NEW.hargadiskon_tindakan,
            NEW.persencyto_tindakan,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_date,
            NEW.last_modified_by,
            NEW.is_deleted,
            NEW.is_active,
            NEW.deleted_date,
            NEW.deleted_by,
            NEW.tipepaket_id,
            NEW.penjamin_id,
            NEW.is_clone,
            NEW.tarifparent_id,
            NEW.kamarruangan_id,
            NEW.persen_penyulit,
            NEW.dokter_id,
            NEW.ruangan_id,
            \'CREATE\',
            NEW.created_date
            );

    RETURN NEW;
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');


         $this->execute('CREATE TRIGGER "tariftindakan_r_insert" AFTER INSERT ON "public"."tariftindakan_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tariftindakan_r_insert"();');

        $this->execute('CREATE TRIGGER "tariftindakan_r_update" AFTER UPDATE ON "public"."tariftindakan_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tariftindakan_r_update"();');

        $this->execute('COMMENT ON TRIGGER "tariftindakan_r_insert" ON "public"."tariftindakan_m" IS \'rekap tariftidakan_r  (INSERT)\';');
        $this->execute('COMMENT ON TRIGGER "tariftindakan_r_update" ON "public"."tariftindakan_m" IS \'rekap tariftidakan_r (UPDATE)\';');
      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211216_075542_migrate_function_tariftindakan_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211216_075542_migrate_function_tariftindakan_r cannot be reverted.\n";

        return false;
    }
    */
}
