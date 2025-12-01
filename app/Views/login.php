<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pharmacy POS - Login</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<style>
	body {
			background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
			min-height: 100vh;
			display: flex;
			align-items: center;
		}
		.pharmacy-card {
			border: none;
			border-radius: 15px;
			box-shadow: 0 8px 32px rgba(0,0,0,0.1);
			transition: transform 0.3s ease, box-shadow 0.3s ease;
		}
		.pharmacy-card:hover {
			transform: translateY(-3px);
			box-shadow: 0 12px 40px rgba(0,0,0,0.15);
		}
		.btn-pharmacy {
			background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
			border: none;
			color: white;
			border-radius: 25px;
			padding: 10px 20px;
			font-weight: 500;
			transition: all 0.3s ease;
		}
		.btn-pharmacy:hover {
			background: linear-gradient(135deg, #229954 0%, #28a745 100%);
			transform: scale(1.05);
		}
		.form-control:focus {
			border-color: #28a745;
			box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-md-6 col-lg-5">
				<div class="text-center mb-4">
					<i class="fas fa-pills fa-4x text-success mb-3"></i>
					<h1 class="text-success fw-bold">Pharmacy POS</h1>
					<p class="text-muted">Welcome back! Please sign in to continue.</p>
				</div>
				<div class="card pharmacy-card">
					<div class="card-body p-4">
						<h4 class="mb-4 text-center fw-bold text-success">
							<i class="fas fa-sign-in-alt me-2"></i>Login to Your Account
						</h4>
						<?php $error = session()->getFlashdata('error'); if ($error): ?>
							<div class="alert alert-danger" role="alert">
								<i class="fas fa-exclamation-triangle me-2"></i><?php echo esc($error); ?>
							</div>
						<?php endif; ?>
						<form action="<?php echo site_url('login'); ?>" method="post">
							<div class="mb-3">
								<label for="username" class="form-label fw-semibold">
									<i class="fas fa-user me-2 text-success"></i>Username
								</label>
								<input type="text" class="form-control form-control-lg" id="username" name="username" value="<?php echo old('username'); ?>" required>
							</div>
							<div class="mb-3">
								<label for="password" class="form-label fw-semibold">
									<i class="fas fa-lock me-2 text-success"></i>Password
								</label>
								<input type="password" class="form-control form-control-lg" id="password" name="password" required>
							</div>
							<button type="submit" class="btn btn-pharmacy btn-lg w-100">
								<i class="fas fa-sign-in-alt me-2"></i>Login
							</button>
						</form>
						<div class="d-flex justify-content-between mt-3">
							<a href="<?php echo site_url('register'); ?>" class="text-decoration-none">
								<i class="fas fa-user-plus me-1"></i>Create account
							</a>
							<a href="<?php echo site_url('forgot-password'); ?>" class="text-decoration-none">
								<i class="fas fa-question-circle me-1"></i>Forgot password?
							</a>
						</div>
						<div class="alert alert-info small mt-3" role="alert">
							<i class="fas fa-info-circle me-2"></i>Demo user: admin / password
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
