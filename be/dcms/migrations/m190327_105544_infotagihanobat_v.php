<?php

use yii\db\Migration;

/**
 * Class m190327_105544_infotagihanobat_v
 */
class m190327_105544_infotagihanobat_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW infotagihanobat_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW infotagihanobat_v AS 
             SELECT penjualanresep_t.penjualanresep_id,
                penjualanresep_t.tglpenjualan,
                jenispenjualan.lookup_name AS jenis_penjualan,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                penjualanresep_t.noresep,
                    CASE
                        WHEN pasien_m.nama_pasien IS NOT NULL THEN pasien_m.nama_pasien::text
                        WHEN penjualanresep_t.nama_pembeli IS NOT NULL THEN penjualanresep_t.nama_pembeli::text
                        WHEN karyawan.nama_pegawai IS NOT NULL THEN karyawan.nama_pegawai::text
                        ELSE \'\'::text
                    END AS nama_pembeli,
                penjualanresep_t.carabayar_id,
                carabayar_m.carabayar_nama,
                penjualanresep_t.penjamin_id,
                penjamin_m.penjamin_nama,
                penjualanresep_t.totharganetto AS totalharga_netto,
                pasien_m.no_rekam_medik,
                penjualanresep_t.jenispenjualan,
                penjualanresep_t.status_bayar,
                stat_bayar.lookup_name AS status_bayar_nama,
                COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) AS jasa,
                COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS administrasi,
                COALESCE(penjualanresep_t.totalhargajual, 0::double precision) AS obat,
                COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totalharga_jual
               FROM penjualanresep_t
                 JOIN lookup_m jenispenjualan ON penjualanresep_t.jenispenjualan::integer = jenispenjualan.lookup_id
                 LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN pegawai_m karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
                 JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
                 JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN lookup_m stat_bayar ON penjualanresep_t.status_bayar = stat_bayar.lookup_id
              WHERE penjualanresep_t.is_active = true AND penjualanresep_t.is_deleted = false;
        ');

        $this->execute('
            ALTER TABLE infotagihanobat_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_105544_infotagihanobat_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_105544_infotagihanobat_v cannot be reverted.\n";

        return false;
    }
    */
}
