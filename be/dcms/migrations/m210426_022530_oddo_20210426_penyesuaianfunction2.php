<?php

use yii\db\Migration;

/**
 * Class m210426_022530_oddo_20210426_penyesuaianfunction2
 */
class m210426_022530_oddo_20210426_penyesuaianfunction2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
    DECLARE
        v_keterangan VARCHAR;

    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            v_keterangan := 'DELETE';
        ELSE
            v_keterangan := 'UPDATE';  
        END IF;
        
    -- INSERT table history pendaftaran_r
    INSERT INTO pendaftaran_r (     
        pendaftaran_id,
        no_pendaftaran,
        tgl_pendaftaran,
        pasienpulang_id,
        pasienbatalperiksa_id,
        penanggungjawab_id,
        penjamin_id,
        shift_id,
        pasien_id,
        persalinan_id,
        pegawai_id,
        instalasi_id,
        caramasuk_id,
        jeniskasuspenyakit_id,
        pembayaranpelayanan_id,
        kelaspelayanan_id,
        carabayar_id,
        pasienadmisi_id,
        golonganumur_id,
        rujukan_id,
        antrian_id,
        karcis_id,
        ruangan_id,
        no_urutantri,
        transportasi,
        keadaan_masuk,
        status_periksa,
        status_pasien,
        kunjungan,
        alih_status,
        by_phone,
        kunjungan_rumah,
        status_masuk,
        umur,
        tgl_selesaiperiksa,
        keterangan_pendaftaran,
        nopendaftaran_aktif,
        status_konfirmasi,
        tgl_konfirmasi,
        tgl_renkontrol,
        status_farmasi,
        panggil_antrian,
        asuransipasien_id,
        tgl_akandilayani,
        statusdok_rekammedik,
        bpjs_id,
        status_bayar,
        is_aps,
        label_gelang,
        is_karcis,
        tgl_masukperiksa,
        status_verifikasi,
        is_ranap,
        is_skd,
        pendaftaranibu_id,
        is_skl,
        catatan_penatajasa,
        is_stopakomodasi,
        tgl_stopakomodasi,
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
        keterangan
    )VALUES(
        NEW.pendaftaran_id ,
        NEW.no_pendaftaran ,
        NEW.tgl_pendaftaran ,
        NEW.pasienpulang_id ,
        NEW.pasienbatalperiksa_id ,
        NEW.penanggungjawab_id ,
        NEW.penjamin_id,
        NEW.shift_id ,
        NEW.pasien_id ,
        NEW.persalinan_id ,
        NEW.pegawai_id ,
        NEW.instalasi_id ,
        NEW.caramasuk_id ,
        NEW.jeniskasuspenyakit_id ,
        NEW.pembayaranpelayanan_id ,
        NEW.kelaspelayanan_id ,
        NEW.carabayar_id ,
        NEW.pasienadmisi_id ,
        NEW.golonganumur_id ,
        NEW.rujukan_id ,
        NEW.antrian_id ,
        NEW.karcis_id ,
        NEW.ruangan_id ,
        NEW.no_urutantri ,
        NEW.transportasi ,
        NEW.keadaan_masuk ,
        NEW.status_periksa ,
        NEW.status_pasien ,
        NEW.kunjungan ,
        NEW.alih_status ,
        NEW.by_phone ,
        NEW.kunjungan_rumah ,
        NEW.status_masuk ,
        NEW.umur ,
        NEW.tgl_selesaiperiksa ,
        NEW.keterangan_pendaftaran ,
        NEW.nopendaftaran_aktif ,
        NEW.status_konfirmasi ,
        NEW.tgl_konfirmasi ,
        NEW.tgl_renkontrol ,
        NEW.status_farmasi ,
        NEW.panggil_antrian ,
        NEW.asuransipasien_id ,
        NEW.tgl_akandilayani ,
        NEW.statusdok_rekammedik ,
        NEW.bpjs_id ,
        NEW.status_bayar ,
        NEW.is_aps ,
        NEW.label_gelang ,
        NEW.is_karcis ,
        NEW.tgl_masukperiksa ,
        NEW.status_verifikasi ,
        NEW.is_ranap ,
        NEW.is_skd ,
        NEW.pendaftaranibu_id ,
        NEW.is_skl ,
        NEW.catatan_penatajasa ,
        NEW.is_stopakomodasi ,
        NEW.tgl_stopakomodasi ,
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
        v_keterangan
    );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('ALTER FUNCTION "public"."pendaftaran_r_update"() OWNER TO "postgres";');

    $this->execute('DROP TRIGGER "obatalkespasien_r_update" ON "public"."obatalkespasien_t";');

    $this->execute('CREATE TRIGGER "obatalkespasien_r_update" AFTER UPDATE OF "tglpelayanan", "det", "obatsudahbayar_id", "qty_oa", "qty_konversi", "is_deleted" ON "public"."obatalkespasien_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."obatalkespasien_r_update"();');

    $this->execute('COMMENT ON TRIGGER "obatalkespasien_r_update" ON "public"."obatalkespasien_t" IS \'rekap saleorder_line (UPDATE)\';');

    $this->execute('DROP TRIGGER "pasien_r_update" ON "public"."pasien_m";');

    $this->execute('CREATE TRIGGER "pasien_r_update" AFTER UPDATE OF "alamat_pasien", "alamatemail", "jenisidentitas", "namadepan", "no_identitas_pasien", "nama_pasien", "no_mobile_pasien", "no_telepon_pasien", "alamat_sekarang", "no_rekam_medik", "tempat_lahir", "tanggal_lahir", "jeniskelamin" ON "public"."pasien_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pasien_r_update"();');

    $this->execute('DROP TRIGGER "pendaftaran_r_update" ON "public"."pendaftaran_t";');

    $this->execute('CREATE TRIGGER "pendaftaran_r_update" AFTER UPDATE ON "public"."pendaftaran_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pendaftaran_r_update"();');

    $this->execute('CREATE TRIGGER "int_billing_r_insert" AFTER INSERT ON "public"."pembayaran_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."int_billing_r_insert"();');
    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210426_022530_oddo_20210426_penyesuaianfunction2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210426_022530_oddo_20210426_penyesuaianfunction2 cannot be reverted.\n";

        return false;
    }
    */
}
