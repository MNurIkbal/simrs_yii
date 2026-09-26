<?php

use yii\db\Migration;

/**
 * Class m200227_033536_migratetable_20200227
 */
class m200227_033536_migratetable_20200227 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('COMMENT ON COLUMN "public"."pembayaran_t"."total_tagihan" IS \'total tindakan + obat\';');
        $this->execute('COMMENT ON COLUMN "public"."pembayaran_t"."total_dibayar" IS \'total_input + uang_muka + piutang\';');
        $this->execute('COMMENT ON COLUMN "public"."pembayaran_t"."total_sisatagihan" IS \'nominal Pemberian piutang\';');
        $this->execute('COMMENT ON COLUMN "public"."pembayaran_t"."total_ditagihkan" IS \'total(tindakan + obat) + biaya adm - penjamin\';');
        $this->execute('ALTER TABLE "public"."pendaftaran_t" ALTER COLUMN "status_konfirmasi" SET DEFAULT 664;');
        $this->execute("COMMENT ON COLUMN \"public\".\"pendaftaran_t\".\"status_konfirmasi\" IS 'lookup_type=''status_konfirmasirm''';");
        $this->execute('ALTER TABLE "public"."returbayarpelayanan_t" ADD COLUMN "total_nontunai" float8 DEFAULT 0;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200227_033536_migratetable_20200227 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_033536_migratetable_20200227 cannot be reverted.\n";

        return false;
    }
    */
}
