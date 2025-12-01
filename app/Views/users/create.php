<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Add User</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
	<?php $currentPage = 'users'; $role = session()->get('user')['role'] ?? ''; $username = esc(session()->get('user')['username'] ?? 'User'); ?>
	<?php echo view('partials/sidebar', ['currentPage' => $currentPage, 'role' => $role, 'username' => $username]); ?>

	<div class="main-content bg-light">
		<div class="container py-4">
			<div class="mb-3">
				<h3 class="mb-0">Add User</h3>
			</div>

			<div class="row justify-content-center">
				<div class="col-md-6">
					<div class="card pharmacy-card">
						<div class="card-body">
							<?php if (isset($errors)): ?>
								<div class="alert alert-danger">
									<ul>
										<?php foreach ($errors as $error): ?>
											<li><?php echo $error; ?></li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>

							<form action="<?php echo site_url('users/store'); ?>" method="post">
								<div class="mb-3">
									<label for="username" class="form-label">Username</label>
									<input type="text" class="form-control" id="username" name="username" value="<?php echo old('username'); ?>" required>
								</div>
								<div class="mb-3">
									<label for="phone" class="form-label">Phone</label>
									<input type="text" class="form-control" id="phone" name="phone" value="<?php echo old('phone'); ?>" required>
								</div>
								<div class="mb-3">
									<label for="password" class="form-label">Password</label>
									<input type="password" class="form-control" id="password" name="password" required>
								</div>
								<div class="mb-3">
									<label for="role" class="form-label">Role</label>
									<select class="form-control" id="role" name="role" required>
										<option value="cashier" <?php echo old('role') === 'cashier' ? 'selected' : ''; ?>>Cashier</option>
										<option value="admin" <?php echo old('role') === 'admin' ? 'selected' : ''; ?>>Admin</option>
									</select>
								</div>
								<div class="d-flex justify-content-between">
									<a href="<?php echo site_url('users'); ?>" class="btn btn-secondary">Cancel</a>
									<button type="submit" class="btn btn-pharmacy">Create User</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
