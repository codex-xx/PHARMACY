<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Settings - Pharmacy POS</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<style>
		[data-bs-theme="dark"] {
			--bs-body-color: #fff;
			--bs-body-bg: #212529;
		}
		[data-bs-theme="dark"] .card {
			background-color: #343a40;
			border-color: #495057;
		}
		[data-bs-theme="dark"] .form-control,
		[data-bs-theme="dark"] .form-select {
			background-color: #343a40;
			border-color: #495057;
			color: #fff;
		}
		[data-bs-theme="dark"] .form-control:focus,
		[data-bs-theme="dark"] .form-select:focus {
			background-color: #343a40;
			color: #fff;
		}
		body {
			transition: background-color 0.3s ease, color 0.3s ease;
		}
	</style>
</head>
<body data-bs-theme="<?php echo $currentTheme; ?>">
	<?php echo view('partials/sidebar', ['currentPage' => 'settings', 'role' => $user['role'] ?? '', 'username' => $user['username'] ?? '']); ?>

	<div class="main-content">
		<div class="container py-4">
			<div class="row">
				<div class="col-12">
					<h2 class="mb-4">
						<i class="fas fa-cog me-2"></i>Settings
					</h2>
				</div>
			</div>

			<?php if (session()->getFlashdata('success')): ?>
				<div class="alert alert-success alert-dismissible fade show">
					<i class="fas fa-check-circle me-2"></i><?php echo session()->getFlashdata('success'); ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			<?php endif; ?>

			<?php if (session()->getFlashdata('errors')): ?>
				<?php $errors = session()->getFlashdata('errors'); ?>
				<div class="alert alert-danger alert-dismissible fade show">
					<i class="fas fa-exclamation-triangle me-2"></i>
					<?php if (is_array($errors)): ?>
						<ul class="mb-0">
							<?php foreach ($errors as $error): ?>
								<li><?php echo esc($error); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<?php echo esc($errors); ?>
					<?php endif; ?>
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			<?php endif; ?>

			<div class="row g-4">
				<!-- Theme Settings -->
				<div class="col-lg-6">
					<div class="card">
						<div class="card-header bg-primary text-white">
							<h5 class="mb-0">
								<i class="fas fa-palette me-2"></i>Appearance
							</h5>
						</div>
						<div class="card-body">
							<h6 class="mb-3">Theme Preferences</h6>
							<div class="row">
								<div class="col-sm-6 mb-3">
									<div class="card theme-option" data-theme="light" style="cursor: pointer;">
										<div class="card-body text-center py-4">
											<i class="fas fa-sun fa-2x text-warning mb-2"></i>
											<div>Light Mode</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6 mb-3">
									<div class="card theme-option" data-theme="dark" style="cursor: pointer;">
										<div class="card-body text-center py-4 bg-dark text-white">
											<i class="fas fa-moon fa-2x text-info mb-2"></i>
											<div>Dark Mode</div>
										</div>
									</div>
								</div>
							</div>
							<div class="form-check mb-3">
								<input class="form-check-input" type="checkbox" id="autoTheme" onchange="toggleAutoTheme(this.checked)">
								<label class="form-check-label" for="autoTheme">
									Auto theme (based on system preference)
								</label>
							</div>
						</div>
					</div>
				</div>

				<!-- Profile Settings -->
				<div class="col-lg-6">
					<div class="card">
						<div class="card-header bg-success text-white">
							<h5 class="mb-0">
								<i class="fas fa-user me-2"></i>Profile Settings
							</h5>
						</div>
						<div class="card-body">
							<form action="<?php echo site_url('settings/profile'); ?>" method="post">
								<div class="mb-3">
									<label for="username" class="form-label">
										<i class="fas fa-user me-1"></i>Username
									</label>
									<input type="text" class="form-control" id="username" name="username"
										   value="<?php echo esc($user['username'] ?? ''); ?>" required>
								</div>
								<div class="mb-3">
									<label for="phone" class="form-label">
										<i class="fas fa-mobile-alt me-1"></i>Phone Number
									</label>
									<input type="tel" class="form-control" id="phone" name="phone"
										   value="<?php echo esc($user['phone'] ?? ''); ?>" required>
								</div>
								<div class="mb-3">
									<label for="current_password" class="form-label">
										<i class="fas fa-lock me-1"></i>Current Password
									</label>
									<input type="password" class="form-control" id="current_password" name="current_password" required>
									<small class="form-text text-muted">Required to save changes</small>
								</div>
								<div class="mb-3">
									<label for="password" class="form-label">
										<i class="fas fa-key me-1"></i>New Password (Optional)
									</label>
									<input type="password" class="form-control" id="password" name="password">
									<small class="form-text text-muted">Leave empty to keep current password</small>
								</div>
								<div class="mb-3">
									<label for="password_confirm" class="form-label">
										<i class="fas fa-key me-1"></i>Confirm New Password
									</label>
									<input type="password" class="form-control" id="password_confirm" name="password_confirm">
								</div>
								<button type="submit" class="btn btn-success">
									<i class="fas fa-save me-2"></i>Save Profile
								</button>
							</form>
						</div>
					</div>
				</div>
			</div>

			<!-- Account Information -->
			<div class="row mt-4">
				<div class="col-12">
					<div class="card">
						<div class="card-header bg-info text-white">
							<h5 class="mb-0">
								<i class="fas fa-info-circle me-2"></i>Account Information
							</h5>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<p><strong>User ID:</strong> #<?php echo esc($user['id']); ?></p>
									<p><strong>Role:</strong> <?php echo ucfirst(esc($user['role'])); ?></p>
								</div>
								<div class="col-md-6">
									<p><strong>Email:</strong> <?php echo esc($user['email'] ?? 'Not set'); ?></p>
									<p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'] ?? 'now')); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		// Theme switching functionality
		document.addEventListener('DOMContentLoaded', function() {
			// Highlight current theme
			const currentTheme = '<?php echo $currentTheme; ?>';
			document.querySelector(`[data-theme="${currentTheme}"]`).classList.add('border-primary');

			// Handle theme changes
			document.querySelectorAll('.theme-option').forEach(option => {
				option.addEventListener('click', function() {
					const theme = this.dataset.theme;

					fetch('<?php echo site_url('settings/theme'); ?>', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: new URLSearchParams({
							'theme': theme,
							'<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
						})
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							document.documentElement.setAttribute('data-bs-theme', theme);
							document.querySelectorAll('.theme-option').forEach(opt => opt.classList.remove('border-primary'));
							this.classList.add('border-primary');
							localStorage.setItem('preferred-theme', theme);

							// Show success message
							showNotification('Theme updated successfully!', 'success');
						}
					})
					.catch(error => {
						console.error('Error:', error);
						showNotification('Failed to update theme', 'error');
					});
				});
			});

			// Check for saved theme preference
			const savedTheme = localStorage.getItem('preferred-theme');
			if (savedTheme && savedTheme !== currentTheme) {
				document.querySelector(`[data-theme="${savedTheme}"]`).classList.add('border-primary');
			}
		});

		// Auto theme toggle
		function toggleAutoTheme(enabled) {
			if (enabled) {
				const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
				const autoTheme = prefersDark ? 'dark' : 'light';

				fetch('<?php echo site_url('settings/theme'); ?>', {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: new URLSearchParams({
						'theme': autoTheme,
						'<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
					})
				}).then(() => {
					document.documentElement.setAttribute('data-bs-theme', autoTheme);
					location.reload(); // Reload to apply theme fully
				});
			} else {
				localStorage.removeItem('preferred-theme');
			}
		}

		// Show temporary notification
		function showNotification(message, type) {
			const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
			const alertDiv = document.createElement('div');
			alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
			alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
			alertDiv.innerHTML = `
				${message}
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			`;

			document.body.appendChild(alertDiv);

			// Auto remove after 3 seconds
			setTimeout(() => {
				if (alertDiv.parentNode) {
					alertDiv.remove();
				}
			}, 3000);
		}
	</script>
</body>
</html>
