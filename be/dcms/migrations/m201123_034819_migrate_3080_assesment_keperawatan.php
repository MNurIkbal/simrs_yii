<?php

use yii\db\Migration;

/**
 * Class m201123_034819_migrate_3080_assesment_keperawatan
 */
class m201123_034819_migrate_3080_assesment_keperawatan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE diagnosakep_m ADD IF NOT EXISTS kategoridiagnosakep_id int4;'); 
        $this->execute('ALTER TABLE diagnosakep_m ADD IF NOT EXISTS subdiagnosakep_id int4;');  
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS skrining_gizi TEXT;'); 
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS extremitas_utuh TEXT;');   
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS extremitas_nyeri TEXT;');  
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS extremitas_deformitas TEXT;'); 
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS extremitas_defisit_neurologis TEXT;'); 
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS extremitas_jejas TEXT;');  
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS extremitas_pulsasi TEXT;');    
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS extremitas_fraktur TEXT;');    
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS tipe_nadi TEXT;'); 
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS hasil_nadi TEXT;');    
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS pupil_os TEXT;');  
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS pupil_od TEXT;');  
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS jenis_resiko_jatuh TEXT;');    
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS asesmen_auto_anamnesa TEXT;'); 
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS nutrisi_1a TEXT;');    
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS nutrisi_1b TEXT;');    
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS nutrisi_2 TEXT;'); 
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS diagnosa_khusus TEXT;');   
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS jenis_diagnosa_khusus TEXT;'); 
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS strongkids_kurus TEXT;');  
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS strongkids_turunbb TEXT;');    
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS strongkids_kondisikhusus TEXT;');  
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS strongkids_keadaan_beresiko TEXT;');   
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS resusitasi TEXT;');    
        $this->execute('ALTER TABLE asesmenperawatrd_t ADD IF NOT EXISTS diagnosa_keperawatan TEXT;');  
        
        $this->execute('DROP TABLE IF EXISTS kategoridiagnosakep_m;');  
        $this->execute('DROP SEQUENCE IF EXISTS kategoridiagnosakep_m_kategoridiagnosakep_id_seq;');    
        
        $this->execute('
            CREATE SEQUENCE "public"."kategoridiagnosakep_m_kategoridiagnosakep_id_seq" 
            INCREMENT 1
            MINVALUE  1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        '); 
        
        $this->execute('
            CREATE TABLE "public"."kategoridiagnosakep_m" (
              "kategoridiagnosakep_id" int4 NOT NULL DEFAULT nextval(\'kategoridiagnosakep_m_kategoridiagnosakep_id_seq\'::regclass),
              kategoridiagnosakep_nama VARCHAR(100),
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4,
              CONSTRAINT "pk_kategoridiagnosakep_m" PRIMARY KEY ("kategoridiagnosakep_id")
            );
        '); 
        
        $this->execute('DROP TABLE IF EXISTS subdiagnosakep_m;');   
        $this->execute('DROP SEQUENCE IF EXISTS subdiagnosakep_m_subdiagnosakep_id_seq;');  
        
        $this->execute('
            CREATE SEQUENCE "public"."subdiagnosakep_m_subdiagnosakep_id_seq" 
            INCREMENT 1
            MINVALUE  1
            MAXVALUE 9223372036854775807
            START 1
            CACHE 1;
        '); 
        
        $this->execute('
            CREATE TABLE "public"."subdiagnosakep_m" (
              "subdiagnosakep_id" int4 NOT NULL DEFAULT nextval(\'subdiagnosakep_m_subdiagnosakep_id_seq\'::regclass),
              subdiagnosakep_kode VARCHAR(20),
              subdiagnosakep_nama VARCHAR(100),
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4,
              CONSTRAINT "pk_subdiagnosakep_m" PRIMARY KEY ("subdiagnosakep_id")
            );
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201123_034819_migrate_3080_assesment_keperawatan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201123_034819_migrate_3080_assesment_keperawatan cannot be reverted.\n";

        return false;
    }
    */
}
