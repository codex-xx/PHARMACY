<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sales - Pharmacy POS</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body data-bs-theme="<?php echo session()->get('theme') ?? 'light'; ?>">
	<?php $currentPage = 'sales'; $role = session()->get('user')['role'] ?? ''; $username = esc(session()->get('user')['username'] ?? 'User'); ?>
	<?php echo view('partials/sidebar', ['currentPage' => $currentPage, 'role' => $role, 'username' => $username]); ?>

	<div class="main-content">
		<div class="container my-4">
			<h1 class="mb-4">Sales Overview</h1>

			<div class="row g-4">
				<!-- Today Sales -->
				<div class="col-12 col-md-4">
					<div class="card pharmacy-card text-center">
						<div class="card-body">
							<i class="fas fa-calendar-day fa-3x text-success mb-3"></i>
							<h4>Today's Sales</h4>
							<h2 class="text-success">₱<?= number_format($todayTotal, 2) ?></h2>
							<small class="text-muted">Sales for <?= date('F j, Y') ?></small>
						</div>
					</div>
				</div>

				<!-- This Week Sales -->
				<div class="col-12 col-md-4">
					<div class="card pharmacy-card text-center">
						<div class="card-body">
							<i class="fas fa-calendar-week fa-3x text-primary mb-3"></i>
							<h4>This Week's Sales</h4>
							<h2 class="text-primary">₱<?= number_format($weekTotal, 2) ?></h2>
							<small class="text-muted">Sales for this week</small>
						</div>
					</div>
				</div>

				<!-- This Month Sales -->
				<div class="col-12 col-md-4">
					<div class="card pharmacy-card text-center">
						<div class="card-body">
							<i class="fas fa-calendar-alt fa-3x text-warning mb-3"></i>
							<h4>This Month's Sales</h4>
							<h2 class="text-warning">₱<?= number_format($monthTotal, 2) ?></h2>
							<small class="text-muted">Sales for <?= date('F Y') ?></small>
						</div>
					</div>
				</div>
			</div>

			<!-- Recent Sales -->
			<div class="row mt-5">
				<div class="col-12">
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h3 class="mb-0">Recent Sales</h3>
						<a href="<?php echo site_url('sales/export'); ?>" class="btn btn-pharmacy">
							<i class="fas fa-download me-2"></i>Export Sales Data
						</a>
					</div>
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-striped">
									<thead>
										<tr>
											<th>Sale ID</th>
											<th>Date</th>
											<th>Total</th>
											<th>User</th>
										</tr>
									</thead>
									<tbody>
										<?php if (empty($recentSales)): ?>
											<tr>
												<td colspan="4" class="text-center">No sales recorded yet.</td>
											</tr>
										<?php else: ?>
											<?php foreach ($recentSales as $sale): ?>
												<tr>
													<td>#<?= esc($sale['id']) ?></td>
													<td>
														<?php
														date_default_timezone_set('Asia/Taipei');
														echo date('M j, Y g:i A', strtotime($sale['created_at']));
														?>
													</td>
													<td>₱<?= number_format($sale['total'], 2) ?></td>
													<td>
														<?php
														// You might want to get user name, but for now just show ID
														echo esc($sale['user_id'] ?? 'N/A');
														?>
													</td>
												</tr>
											<?php endforeach; ?>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<?php echo view('partials/chat_widget'); ?>
</body>
</html>
