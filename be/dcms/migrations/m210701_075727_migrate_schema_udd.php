<?php

use yii\db\Migration;

/**
 * Class m210701_075727_migrate_schema_udd
 */
class m210701_075727_migrate_schema_udd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE if not exists "public"."udd_t" (
  "udd_id" serial8,
  "pendaftaran_id" int4,
  "pasienadmisi_id" int4,
  "tgl_order" timestamp(6) DEFAULT (\'now\'::text)::date,
  "no_udd" varchar(255) COLLATE "pg_catalog"."default",
  "instruksi_id" int4,
  "status_udd" int4 DEFAULT 1029,
  "peg_penerima_id" int4,
  "tgl_terima" timestamp(6),
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
  "ruanganproses_id" int4,
  CONSTRAINT "udd_t_pkey" PRIMARY KEY ("udd_id")
)
;');
        $this->execute('COMMENT ON COLUMN "public"."udd_t"."status_udd" IS \'lookup_type=status_udd\';');

        $this->execute('CREATE TABLE if not exists "public"."udd_r" (
  "id" serial8,
  "udd_id" int4,
  "pendaftaran_id" int4,
  "pasienadmisi_id" int4,
  "tgl_order" timestamp(6) DEFAULT (\'now\'::text)::date,
  "no_udd" varchar(255) COLLATE "pg_catalog"."default",
  "instruksi_id" int4,
  "status_udd" int4 DEFAULT 1029,
  "peg_penerima_id" int4,
  "tgl_terima" timestamp(6),
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
  "ruanganproses_id" int4,
  CONSTRAINT "udd_r_pkey" PRIMARY KEY ("id")
)
;');

        $this->execute('CREATE TABLE if not exists "public"."udd_detail_t" (
  "udd_detail_id" serial8,
  "udd_id" int4 NOT NULL,
  "obatalkes_id" int4,
  "qty" float4,
  "signa_id" int4,
  "catatan_dokter" text COLLATE "pg_catalog"."default",
  "tgl_mulai" date DEFAULT (\'now\'::text)::date,
  "tgl_selesai" date,
  "satuaninput_id" int4,
  "satuankecil_id" int4,
  "satuankonversi_id" int4,
  "catatan_farmasi" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "udd_detail_t_pkey" PRIMARY KEY ("udd_detail_id")
)
;');

        $this->execute('CREATE TABLE if not exists "public"."udd_detail_r" (
  "id" serial8,
  "udd_detail_id" int8,
  "udd_id" int4,
  "obatalkes_id" int4,
  "qty" float4,
  "signa_id" int4,
  "catatan_dokter" text COLLATE "pg_catalog"."default",
  "tgl_mulai" date DEFAULT (\'now\'::text)::date,
  "tgl_selesai" date,
  "satuaninput_id" int4,
  "satuankecil_id" int4,
  "satuankonversi_id" int4,
  "catatan_farmasi" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "udd_detail_r_pkey" PRIMARY KEY ("id")
)
;');

        $this->execute('CREATE TABLE if not exists "public"."udd_dosis_t" (
  "udd_dosis_id" serial8,
  "udd_detail_id" int4 NOT NULL,
  "pemberianobat_id" int4,
  "obatalkes_id" int4,
  "dosis" float4,
  "waktupemberian_id" int4,
  "jam_pemberian" time(6),
  "keterangan" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "udd_dosis_t_pkey" PRIMARY KEY ("udd_dosis_id")
)
;');

        $this->execute('CREATE TABLE if not exists "public"."waktupemberian_m" (
  "waktupemberian_id" serial8,
  "waktu_pemberian" varchar(100) COLLATE "pg_catalog"."default",
  "jam_mulai" time(6),
  "jam_akhir" time(6),
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
  CONSTRAINT "waktupemberian_m_pkey" PRIMARY KEY ("waktupemberian_id")
)
;');

        $this->execute('DROP VIEW if exists public.infoudd_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infoudd_v\" AS  SELECT udd_t.udd_id,
    udd_t.tgl_order,
    udd_t.no_udd,
    udd_t.pendaftaran_id,
    udd_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
        CASE
            WHEN udd_t.pasienadmisi_id IS NULL THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN udd_t.pasienadmisi_id IS NULL THEN peg_pendaftaran.nama_pegawai
            ELSE peg_admisi.nama_pegawai
        END AS dok_dpjp,
        CASE
            WHEN udd_t.pasienadmisi_id IS NULL THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN udd_t.pasienadmisi_id IS NULL THEN ruangan_pendaftaran.ruangan_nama
            ELSE ruangan_admisi.ruangan_nama
        END AS ruangan,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    kamartempattidur_m.kamartempattidur_id,
    kamartempattidur_m.no_tempattidur AS no_tt,
    udd_t.status_udd AS status_id,
    fgetnamalookup(udd_t.status_udd) AS status,
    udd_t.peg_penerima_id,
    peg_penerima.nama_pegawai AS penerima,
    udd_t.tgl_terima,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    COALESCE(cppt_t.diagnosa_utama, resumemedisri_t.diagnosa_utama) AS diagnosa_utama,
    asesmenmedis_t.r_alergiobat AS alergi,
    udd_t.ruanganproses_id
   FROM udd_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran,
            a.pasien_id,
            a.ruangan_id,
            a.pegawai_id,
            a.umur
           FROM pendaftaran_t a) pendaftaran_t ON udd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.no_rekam_medik,
            b.tanggal_lahir
           FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama
           FROM ruangan_m c) ruangan_pendaftaran ON pendaftaran_t.ruangan_id = ruangan_pendaftaran.ruangan_id
     JOIN ( SELECT d.pasienadmisi_id,
            d.ruangan_id,
            d.kamarruangan_id,
            d.kamartempattidur_id,
            d.pegawai_id
           FROM pasienadmisi_t d
          WHERE d.pasienpulang_id IS NULL) pasienadmisi_t ON udd_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT e.ruangan_id,
            e.ruangan_nama
           FROM ruangan_m e) ruangan_admisi ON pasienadmisi_t.ruangan_id = ruangan_admisi.ruangan_id
     LEFT JOIN ( SELECT f.kamarruangan_id,
            f.kamarruangan_nokamar
           FROM kamarruangan_m f) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT g.kamartempattidur_id,
            g.no_tempattidur
           FROM kamartempattidur_m g) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN ( SELECT h.pegawai_id,
            h.nama_pegawai
           FROM pegawai_m h) peg_pendaftaran ON pendaftaran_t.pegawai_id = peg_pendaftaran.pegawai_id
     LEFT JOIN ( SELECT i.pegawai_id,
            i.nama_pegawai
           FROM pegawai_m i) peg_admisi ON pasienadmisi_t.pegawai_id = peg_admisi.pegawai_id
     LEFT JOIN ( SELECT j.pegawai_id,
            j.nama_pegawai
           FROM pegawai_m j) peg_penerima ON udd_t.peg_penerima_id = peg_penerima.pegawai_id
     LEFT JOIN ( SELECT DISTINCT ON (k.pasienadmisi_id) k.pasienadmisi_id,
            concat(k.diag_utama ->> 'kode'::text, ' - ', k.diag_utama ->> 'nama'::text) AS diagnosa_utama
           FROM resumemedisri_t k
          WHERE k.is_deleted = false) resumemedisri_t ON udd_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT ON (l.pasienadmisi_id) l.pasienadmisi_id,
            l.tinggi_badan,
            l.berat_badan,
            l.r_alergiobat
           FROM asesmenmedis_t l
          WHERE l.is_deleted = false) asesmenmedis_t ON udd_t.pasienadmisi_id = asesmenmedis_t.pasienadmisi_id
     LEFT JOIN ( SELECT DISTINCT ON (m.pasienadmisi_id) m.pasienadmisi_id,
            concat(m.a_diag_utama ->> 'kode'::text, ' - ', m.a_diag_utama ->> 'nama'::text) AS diagnosa_utama
           FROM cppt_t m
          WHERE m.is_deleted = false) cppt_t ON udd_t.pasienadmisi_id = cppt_t.pasienadmisi_id
  WHERE udd_t.is_deleted = false;");

        $this->execute('DROP VIEW if exists public.infoudd_detail_v;');

        $this->execute("
            CREATE VIEW \"public\".\"infoudd_detail_v\" AS  SELECT udd_detail_t.udd_detail_id,
    udd_detail_t.udd_id,
    udd_detail_t.tgl_mulai,
    udd_detail_t.tgl_selesai,
    udd_detail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama AS obat,
    satuankonversi_m.satuan_besar,
    satuankonversi_m.satuan_kecil,
    signa_m.signa_nama AS signa,
    udd_detail_t.qty,
    udd_detail_t.catatan_dokter,
    udd_detail_t.catatan_farmasi,
    udd_dosis_t.dosis
   FROM udd_detail_t
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama
           FROM obatalkes_m a) obatalkes_m ON udd_detail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT b.satuankonversi_id,
            b.satuanbesar_id,
            sat_besar.satuanunit_nama AS satuan_besar,
            b.satuankecil_id,
            sat_kecil.satuanunit_nama AS satuan_kecil,
            b.nilai_konversi
           FROM satuankonversi_m b
             JOIN satuanunit_m sat_besar ON b.satuanbesar_id = sat_besar.satuanunit_id
             JOIN satuanunit_m sat_kecil ON b.satuankecil_id = sat_kecil.satuanunit_id
          WHERE b.is_deleted = false) satuankonversi_m ON udd_detail_t.satuankonversi_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT c.signa_id,
            c.signa_nama
           FROM signaobat_m c) signa_m ON udd_detail_t.signa_id = signa_m.signa_id
     LEFT JOIN ( SELECT d.udd_detail_id,
            d.dosis
           FROM udd_dosis_t d
          WHERE d.is_deleted = false) udd_dosis_t ON udd_detail_t.udd_detail_id = udd_dosis_t.udd_detail_id
  WHERE udd_detail_t.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN if not exists "udd_detail_id" int4;');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_udd\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 66; --> Transaksi UDD (UDD)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_udd), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN udd_t ON udd_t.created_date::DATE = CURRENT_DATE
    WHERE penomoran_id = vId; 
        
    SELECT 
        prefix 
    INTO 
        vPrefix 
    FROM penomoran_k 
    WHERE penomoran_id = vId; 
    
    UPDATE penomoran_k SET
        last_number = vNumber,
        last_generate = TRIM(vPrefix) || vNumber
  WHERE penomoran_id = vId;
        
    NEW.no_udd := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "no_udd" BEFORE INSERT ON "public"."udd_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."no_udd"();');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"udd_detail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$   
        
