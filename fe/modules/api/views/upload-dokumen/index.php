<?php

use app\components\DocoHelpers;
use app\components\DocoConstants;
?>
<?php if (!empty($is_modal)) : ?>
	<div class="modal-header bg-inverse">
		<button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
		<h5 class="modal-title">Dokumen Pasien</h5>
	</div>
	<div class="modal-body">
	<?php endif; ?>
	<?=
	\app\components\widgets\InputFile::widget([
		'model' => $model,
		'data'	=> $data,
		'dokumen' => $dokumen,
		'dokumen_eklaim' => $dokumen_eklaim,
		'parent_id' => $parent_id,
        'pendaftaran_id' => $pendaftaran_id_encrypted,
		'is_pasienid' => $is_pasienid,
        'isDisabled' => $groupCaraBayarId == DocoConstants::GROUP_BPJS ? false : true,
        'isHide' => $isHide,
	]);
	?>
	<?php if (!empty($is_modal)) : ?>
	</div>
<?php endif; ?>
<?php
