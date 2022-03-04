<div class="content">
    <div class="row row-deck">
        <div class="col-sm-6 col-xl-3">
            <div class="block block-rounded d-flex flex-column">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="font-size-h2 font-w700"><?= $cards->pemasok;?></dt>
                        <dd class="text-muted mb-0">Total Supplier</dd>
                    </dl>
                    <div class="item item-rounded bg-body">
				    	<i class="fa fa-users font-size-h3 text-primary"></i>
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                    <a class="font-w500 d-flex align-items-center" href="<?php echo base_url()?>/master/pemasok">
                        Lihat
                        <i class="fa fa-arrow-alt-circle-right ml-1 opacity-25 font-size-base"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-rounded d-flex flex-column">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="font-size-h2 font-w700"><?= $cards->pelanggan;?></dt>
                        <dd class="text-muted mb-0">Total Pelanggan</dd>
                    </dl>
                    <div class="item item-rounded bg-body">
                        <i class="fa fa-users font-size-h3 text-primary"></i>
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                    <a class="font-w500 d-flex align-items-center" href="<?php echo base_url()?>/master/pelanggan">
                        Lihat
                        <i class="fa fa-arrow-alt-circle-right ml-1 opacity-25 font-size-base"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-rounded d-flex flex-column">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="font-size-h2 font-w700"><?= $cards->barang;?></dt>
                        <dd class="text-muted mb-0">Total Barang</dd>
                    </dl>
                    <div class="item item-rounded bg-body">
                        <i class="fa fa-inbox font-size-h3 text-primary"></i>
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                    <a class="font-w500 d-flex align-items-center" href="<?php echo base_url()?>/master/produk">
                        Lihat
                        <i class="fa fa-arrow-alt-circle-right ml-1 opacity-25 font-size-base"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="block block-rounded d-flex flex-column">
                <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                    <dl class="mb-0">
                        <dt class="font-size-h2 font-w700">Rp. <?= number_format($cards->penjualan);?></dt>
                        <dd class="text-muted mb-0">Penjualan Hari Ini</dd>
                    </dl>
                    <div class="item item-rounded bg-body">
                        <i class="fa fa-chart-line font-size-h3 text-primary"></i>
                    </div>
                </div>
                <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                    <a class="font-w500 d-flex align-items-center" href="<?php echo base_url()?>/penjualan/faktur">
                        Lihat
                        <i class="fa fa-arrow-alt-circle-right ml-1 opacity-25 font-size-base"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-7 d-flex flex-column">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Daftar Stok Kosong</h3>
                  
                </div>
                <div class="block-content block-content-default">
			<div class="table-responsive">
                <table  id="stok" class="table table-borderless table-striped table-vcenter">
                    <thead>
                            <tr>
								<th>Kode</th>
								<th>Nama Barang</th>
								<th>Pemasok</th>
                                <th>qty</th>
							</tr>
                    </thead>   
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
            </div>
        
            </div>
        </div>
        <div class="col-xl-5 d-flex flex-column">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Daftar Top Pelanggan</h3>
                  
                </div>
                <div class="block-content block-content-default">
			<div class="table-responsive">
                <table  id="pelanggan" class="table table-borderless table-striped table-vcenter">
                    <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Pembelian</th>
							</tr>
                    </thead>   
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
            </div>
        
            </div>
        </div>
    </div>
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Grafik Penjualan</h3>
            <div class="block-options">
              <input type="text" class="form-control flatpickr" name="filter[tgl]" value="<?= date('Y-m')?>">
            </div>
        </div>
      
        <div class="block-content">
          <div id="graph"></div> 
        </div>
    </div>
</div>