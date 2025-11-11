<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register - Pharmacy POS</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-12 col-md-6 col-lg-5">
				<div class="card shadow-sm">
					<div class="card-body">
						<h3 class="mb-4 text-center">Create Account</h3>
						<?php $error = session()->getFlashdata('error'); if ($error): ?>
							<div class="alert alert-danger" role="alert"><?php echo esc($error); ?></div>
						<?php endif; ?>
						<?php $success = session()->getFlashdata('success'); if ($success): ?>
							<div class="alert alert-success" role="alert"><?php echo esc($success); ?></div>
						<?php endif; ?>
						<form action="<?php echo site_url('register'); ?>" method="post">
							<div class="mb-3">
								<label for="phone" class="form-label">Phone Number</label>
								<input type="tel" pattern="0[0-9]{10}" class="form-control" id="phone" name="phone" value="<?php echo old('phone'); ?>" placeholder="" required>
								<div class="form-text">Enter 11-digit Philippines mobile number starting with 09.</div>
							</div>
							<div class="mb-3">
								<label for="username" class="form-label">Username</label>
								<input type="text" class="form-control" id="username" name="username" value="<?php echo old('username'); ?>" required>
							</div>
							<div class="mb-3">
								<label for="email" class="form-label">Email</label>
								<input type="email" class="form-control" id="email" name="email" value="<?php echo old('email'); ?>" required>
							</div>
							<div class="mb-3">
								<label for="password" class="form-label">Password</label>
								<input type="password" class="form-control" id="password" name="password" required>
							</div>
							<div class="mb-3">
								<label for="password_confirm" class="form-label">Confirm Password</label>
								<input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
							</div>
							<button type="submit" class="btn btn-primary w-100">Register</button>
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


