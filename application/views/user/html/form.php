<div class="content">
	<div class="row">
		<div class="col-md-4">
			<div class="block">
				<div class="block-content">
					<form id="<?php echo $form_update['id'];?>" method="POST" autocomplete="off" action="" enctype="multipart/form-data">
						<div class="form-group">
							<label>Nama Lengkap</label>
							<input type="text" class="form-control" name="user[nama]" value="<?php echo $user['nama'];?>">
						</div>
						<div class="form-group">
							<label>Username</label>
							<input type="text" class="form-control" name="user[username]" value="<?php echo $user['username'];?>">
						</div>
						<div class="form-group">
							<label>Email</label>
							<input type="email" class="form-control" name="user[email]" value="<?php echo $user['email'];?>">
						</div>
						<div class="form-group">
							<label>Telp</label>
							<input class="form-control" name="user[telp]" value="<?php echo $user['telp'];?>">
						</div>
						<button type="submit" class="btn btn-block btn-outline-primary">Ubah</button>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="block">
				<div class="block-content">
					<form id="<?php echo $form_change_password['id'];?>" method="POST" autocomplete="off" action="" enctype="multipart/form-data">
						<div class="form-group">
							<label>Password Lama</label>
							<input type="password" class="form-control" name="old_password" value="">
						</div>
						<div class="form-group">
							<label>Password Baru</label>
							<input id="new_password" type="password" class="form-control" name="new_password" value="">
						</div>
						<div class="form-group">
							<label>Konfirmasi Passsword Baru</label>
							<input type="password" class="form-control" name="confirm_new_password" value="">
						</div>
						<button type="submit" class="btn btn-block btn-outline-primary">Ganti Passsword</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>