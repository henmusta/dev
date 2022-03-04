<div class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="block">
				<div class="block-header">
					<h3 class="block-title">Data <?= $module['name'];?></h3>
					<div class="block-options">
				    	<input type="hidden" id="id_cabang" name="filter[id_cabang]" value="<?php echo $this->user->id_cabang;?>">
					</div>
				</div>
				<div class="block-content">
                <div class="col-md-3">
                <!-- <select id="cek_giro" style="width:100%" class="form-control select2-cek_giro" name="filter[cek_giro]" >  
                        <option value="2">Cair</option>
					    <option value="1">Belum Cair</option>	
				</select> -->
				<a href="javascript:history.back();" class="btn btn-outline-secondary"><i class="fa fa-reply"></i> Kembali</a>
                </div><br>
					<div class="table-responsive">
						<table id="dt" class="table table-sm table-vcenter table-bordered" width="100%">
							<thead>
								<tr>
									<th>Group</th>
									<th>Nomor</th>
									<th>Detail</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>