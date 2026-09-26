<?php

use yii\db\Migration;

/**
 * Class m201020_065704_oddo_trigger_20201020
 */
class m201020_065704_oddo_trigger_20201020 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TRIGGER "adjusmenobatkeluar_r_insert" AFTER INSERT ON "public"."adjusmenobatkeluar_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."adjusmenobatkeluar_r_insert"();');
        $this->execute('COMMENT ON TRIGGER "adjusmenobatkeluar_r_insert" ON "public"."adjusmenobatkeluar_t" IS \'rekap stockscrap (INSERT)\';');

        $this->execute('CREATE TRIGGER "adjusmenobatmasuk_r_insert" AFTER INSERT ON "public"."adjusmenobatmasuk_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."adjusmenobatmasuk_r_insert"();');
        $this->execute('COMMENT ON TRIGGER "adjusmenobatmasuk_r_insert" ON "public"."adjusmenobatmasuk_t" IS \'rekap stockscrap (INSERT)\';');

        $this->execute('CREATE TRIGGER "bayaruangmuka_r_insert" AFTER INSERT ON "public"."bayaruangmuka_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."bayaruangmuka_r_insert"();');

        $this->execute('CREATE TRIGGER "obatalkes_r_insert" AFTER INSERT ON "public"."obatalkes_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."obatalkes_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "obatalkes_r_insert" ON "public"."obatalkes_m" IS \'rekap int_obat_v (INSERT)\';');
        
        $this->execute('CREATE TRIGGER "obatalkes_r_update" AFTER UPDATE ON "public"."obatalkes_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."obatalkes_r_update"();');

        $this->execute('COMMENT ON TRIGGER "obatalkes_r_update" ON "public"."obatalkes_m" IS \'rekap int_obat_v (UPDATE)\';');

        $this->execute('CREATE TRIGGER "generate_no_obatalkespasien" BEFORE INSERT ON "public"."obatalkespasien_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."generate_obatalkespasien"();');

        $this->execute('CREATE TRIGGER "int_obatalkespasien_r_insert" AFTER INSERT ON "public"."obatalkespasien_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."int_obatalkespasien_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "int_obatalkespasien_r_insert" ON "public"."obatalkespasien_t" IS \'rekap stokout_detail (INSERT)\';');

        $this->execute('CREATE TRIGGER "obatalkespasien_r_insert" AFTER INSERT ON "public"."obatalkespasien_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."obatalkespasien_r_insert"();
');
        $this->execute('COMMENT ON TRIGGER "obatalkespasien_r_insert" ON "public"."obatalkespasien_t" IS \'rekap saleorder_line (INSERT)\';');

        $this->execute('CREATE TRIGGER "obatalkespasien_r_update" AFTER UPDATE ON "public"."obatalkespasien_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."obatalkespasien_r_update"();');
        $this->execute('COMMENT ON TRIGGER "obatalkespasien_r_update" ON "public"."obatalkespasien_t" IS \'rekap saleorder_line (UPDATE)\';');

        $this->execute('CREATE TRIGGER "pembatalanresepdetail_r_insert" AFTER INSERT ON "public"."obatalkespasien_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembatalanresepdetail_r_insert"();');
        $this->execute('COMMENT ON TRIGGER "pembatalanresepdetail_r_insert" ON "public"."obatalkespasien_t" IS \'rekap stokreturndetail (INSERT)\';');

        $this->execute('CREATE TRIGGER "insert_obatalkespasien_t" BEFORE INSERT ON "public"."obatsudahbayar_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."obatsudahbayar_t_insert"();');

        $this->execute('CREATE TRIGGER "obatsudahbayar_t_cancel" AFTER UPDATE ON "public"."obatsudahbayar_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."obatsudahbayar_t_cancel"();');

        $this->execute('CREATE TRIGGER "pasien_r_insert" AFTER INSERT ON "public"."pasien_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pasien_r_insert"();');

        $this->execute('CREATE TRIGGER "pasien_r_update" AFTER UPDATE ON "public"."pasien_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pasien_r_update"();');

        $this->execute('CREATE TRIGGER "pasienadmisi_r_insert" AFTER INSERT ON "public"."pasienadmisi_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pasienadmisi_r_insert"();');

        $this->execute('CREATE TRIGGER "pasienadmisi_r_update" AFTER UPDATE ON "public"."pasienadmisi_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pasienadmisi_r_update"();');

        $this->execute('CREATE TRIGGER "pemakaianobatdetail_r_insert" AFTER INSERT ON "public"."pemakaianobatdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pemakaianobatdetail_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "pemakaianobatdetail_r_insert" ON "public"."pemakaianobatdetail_t" IS \'rekap stockscrap (INSERT)\';');

        $this->execute('CREATE TRIGGER "pemakaianuangmuka_r_insert" AFTER INSERT ON "public"."pemakaianuangmuka_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pemakaianuangmuka_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "pemakaianuangmuka_r_insert" ON "public"."pemakaianuangmuka_t" IS \'rekap int_uangmuka_v\';');

        $this->execute('CREATE TRIGGER "pembatalanresep_r_insert" AFTER INSERT ON "public"."pembatalanresep_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembatalanresep_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "pembatalanresep_r_insert" ON "public"."pembatalanresep_t" IS \'rekap ke stockreturn (insert)\';');

        $this->execute('CREATE TRIGGER "pembayaran_r_insert" AFTER INSERT ON "public"."pembayaran_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembayaran_r_insert"();');

        $this->execute('CREATE TRIGGER "pembayaran_r_update" AFTER UPDATE ON "public"."pembayaran_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembayaran_r_update"();');

        $this->execute('
ALTER TABLE "public"."pembayaran_t" DISABLE TRIGGER "pembayaran_r_update";');

        $this->execute('CREATE TRIGGER "pembulatan_diskon_adm_insert" AFTER INSERT ON "public"."pembayaran_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembulatan_diskon_insert"();
');
        $this->execute('COMMENT ON TRIGGER "pembulatan_diskon_adm_insert" ON "public"."pembayaran_t" IS \'insert ke table tindakanpelayanan_r\';');

        $this->execute('CREATE TRIGGER "pembayaran_r_nontunai" BEFORE INSERT ON "public"."pembayaranmetode_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembayaran_r_nontunai"();');

        $this->execute('COMMENT ON TRIGGER "pembayaran_r_nontunai" ON "public"."pembayaranmetode_t" IS \'insert ke pembayaran_r (case non tunai)\';');

        $this->execute('CREATE TRIGGER "pembayaran_r_nontunai_batal" AFTER UPDATE ON "public"."pembayaranmetode_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembayaran_r_nontunai_batal"();');

        $this->execute('COMMENT ON TRIGGER "pembayaran_r_nontunai_batal" ON "public"."pembayaranmetode_t" IS \'insert ke pembayaran_r (case non tunai) - batal\';');

        $this->execute('CREATE TRIGGER "pembayaran_r_delete" AFTER UPDATE ON "public"."pembayaranpelayanan_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembayaran_r_delete"();');

        $this->execute('COMMENT ON TRIGGER "pembayaran_r_delete" ON "public"."pembayaranpelayanan_t" IS \'insert ke table pembayaran_r (pembatalan bill)\';');

        $this->execute('CREATE TRIGGER "pemusnahanobatdetail_r_insert" AFTER INSERT ON "public"."pemusnahanobatdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pemusnahanobatdetail_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "pemusnahanobatdetail_r_insert" ON "public"."pemusnahanobatdetail_t" IS \'rekap stockscrap (INSERT)\';');

        $this->execute('CREATE TRIGGER "int_pendaftaranbmhp_r_insert" AFTER INSERT ON "public"."pendaftaran_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."int_pendaftaranbmhp_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "int_pendaftaranbmhp_r_insert" ON "public"."pendaftaran_t" IS \'rekap stockout_header (INSERT)\';');

        $this->execute('CREATE TRIGGER "pendaftaran_r_insert" AFTER INSERT ON "public"."pendaftaran_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pendaftaran_r_insert"();');

        $this->execute('CREATE TRIGGER "pendaftaran_r_update" AFTER UPDATE ON "public"."pendaftaran_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pendaftaran_r_update"();');

        $this->execute('CREATE TRIGGER "penerimaanobat_r_insert" AFTER INSERT ON "public"."penerimaanobat_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaanobat_r_insert"();');

        $this->execute('CREATE TRIGGER "penerimaanobat_r_update" AFTER UPDATE ON "public"."penerimaanobat_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaanobat_r_insert"();');

        $this->execute('CREATE TRIGGER "penerimaanobatdetail_r_insert" AFTER INSERT ON "public"."penerimaanobatdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaanobatdetail_r_insert"();');

        $this->execute('CREATE TRIGGER "penerimaanobatdetail_r_update" AFTER UPDATE ON "public"."penerimaanobatdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaanobatdetail_r_update"();');

        $this->execute('ALTER TABLE "public"."penerimaanobatdetail_t" DISABLE TRIGGER "penerimaanobatdetail_r_update";');
        $this->execute('CREATE TRIGGER "penerimaansupp_r_insert" AFTER INSERT ON "public"."penerimaansupp_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaansupp_r_insert"();');

        $this->execute('CREATE TRIGGER "penerimaansupp_r_update" AFTER UPDATE ON "public"."penerimaansupp_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaansupp_r_insert"();');

        $this->execute('CREATE TRIGGER "penerimaansuppdetail_r_insert" AFTER INSERT ON "public"."penerimaansuppdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaansuppdetail_r_insert"();');

        $this->execute('CREATE TRIGGER "pengembalianuangmuka_r_insert" AFTER INSERT ON "public"."pengembalianuangmuka_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pengembalianuangmuka_r_insert"();');

        $this->execute('CREATE TRIGGER "int_penjualanresep_r_insert" AFTER INSERT ON "public"."penjualanresep_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."int_penjualanresep_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "int_penjualanresep_r_insert" ON "public"."penjualanresep_t" IS \'rekap stokout_header (INSERT)\';');

        $this->execute('CREATE TRIGGER "returpenerimaanobat_r_insert" AFTER INSERT ON "public"."returpenerimaanobat_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."returpenerimaanobat_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "returpenerimaanobat_r_insert" ON "public"."returpenerimaanobat_t" IS \'insert ke table rekap returpenerimaanobat_r (INSERT)\';');

        $this->execute('CREATE TRIGGER "returpenerimaanobatdetail_r_insert" AFTER INSERT ON "public"."returpenerimaanobatdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."returpenerimaanobatdetail_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "returpenerimaanobatdetail_r_insert" ON "public"."returpenerimaanobatdetail_t" IS \'rekap returpenerimaanobatdetail_r (INSERT)\';');

        $this->execute('CREATE TRIGGER "returresep_r_insert" AFTER INSERT ON "public"."returresep_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."returresep_r_insert"();');

        $this->execute('COMMENT ON TRIGGER "returresep_r_insert" ON "public"."returresep_t" IS \'rekap stockreturn (INSERT)\';');
        $this->execute('CREATE TRIGGER "returresepdetail_r_insert" AFTER INSERT ON "public"."returresepdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."returresepdetail_r_insert"();');
        $this->execute('COMMENT ON TRIGGER "returresepdetail_r_insert" ON "public"."returresepdetail_t" IS \'rekap stockreturndetail (INSERT)\';');
        $this->execute('CREATE TRIGGER "satuanunit_r_insert" AFTER INSERT ON "public"."satuanunit_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."satuanunit_r_insert"();');
        $this->execute('COMMENT ON TRIGGER "satuanunit_r_insert" ON "public"."satuanunit_m" IS \'rekap int_satuanunit_v (INSERT)\';');
        $this->execute('CREATE TRIGGER "satuanunit_r_update" AFTER UPDATE ON "public"."satuanunit_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."satuanunit_r_update"();');
        $this->execute('COMMENT ON TRIGGER "satuanunit_r_update" ON "public"."satuanunit_m" IS \'rekap int_satuanunit_v (UPDATE)\';');
        $this->execute('CREATE TRIGGER "supplier_r_insert" AFTER INSERT ON "public"."supplier_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."supplier_r_insert"();
');
        $this->execute('COMMENT ON TRIGGER "supplier_r_insert" ON "public"."supplier_m" IS \'rekap int_supplier_v (INSERT)\';');
        $this->execute('CREATE TRIGGER "supplier_r_update" AFTER UPDATE ON "public"."supplier_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."supplier_r_update"();');

        $this->execute('COMMENT ON TRIGGER "supplier_r_update" ON "public"."supplier_m" IS \'rekap int_supplier_v (UPDATE)\';');
        $this->execute('CREATE TRIGGER "tindakankomponen_r_insert" AFTER INSERT ON "public"."tindakankomponen_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tindakankomponen_r_insert"();');

        $this->execute('CREATE TRIGGER "tindakankomponen_r_update" AFTER UPDATE ON "public"."tindakankomponen_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tindakankomponen_r_update"();');

        $this->execute('CREATE TRIGGER "generate_no_tindakanpelayanan" BEFORE INSERT ON "public"."tindakanpelayanan_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."generate_tindakanpelayanan"();');

        $this->execute('CREATE TRIGGER "tindakanpelayanan_update" AFTER UPDATE ON "public"."tindakanpelayanan_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tindakanpelayanan_r_update"();');

        $this->execute('CREATE TRIGGER "insert_tindakanpelayanan_t" BEFORE INSERT ON "public"."tindakansudahbayar_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tindakansudahbayar_t_insert"();');

        $this->execute('CREATE TRIGGER "tindakansudahbayar_t_cancel" AFTER UPDATE ON "public"."tindakansudahbayar_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."tindakansudahbayar_t_cancel"();');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201020_065704_oddo_trigger_20201020 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201020_065704_oddo_trigger_20201020 cannot be reverted.\n";

        return false;
    }
    */
}
