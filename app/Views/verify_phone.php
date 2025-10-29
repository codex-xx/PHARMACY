<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Verify Phone - Pharmacy POS</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-12 col-md-6 col-lg-4">
				<div class="card shadow-sm">
					<div class="card-body">
						<h3 class="mb-4 text-center">Verify Phone</h3>
						<?php $error = session()->getFlashdata('error'); if ($error): ?>
							<div class="alert alert-danger" role="alert"><?php echo esc($error); ?></div>
						<?php endif; ?>
						<?php $success = session()->getFlashdata('success'); if ($success): ?>
							<div class="alert alert-success" role="alert"><?php echo esc($success); ?></div>
						<?php endif; ?>
						<form action="<?php echo site_url('verify-phone'); ?>" method="post">
							<div class="mb-3">
								<label for="code" class="form-label">Verification Code</label>
								<input type="text" class="form-control" id="code" name="code" placeholder="Enter 6-digit code" required>
							</div>
							<button type="submit" class="btn btn-primary w-100">Verify</button>
							<div class="mt-3 text-center">
								<form action="<?php echo site_url('verify-phone/resend'); ?>" method="post" class="d-inline">
									<button type="submit" class="btn btn-link p-0">Resend code</button>
								</form>
								<span class="mx-2">|</span>
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


