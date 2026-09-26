<?php

use yii\db\Migration;

/**
 * Class m201208_081317_improve_reseptempdetail_add_column
 */
class m201208_081317_improve_reseptempdetail_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."reseptempdetail_m" ADD IF NOT EXISTS "signa" json;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."reseptempdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."reseptempdetail_v" AS  SELECT reseptemp_m.reseptemp_id,
                reseptemp_m.reseptemp_nama,
                reseptemp_m.dokter_id,
                pegawai_m.nama_pegawai,
                reseptempdetail_m.racikan_id,
                racikan_m.racikan_nama,
                reseptempdetail_m.rke,
                reseptempdetail_m.obatalkes_id,
                obatalkes_m.obatalkes_nama,
                obatalkes_m.hargajual,
                reseptempdetail_m.satuankecil_id,
                satuanunit_m.satuanunit_nama,
                reseptempdetail_m.qty,
                reseptempdetail_m.signa_id,
                ((reseptempdetail_m.additional_data)::json ->> \'satuaninput_id\'::text) AS satuaninput_id,
                ((reseptempdetail_m.additional_data)::json ->> \'satuan_input\'::text) AS satuan_input,
                ((reseptempdetail_m.additional_data)::json ->> \'satuankonversi_id\'::text) AS satuankonversi_id,
                ((reseptempdetail_m.additional_data)::json ->> \'satuan_konversi\'::text) AS satuan_konversi,
                ((reseptempdetail_m.additional_data)::json ->> \'harga_konversi\'::text) AS harga_konversi,
                ((reseptempdetail_m.additional_data)::json ->> \'harga_jual\'::text) AS harga_jual,
                ((reseptempdetail_m.additional_data)::json ->> \'nilai_konversi\'::text) AS nilai_konversi,
                ((reseptempdetail_m.additional_data)::json ->> \'etiket\'::text) AS etiket,
                    CASE COALESCE(signaobat_m.signa_id,0)
                        WHEN 0 THEN ((reseptempdetail_m.signa)::json ->> \'text\'::text) 
                        ELSE signaobat_m.signa_nama
                    END AS signa
               FROM ((((((reseptemp_m
                 JOIN reseptempdetail_m ON ((reseptemp_m.reseptemp_id = reseptempdetail_m.reseptemp_id)))
                 JOIN pegawai_m ON ((reseptemp_m.dokter_id = pegawai_m.pegawai_id)))
                 JOIN racikan_m ON ((reseptempdetail_m.racikan_id = racikan_m.racikan_id)))
                 JOIN obatalkes_m ON ((reseptempdetail_m.obatalkes_id = obatalkes_m.obatalkes_id)))
                 JOIN satuanunit_m ON ((reseptempdetail_m.satuankecil_id = satuanunit_m.satuanunit_id)))
                 LEFT JOIN signaobat_m ON ((reseptempdetail_m.signa_id = signaobat_m.signa_id)));
        ');

        



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201208_081317_improve_reseptempdetail_add_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201208_081317_improve_reseptempdetail_add_column cannot be reverted.\n";

        return false;
    }
    */
}
