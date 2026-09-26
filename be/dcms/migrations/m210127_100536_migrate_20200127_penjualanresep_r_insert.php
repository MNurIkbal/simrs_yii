<?php

use yii\db\Migration;

/**
 * Class m210127_100536_migrate_20200127_penjualanresep_r_insert
 */
class m210127_100536_migrate_20200127_penjualanresep_r_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"penjualanresep_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN

------------------------------->INSERT table rekap penjualanresep_r<----------------------------------
        INSERT INTO penjualanresep_r (      
            penjualanresep_id,
            pasienadmisi_id,
            pegawai_id,
            pendaftaran_id,
            returresep_id,
            kelaspelayanan_id,
            penjamin_id,
            pasien_id,
            carabayar_id,
            ruangan_id,
            reseptur_id,
            shift_id,
            tglpenjualan,
            jenispenjualan,
            tglresep,
            noresep,
            totharganetto,
            totalhargajual,
            totaltarifservice,
            biayaadministrasi,
            biayakonseling,
            pembulatanharga,
            jasadokterresep,
            discount,
            subsidiasuransi,
            subsidipemerintah,
            subsidirs,
            iurbiaya,
            lamapelayanan,
            penjpasienpegawai_id,
            penjpasienruangan_id,
            antrianfarmasi_id,
            permohonanoa_id,
            takaranresep,
            isresepperawatan,
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
            iter,
            nama_pembeli,
            pegawairesep_id,
            karyawan_id,
            catatan,
            status_bayar,
            status_reseptur,
            antrian_id,
            pembatalanresep_id,
            status_worklist,
            tgl_lahir,
            log_user,
            keterangan
        )VALUES(
            NEW.penjualanresep_id,
            NEW.pasienadmisi_id,
            NEW.pegawai_id,
            NEW.pendaftaran_id,
            NEW.returresep_id,
            NEW.kelaspelayanan_id,
            NEW.penjamin_id,
            NEW.pasien_id,
            NEW.carabayar_id,
            NEW.ruangan_id,
            NEW.reseptur_id,
            NEW.shift_id,
            NEW.tglpenjualan,
            NEW.jenispenjualan,
            NEW.tglresep,
            NEW.noresep,
            NEW.totharganetto,
            NEW.totalhargajual,
            NEW.totaltarifservice,
            NEW.biayaadministrasi,
            NEW.biayakonseling,
            NEW.pembulatanharga,
            NEW.jasadokterresep,
            NEW.discount,
            NEW.subsidiasuransi,
            NEW.subsidipemerintah,
            NEW.subsidirs,
            NEW.iurbiaya,
            NEW.lamapelayanan,
            NEW.penjpasienpegawai_id,
            NEW.penjpasienruangan_id,
            NEW.antrianfarmasi_id,
            NEW.permohonanoa_id,
            NEW.takaranresep,
            NEW.isresepperawatan,
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
            NEW.iter,
            NEW.nama_pembeli,
            NEW.pegawairesep_id,
            NEW.karyawan_id,
            NEW.catatan,
            NEW.status_bayar,
            NEW.status_reseptur,
            NEW.antrian_id,
            NEW.pembatalanresep_id,
            NEW.status_worklist,
            NEW.tgl_lahir,
            NEW.log_user,
            'ACCRUAL'
        );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."penjualanresep_r_insert"() OWNER TO "postgres";');



        $this->execute('CREATE TRIGGER "penjualanresep_r_insert" AFTER INSERT ON "public"."penjualanresep_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penjualanresep_r_insert"();');


    $this->execute('COMMENT ON TRIGGER "penjualanresep_r_insert" ON "public"."penjualanresep_t" IS \'rekap penjualan resep (INSERT)\';');



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_100536_migrate_20200127_penjualanresep_r_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_100536_migrate_20200127_penjualanresep_r_insert cannot be reverted.\n";

        return false;
    }
    */
}
