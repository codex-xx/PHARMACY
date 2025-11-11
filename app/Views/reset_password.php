<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Reset Password - Pharmacy POS</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-12 col-md-6 col-lg-4">
				<div class="card shadow-sm">
					<div class="card-body">
					<h3 class="mb-4 text-center">Reset Password</h3>
					<?php $error = session()->getFlashdata('error'); if ($error): ?>
						<div class="alert alert-danger" role="alert"><?php echo esc($error); ?></div>
					<?php endif; ?>
					<form action="<?php echo site_url('reset-password'); ?>" method="post">
						<div class="mb-3">
							<label for="password" class="form-label">New Password</label>
							<input type="password" class="form-control" id="password" name="password" required>
						</div>
						<div class="mb-3">
							<label for="password_confirm" class="form-label">Confirm Password</label>
							<input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
						</div>
						<button type="submit" class="btn btn-primary w-100">Update Password</button>
						<div class="mt-3 text-center">
							<a href="<?php echo site_url('login'); ?>">Back to Login</a>
						</div>
					</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