BEGIN

     INSERT INTO udd_detail_r (
                udd_detail_id,
                udd_id,
                obatalkes_id,
                qty,
                signa_id,
                catatan_dokter,
                tgl_mulai,
                tgl_selesai,
                satuaninput_id,
                satuankecil_id,
                satuankonversi_id,
                catatan_farmasi,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by     
     )VALUES(
                NEW.udd_detail_id,
                NEW.udd_id,
                NEW.obatalkes_id,
                NEW.qty,
                NEW.signa_id,
                NEW.catatan_dokter,
                NEW.tgl_mulai,
                NEW.tgl_selesai,
                NEW.satuaninput_id,
                NEW.satuankecil_id,
                NEW.satuankonversi_id,
                NEW.catatan_farmasi,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by      
        );


        RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "udd_detail_r_insert" AFTER INSERT ON "public"."udd_detail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."udd_detail_r_insert"();');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"udd_detail_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$   
        
BEGIN

     INSERT INTO udd_detail_r (
                udd_detail_id,
                udd_id,
                obatalkes_id,
                qty,
                signa_id,
                catatan_dokter,
                tgl_mulai,
                tgl_selesai,
                satuaninput_id,
                satuankecil_id,
                satuankonversi_id,
                catatan_farmasi,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by     
     )VALUES(
                NEW.udd_detail_id,
                NEW.udd_id,
                NEW.obatalkes_id,
                NEW.qty,
                NEW.signa_id,
                NEW.catatan_dokter,
                NEW.tgl_mulai,
                NEW.tgl_selesai,
                NEW.satuaninput_id,
                NEW.satuankecil_id,
                NEW.satuankonversi_id,
                NEW.catatan_farmasi,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by      
        );

        RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "udd_detail_r_update" AFTER UPDATE ON "public"."udd_detail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."udd_detail_r_update"();');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"udd_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$   
        
