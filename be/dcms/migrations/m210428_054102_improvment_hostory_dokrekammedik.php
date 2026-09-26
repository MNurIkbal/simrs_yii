<?php

use yii\db\Migration;

/**
 * Class m210428_054102_improvment_hostory_dokrekammedik
 */
class m210428_054102_improvment_hostory_dokrekammedik extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_type = \'status_riwayat_rm\';
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m"("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1020, \'status_riwayat_rm\', \'Request\', \'Request\', NULL, NULL, NULL, \'2021-04-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1021, \'status_riwayat_rm\', \'Delivery\', \'Delivery\', NULL, NULL, NULL, \'2021-04-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1022, \'status_riwayat_rm\', \'Issues\', \'Issues\', NULL, NULL, NULL, \'2021-04-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1023, \'status_riwayat_rm\', \'Receive\', \'Receive\', NULL, NULL, NULL, \'2021-04-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (1024, \'status_riwayat_rm\', \'Return\', \'Return\', NULL, NULL, NULL, \'2021-04-21 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');


        $this->execute('
            DROP TABLE IF EXISTS permintaandokrekammedik_t;
        ');

        $this->execute('
            CREATE TABLE "public"."permintaandokrekammedik_t" (
              "permintaandokrekammedik_id" serial8 NOT NULL PRIMARY KEY,
              "pendaftaran_id" int4,
              "pasienadmisi_id" int4,
              "pasien_id" int4,
              "dokrekammedis_id" int4,
              "tgl_permintaan" timestamp(6),
              "tgl_dikembalikan" timestamp(6),
              "status_rekam_medik" int4,
              "pegawai_id" int4,
              "ruangan_id" int4,
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4
            );
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."update_process_rm"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                DECLARE
                vpasien_id INT;
                vstatus_konfirmasi VARCHAR;
                vtglrekammedis DATE;
            BEGIN
                vstatus_konfirmasi := NEW.status_konfirmasi;
                vpasien_id := NEW.pasien_id;
                
                IF(vstatus_konfirmasi = \'665\')
                THEN
                    IF NOT EXISTS (
                        SELECT 1 
                        FROM dokrekammedis_m
                        WHERE pasien_id = vpasien_id
                        LIMIT 1
                    )
                    THEN
                        SELECT tgl_rekam_medik INTO vtglrekammedis
                        FROM pasien_m
                        WHERE pasien_id = vpasien_id;
                        
                        INSERT INTO dokrekammedis_m(
                            warnadokrm_id, pasien_id, tglrekammedis, tglmasukrak, statusrekammedis
                        )VALUES(
                            1, vpasien_id, vtglrekammedis, CURRENT_DATE,\'336\'
                        );
                    END IF;
                END IF;

                RETURN NEW;
            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."insert_riwayat_rm_pendaftaran"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                DECLARE
                vpendaftaran_id INT;
                vpasienadmisi_id INT;
                vpasien_id INT;
                vtgl_permintaan TIMESTAMP;
                vruangan_id INT;
                vuser_id INT;
                
            BEGIN
                vpendaftaran_id := NEW.pendaftaran_id;
                vpasienadmisi_id := NEW.pasienadmisi_id;
                vpasien_id := NEW.pasien_id;
                vuser_id := NEW.created_by;
                vtgl_permintaan := NEW.tgl_pendaftaran;
                vruangan_id := NEW.ruangan_id;
                
                IF(NEW.is_aps = FALSE)
                THEN
                    IF(COALESCE(vpasienadmisi_id,0) = 0)
                    THEN
                        
                        INSERT INTO permintaandokrekammedik_t(
                            pendaftaran_id, pasienadmisi_id,pasien_id, tgl_permintaan, status_rekam_medik, ruangan_id,
                            created_by, created_date
                        )VALUES(
                            vpendaftaran_id, vpasienadmisi_id, vpasien_id, vtgl_permintaan, 1020, vruangan_id,
                            vuser_id, CURRENT_TIMESTAMP
                        );
                    END IF;
                END IF;

                RETURN NEW;
            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');


        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."insert_riwayat_rm_admisi"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                DECLARE
                vpendaftaran_id INT;
                vpasienadmisi_id INT;
                vpasien_id INT;
                vtgl_permintaan TIMESTAMP;
                vruangan_id INT;
                vuser_id INT;
                
            BEGIN
                vpendaftaran_id := NEW.pendaftaran_id;
                vpasienadmisi_id := NEW.pasienadmisi_id;
                vpasien_id := NEW.pasien_id;
                vuser_id := NEW.created_by;
                vtgl_permintaan := NEW.tgl_admisi;
                vruangan_id := NEW.ruangan_id;
                
                INSERT INTO permintaandokrekammedik_t(
                    pendaftaran_id, pasienadmisi_id,pasien_id, tgl_permintaan, status_rekam_medik, ruangan_id,
                    created_by, created_date
                )VALUES(
                    vpendaftaran_id, vpasienadmisi_id, vpasien_id, vtgl_permintaan, 1020, vruangan_id,
                    vuser_id, CURRENT_TIMESTAMP
                );

                RETURN NEW;
            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');

        $this->execute('
            DROP TRIGGER IF EXISTS "rm_update_process" ON "public"."pendaftaran_t";
        ');

        $this->execute('
            CREATE TRIGGER "rm_update_process" 
            AFTER UPDATE OF "status_konfirmasi" ON "public"."pendaftaran_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."update_process_rm"();
        ');

        $this->execute('
            DROP TRIGGER IF EXISTS "rm_riwayat_rm" ON "public"."pendaftaran_t";
        ');

        $this->execute('
            CREATE TRIGGER "rm_riwayat_rm" BEFORE INSERT ON "public"."pendaftaran_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."insert_riwayat_rm_pendaftaran"();
        ');

        $this->execute('
            DROP TRIGGER IF EXISTS "riwayat_rm_admisi" ON "public"."pasienadmisi_t";
        ');

        $this->execute('
            CREATE TRIGGER "riwayat_rm_admisi" BEFORE INSERT ON "public"."pasienadmisi_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."insert_riwayat_rm_admisi"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210428_054102_improvment_hostory_dokrekammedik cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210428_054102_improvment_hostory_dokrekammedik cannot be reverted.\n";

        return false;
    }
    */
}
