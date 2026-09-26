<?php

use yii\db\Migration;

/**
 * Class m240808_052901_migrate_RPP1125_docmapping_k
 */
class m240808_052901_migrate_RPP1125_docmapping_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('UPDATE docmapping_k SET docbody_text = \'<div style="letter-spacing:2px">
<table align="right" border="0" cellpadding="1" cellspacing="1" style="letter-spacing:2px; width:200px">
	<tbody>
		<tr>
			<td colspan="3" style="text-align:right"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">#tgl_transaksi#</span></span></td>
		</tr>
		<tr>
			<td style="width:40%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">No. Kwi</span></span></td>
			<td style="width:1%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px"><strong>:</strong></span></span></td>
			<td style="width:69%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">#no_transaksi#</span></span></td>
		</tr>
	</tbody>
</table>

<p style="text-align:center"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">KWITANSI<strong>&nbsp;</strong>#jenis#</span></span></p>

<table border="0" cellpadding="1" cellspacing="1" style="letter-spacing:2px; overflow:wrap; page-break-inside:avoid; width:100%">
	<tbody>
		<tr>
			<td style="white-space:nowrap; width:30%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">Sudah Terima Dari<strong>&nbsp;</strong></span></span></td>
			<td style="width:1%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px"><strong>:</strong></span></span></td>
			<td style="width:69%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">&nbsp;#dari_kepada#</span></span></td>
		</tr>
	</tbody>
</table>

<table border="0" cellpadding="1" cellspacing="1" style="letter-spacing:2px; overflow:wrap; page-break-inside:avoid; width:100%">
	<tbody>
		<tr>
			<td style="width:100%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">&nbsp; &nbsp; &nbsp;</span></span></td>
		</tr>
	</tbody>
</table>

<table border="0" cellpadding="1" cellspacing="1" style="letter-spacing:2px; overflow:wrap; page-break-inside:avoid; width:100%">
	<tbody>
		<tr>
			<td style="white-space:nowrap; width:30%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">Untuk Pembayaran<strong>&nbsp;&nbsp;</strong></span></span></td>
			<td style="width:1%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px"><strong>:</strong></span></span></td>
			<td style="width:69%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">&nbsp;#kategoritransaksi_nama# -&nbsp;</span></span>Rp.&nbsp;#jumlah#</td>
		</tr>
	</tbody>
</table>

<table border="0" cellpadding="1" cellspacing="1" style="letter-spacing:2px; overflow:wrap; page-break-inside:avoid; width:100%">
	<tbody>
		<tr>
			<td style="width:100%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">&nbsp; &nbsp; </span></span></td>
		</tr>
	</tbody>
</table>

<table border="0" cellpadding="1" cellspacing="1" style="letter-spacing:2px; overflow:wrap; page-break-inside:avoid; width:100%">
	<tbody>
		<tr>
			<td style="white-space:nowrap; width:30%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">Terbilang<strong>&nbsp;&nbsp;</strong></span></span></td>
			<td style="width:1%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px"><strong>:</strong></span></span></td>
			<td style="width:69%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">&nbsp;#terbilang#</span></span></td>
		</tr>
	</tbody>
</table>

<table border="0" cellpadding="1" cellspacing="1" style="letter-spacing:2px; overflow:wrap; page-break-inside:avoid; width:100%">
	<tbody>
		<tr>
			<td style="width:100%"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">&nbsp; &nbsp; &nbsp;</span></span></td>
		</tr>
	</tbody>
</table>

<p style="text-align:center">&nbsp;</p>

<table align="right" border="0" cellpadding="1" cellspacing="1" style="letter-spacing:2px; width:35%">
	<tbody>
		<tr>
			<td style="text-align:center"><span style="font-family:Arial,Helvetica,sans-serif"><span style="font-size:14px">#tanggal_sekarang#</span></span></td>
		</tr>
		<tr>
			<td>
			<p>&nbsp;</p>

			<p>&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td>
			<p style="text-align:center">&nbsp;</p>
			</td>
		</tr>
		<tr>
			<td style="text-align:center"><span style="font-size:14px"><span style="font-family:Arial,Helvetica,sans-serif">#created_by_nama#</span></span></td>
		</tr>
	</tbody>
</table>

<p style="text-align:center">&nbsp;</p>

<p style="text-align:center">&nbsp;</p>

<p style="text-align:center">&nbsp;</p>
</div>
\'
WHERE kode_doc = \'kwitansi-transaksi\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240808_052901_migrate_RPP1125_docmapping_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240808_052901_migrate_RPP1125_docmapping_k cannot be reverted.\n";

        return false;
    }
    */
}
