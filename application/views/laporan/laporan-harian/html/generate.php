

<div class="bg-body-light d-print-none">
<div class="block">
		<div class="block-content">
			<div class="container">
			<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
			<div class="row">
				<div class="col-sm">
                <?php echo form_open('',array('id'=>'formFilter')); ?>
							<label class="mr-2">Harian</label>
							<div class="input-group mr-2">
								<input type="text" class="form-control flatpickrhri" id="tglhari" name="filter[tglhari]" value="<?= date('Y-m-d')?>">
								<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
								<button type="submit" id="submitFilterhari" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
								<button id="excel_hari" name="excel_hari" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
							</div>
                <?php echo form_close(); ?>
				</div>
				<div class="col-sm">
                <?php echo form_open('',array('id'=>'formFilterbulan')); ?>
				<label class="mr-2">Bulanan</label>
							<div class="input-group mr-2">
								<input type="text" class="form-control flatpickrbln" id="tglbulan" name="filter[tglbulan]" value="<?= date('Y-m')?>">
								<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
								<button type="submit"  id="submitFilterbulan" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
								<button id="excel_bulan" name="excel_bulan" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
							</div>
                 <?php echo form_close(); ?>
				</div>
				<div class="col-sm">
                <?php echo form_open('',array('id'=>'formFiltertahun')); ?>
				<label class="mr-2">Tahunan</label>
							<div class="input-group mr-2">
								<input type="text" class="form-control flatpickrthn" id="tgltahun" name="filter[tgltahun]" value="<?= date('Y')?>">
								<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
								<button type="submit"  id="submitFiltertahun" class="btn btn-outline-primary"><i class="fa fa-print"></i> Cetak</button>
								<button id="excel_tahun" name="excel_tahun" type="button" class="btn btn-outline-primary"><i class="fas fa-download"></i>Cetak Excel</button>
							</div>
                <?php echo form_close(); ?>
				</div>
			</div>
			</div>
			
		</div>
	</div>
</div>
<!-- Page Content -->
<div class="content">
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title d-print-none" id="title">Pilih Filter Dahulu</h3>
            <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option"
                    data-action="fullscreen_toggle"><i class="si si-size-fullscreen"></i></button>
                <button type="button" class="btn-block-option" onclick="One.helpers('print');"><i
                        class="si si-printer mr-1"></i></button>
            </div>
        </div>
        <div class="block-content" id="report">


        </div>
    </div>
</div>

