<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pharmacy POS - Login</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-12 col-md-5">
				<div class="card shadow-sm">
					<div class="card-body">
						<h3 class="mb-4 text-center">Pharmacy POS</h3>
						<?php $error = session()->getFlashdata('error'); if ($error): ?>
							<div class="alert alert-danger" role="alert"><?php echo esc($error); ?></div>
						<?php endif; ?>
						<form action="<?php echo site_url('login'); ?>" method="post">
							<div class="mb-3">
								<label for="username" class="form-label">Username</label>
								<input type="text" class="form-control" id="username" name="username" value="<?php echo old('username'); ?>" required>
							</div>
							<div class="mb-3">
								<label for="password" class="form-label">Password</label>
								<input type="password" class="form-control" id="password" name="password" required>
							</div>
							<button type="submit" class="btn btn-primary w-100">Login</button>
						</form>
						<div class="text-muted small mt-3">Demo user: admin / password</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


