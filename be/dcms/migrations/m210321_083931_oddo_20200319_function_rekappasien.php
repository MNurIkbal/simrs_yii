<?php

use yii\db\Migration;

/**
 * Class m210321_083931_oddo_20200319_function_rekappasien
 */
class m210321_083931_oddo_20200319_function_rekappasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pasien_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
-- @created by ridona 31 Agustus 2020
    
        
BEGIN
        -- INSERT table history pasien_r
        INSERT INTO pasien_r (      
        pasien_id,
        no_rekam_medik,
        tgl_rekam_medik,
        jenisidentitas,
        no_identitas_pasien,
        namadepan,
        nama_pasien,
        nama_bin,
        jeniskelamin,
        tempat_lahir,
        tanggal_lahir,
        golonganumur_id,
        alamat_pasien,
        rt,
        rw,
        propinsi_id,
        kabupaten_id,
        kecamatan_id,
        kelurahan_id,
        pendidikan_id,
        pekerjaan_id,
        suku_id,
        statusperkawinan,
        agama,
        golongandarah,
        rhesus,
        anakke,
        jumlah_bersaudara,
        no_telepon_pasien,
        no_mobile_pasien,
        warga_negara,
        photopasien,
        alamatemail,
        nama_ibu,
        nama_ayah,
        dokrekammedis_id,
        tgl_meninggal,
        pegawai_id,
        loginpemakai_id,
        garis_latitude,
        garis_longitude,
        statusrekammedis,
        profilrs_id,
        alamat_sekarang,
        nopeserta_bpjs,
        is_aps,
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
        keterangan,
        additional_pasien
        )VALUES(
        NEW.pasien_id ,
        NEW.no_rekam_medik ,
        NEW.tgl_rekam_medik ,
        NEW.jenisidentitas ,
        NEW.no_identitas_pasien ,
        NEW.namadepan ,
        NEW.nama_pasien ,
        NEW.nama_bin ,
        NEW.jeniskelamin ,
        NEW.tempat_lahir ,
        NEW.tanggal_lahir ,
        NEW.golonganumur_id ,
        NEW.alamat_pasien ,
        NEW.rt ,
        NEW.rw ,
        NEW.propinsi_id ,
        NEW.kabupaten_id ,
        NEW.kecamatan_id ,
        NEW.kelurahan_id ,
        NEW.pendidikan_id ,
        NEW.pekerjaan_id ,
        NEW.suku_id ,
        NEW.statusperkawinan ,
        NEW.agama ,
        NEW.golongandarah ,
        NEW.rhesus ,
        NEW.anakke ,
        NEW.jumlah_bersaudara ,
        NEW.no_telepon_pasien ,
        NEW.no_mobile_pasien ,
        NEW.warga_negara ,
        NEW.photopasien ,
        NEW.alamatemail ,
        NEW.nama_ibu ,
        NEW.nama_ayah ,
        NEW.dokrekammedis_id ,
        NEW.tgl_meninggal ,
        NEW.pegawai_id ,
        NEW.loginpemakai_id ,
        NEW.garis_latitude ,
        NEW.garis_longitude ,
        NEW.statusrekammedis ,
        NEW.profilrs_id ,
        NEW.alamat_sekarang ,
        NEW.nopeserta_bpjs ,
        NEW.is_aps ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'INSERT',
        NEW.additional_pasien 
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."pasien_r_insert"() OWNER TO "postgres";');
        
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pasien_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    -- @created by ridona 31 Agustus 2020
    
    DECLARE
        v_keterangan VARCHAR;
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            v_keterangan := 'DELETE';
        ELSE
            v_keterangan := 'UPDATE';
  END IF;
        
    -- INSERT table history pasien_r
    INSERT INTO pasien_r (      
        pasien_id,
        no_rekam_medik,
        tgl_rekam_medik,
        jenisidentitas,
        no_identitas_pasien,
        namadepan,
        nama_pasien,
        nama_bin,
        jeniskelamin,
        tempat_lahir,
        tanggal_lahir,
        golonganumur_id,
        alamat_pasien,
        rt,
        rw,
        propinsi_id,
        kabupaten_id,
        kecamatan_id,
        kelurahan_id,
        pendidikan_id,
        pekerjaan_id,
        suku_id,
        statusperkawinan,
        agama,
        golongandarah,
        rhesus,
        anakke,
        jumlah_bersaudara,
        no_telepon_pasien,
        no_mobile_pasien,
        warga_negara,
        photopasien,
        alamatemail,
        nama_ibu,
        nama_ayah,
        dokrekammedis_id,
        tgl_meninggal,
        pegawai_id,
        loginpemakai_id,
        garis_latitude,
        garis_longitude,
        statusrekammedis,
        profilrs_id,
        alamat_sekarang,
        nopeserta_bpjs,
        is_aps,
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
        keterangan,
        additional_pasien
    )VALUES(
        NEW.pasien_id ,
        NEW.no_rekam_medik ,
        NEW.tgl_rekam_medik ,
        NEW.jenisidentitas ,
        NEW.no_identitas_pasien , 
        NEW.namadepan ,
        NEW.nama_pasien ,
        NEW.nama_bin ,
        NEW.jeniskelamin ,
        NEW.tempat_lahir ,
        NEW.tanggal_lahir ,
        NEW.golonganumur_id ,
        NEW.alamat_pasien ,
        NEW.rt ,
        NEW.rw ,
        NEW.propinsi_id ,
        NEW.kabupaten_id ,
        NEW.kecamatan_id ,
        NEW.kelurahan_id ,
        NEW.pendidikan_id ,
        NEW.pekerjaan_id ,
        NEW.suku_id ,
        NEW.statusperkawinan ,
        NEW.agama ,
        NEW.golongandarah ,
        NEW.rhesus ,
        NEW.anakke ,
        NEW.jumlah_bersaudara ,
        NEW.no_telepon_pasien ,
        NEW.no_mobile_pasien ,
        NEW.warga_negara ,
        NEW.photopasien ,
        NEW.alamatemail ,
        NEW.nama_ibu ,
        NEW.nama_ayah ,
        NEW.dokrekammedis_id ,
        NEW.tgl_meninggal ,
        NEW.pegawai_id ,
        NEW.loginpemakai_id ,
        NEW.garis_latitude ,
        NEW.garis_longitude ,
        NEW.statusrekammedis ,
        NEW.profilrs_id ,
        NEW.alamat_sekarang ,
        NEW.nopeserta_bpjs ,
        NEW.is_aps ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        v_keterangan,
        NEW.additional_pasien
    );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."pasien_r_update"() OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210321_083931_oddo_20200319_function_rekappasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210321_083931_oddo_20200319_function_rekappasien cannot be reverted.\n";

        return false;
    }
    */
}
