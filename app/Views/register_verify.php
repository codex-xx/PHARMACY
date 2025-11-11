<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Verify Account - Pharmacy POS</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-12 col-md-6 col-lg-5">
				<div class="card shadow-sm">
					<div class="card-body">
						<h3 class="mb-4 text-center">Verify Your Phone</h3>
						<?php $error = session()->getFlashdata('error'); if ($error): ?>
							<div class="alert alert-danger" role="alert"><?php echo esc($error); ?></div>
						<?php endif; ?>
						<?php $success = session()->getFlashdata('success'); if ($success): ?>
							<div class="alert alert-success" role="alert"><?php echo esc($success); ?></div>
						<?php endif; ?>
						<p>We've sent a verification code to <strong><?php echo esc($phone); ?></strong>. Enter it below to complete your registration.</p>
						<form action="<?php echo site_url('register/verify'); ?>" method="post">
							<div class="mb-3">
								<label for="code" class="form-label">Verification Code</label>
								<input type="text" class="form-control" id="code" name="code" pattern="\d{6}" maxlength="6" required>
							</div>
							<button type="submit" class="btn btn-primary w-100">Verify & Create Account</button>
							<div class="mt-3 text-center">
								<a href="<?php echo site_url('register'); ?>">Back to Register</a>
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
