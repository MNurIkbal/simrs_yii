<?php

use yii\db\Migration;

/**
 * Class m210715_101712_migrate_improve_udd
 */
class m210715_101712_migrate_improve_udd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."obatalkespasien_t" ADD COLUMN if not exists "is_uddterima" bool DEFAULT false;');
    
    $this->execute('ALTER TABLE "public"."udd_detail_r" ADD COLUMN if not exists "tgl_proses" timestamp(6) DEFAULT CURRENT_TIMESTAMP;');
    $this->execute('ALTER TABLE "public"."udd_detail_r" ADD COLUMN if not exists "update_by" int4;');
    
    $this->execute('ALTER TABLE "public"."udd_r" ADD COLUMN if not exists "tgl_proses" timestamp(6) DEFAULT CURRENT_TIMESTAMP;');
    $this->execute('ALTER TABLE "public"."udd_r" ADD COLUMN if not exists "update_by" int4;');

    $this->execute('DROP VIEW if exists "public"."infoudd_detail_v";');

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
    udd_dosis_t.dosis,
        CASE
            WHEN udd_detail_t.tgl_selesai < CURRENT_DATE THEN true
            ELSE false
        END AS is_stoped,
    obatalkes_m.is_oral,
    udd_dosis_t.waktu_pemberian,
    udd_dosis_t.jam_pemberian,
    satuankonversi_m.satuankecil_id
   FROM udd_detail_t
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.is_oral
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
            d.dosis,
            string_agg(waktupemberian_m.waktu_pemberian::text, '-'::text) AS waktu_pemberian,
            string_agg(d.jam_pemberian::text, '-'::text) AS jam_pemberian
           FROM udd_dosis_t d
             JOIN waktupemberian_m ON d.waktupemberian_id = waktupemberian_m.waktupemberian_id
          WHERE d.is_deleted = false
          GROUP BY d.udd_detail_id, d.dosis) udd_dosis_t ON udd_detail_t.udd_detail_id = udd_dosis_t.udd_detail_id;");

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
                deleted_by,
                update_by        
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
                NEW.deleted_by,
                NEW.last_modified_by            
        );

        RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

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
                ruanganproses_id,
                update_by                       
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
                NEW.ruanganproses_id,
                NEW.last_modified_by
        );


        RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210715_101712_migrate_improve_udd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210715_101712_migrate_improve_udd cannot be reverted.\n";

        return false;
    }
    */
}
