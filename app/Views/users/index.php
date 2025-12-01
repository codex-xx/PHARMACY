<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Manage Users</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body data-bs-theme="<?php echo session()->get('theme') ?? 'light'; ?>">
	<?php $currentPage = 'users'; $role = session()->get('user')['role'] ?? ''; $username = esc(session()->get('user')['username'] ?? 'User'); ?>
	<?php echo view('partials/sidebar', ['currentPage' => $currentPage, 'role' => $role, 'username' => $username]); ?>

	<div class="main-content bg-light">
		<div class="container py-4">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h3 class="mb-0">Manage Users</h3>
				<div>
					<a href="<?php echo site_url('users/create'); ?>" class="btn btn-pharmacy">Add User</a>
				</div>
			</div>

			<?php if (session()->getFlashdata('success')): ?>
				<div class="alert alert-success"><?php echo session()->getFlashdata('success'); ?></div>
			<?php endif; ?>
			<?php if (session()->getFlashdata('error')): ?>
				<div class="alert alert-danger"><?php echo session()->getFlashdata('error'); ?></div>
			<?php endif; ?>

			<div class="card">
				<div class="card-body p-0">
					<table class="table table-striped mb-0">
						<thead>
							<tr>
								<th>Username</th>
								<th>Phone</th>
								<th>Role</th>
								<th class="text-end">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($users)): ?>
								<?php foreach ($users as $u): ?>
									<tr>
										<td><?php echo esc($u['username']); ?></td>
										<td><?php echo esc($u['phone']); ?></td>
										<td><?php echo ucfirst(esc($u['role'])); ?></td>
										<td class="text-end">
											<a href="<?php echo site_url('users/edit/' . $u['id']); ?>" class="btn btn-sm btn-primary">Edit</a>
											<?php if ($u['role'] !== 'admin' || session()->get('user')['id'] !== $u['id']): ?>
											<form action="<?php echo site_url('users/delete/' . $u['id']); ?>" method="post" style="display:inline-block" onsubmit="return confirm('Delete this user?');">
												<button type="submit" class="btn btn-sm btn-danger">Delete</button>
											</form>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr><td colspan="4" class="text-center">No users found.</td></tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