BEGIN

     INSERT INTO udd_r (
                udd_id,
                pendaftaran_id,
                pasienadmisi_id,
                tgl_order,
                no_udd,
                instruksi_id,
                status_udd,
                peg_penerima_id,
                tgl_terima,
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
                ruanganproses_id                        
     )VALUES(
                NEW.udd_id,
                NEW.pendaftaran_id,
                NEW.pasienadmisi_id,
                NEW.tgl_order,
                NEW.no_udd,
                NEW.instruksi_id,
                NEW.status_udd,
                NEW.peg_penerima_id,
                NEW.tgl_terima,
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
                NEW.ruanganproses_id
        );


        RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "udd_r_insert" AFTER INSERT ON "public"."udd_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."udd_r_insert"();');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"udd_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$   
        
BEGIN

     INSERT INTO udd_r (
                udd_id,
                pendaftaran_id,
                pasienadmisi_id,
                tgl_order,
                no_udd,
                instruksi_id,
                status_udd,
                peg_penerima_id,
                tgl_terima,
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
                ruanganproses_id                        
     )VALUES(
                NEW.udd_id,
                NEW.pendaftaran_id,
                NEW.pasienadmisi_id,
                NEW.tgl_order,
                NEW.no_udd,
                NEW.instruksi_id,
                NEW.status_udd,
                NEW.peg_penerima_id,
                NEW.tgl_terima,
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
                NEW.ruanganproses_id
        );


        RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('CREATE TRIGGER "udd_r_update" AFTER UPDATE ON "public"."udd_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."udd_r_update"();');

        $this->execute('DELETE from penomoran_k WHERE penomoran_id=66');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES (66, 'Transaksi UDD', 'UDD', 'UDD202107010007', '202107010007', '0');
");
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210701_075727_migrate_schema_udd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210701_075727_migrate_schema_udd cannot be reverted.\n";

        return false;
    }
    */
}
