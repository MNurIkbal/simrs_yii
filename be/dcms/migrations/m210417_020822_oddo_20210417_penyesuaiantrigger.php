<?php

use yii\db\Migration;

/**
 * Class m210417_020822_oddo_20210417_penyesuaiantrigger
 */
class m210417_020822_oddo_20210417_penyesuaiantrigger extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER "obatalkespasien_r_update" ON "public"."obatalkespasien_t";');

        $this->execute('CREATE TRIGGER "obatalkespasien_r_update" AFTER UPDATE OF "qty_konversi", "qty_oa", "obatsudahbayar_id", "is_deleted", "tglpelayanan", "det" ON "public"."obatalkespasien_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."obatalkespasien_r_update"();');

        $this->execute('COMMENT ON TRIGGER "obatalkespasien_r_update" ON "public"."obatalkespasien_t" IS \'rekap saleorder_line (UPDATE)\';');

        $this->execute('DROP TRIGGER "pasien_r_update" ON "public"."pasien_m";');

        $this->execute('CREATE TRIGGER "pasien_r_update" AFTER UPDATE OF "no_telepon_pasien", "alamat_pasien", "tanggal_lahir", "no_identitas_pasien", "jenisidentitas", "alamat_sekarang", "alamatemail", "no_mobile_pasien", "tempat_lahir", "jeniskelamin", "namadepan", "nama_pasien", "no_rekam_medik" ON "public"."pasien_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pasien_r_update"();');

        $this->execute('DROP TRIGGER "pendaftaran_r_update" ON "public"."pendaftaran_t";');

        $this->execute('CREATE TRIGGER "pendaftaran_r_update" AFTER UPDATE OF "pasien_id", "ruangan_id", "asuransipasien_id", "kelaspelayanan_id", "carabayar_id", "instalasi_id", "pegawai_id", "no_pendaftaran", "tgl_pendaftaran", "penjamin_id" ON "public"."pendaftaran_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pendaftaran_r_update"();');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210417_020822_oddo_20210417_penyesuaiantrigger cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210417_020822_oddo_20210417_penyesuaiantrigger cannot be reverted.\n";

        return false;
    }
    */
}
