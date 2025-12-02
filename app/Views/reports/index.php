<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Reports & Analytics</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body data-bs-theme="<?php echo session()->get('theme') ?? 'light'; ?>">
	<?php $currentPage = 'reports'; $role = session()->get('user')['role'] ?? ''; $username = esc(session()->get('user')['username'] ?? 'User'); ?>
	<?php echo view('partials/sidebar', ['currentPage' => $currentPage, 'role' => $role, 'username' => $username]); ?>

	<div class="main-content bg-light">
		<div class="container py-4">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<h3 class="mb-0">Reports & Analytics</h3>
				<div>
					<a href="<?php echo site_url('reports/export-sales'); ?>" class="btn btn-pharmacy btn-sm me-2">
						<i class="fas fa-download me-1"></i>Export Sales
					</a>
					<a href="<?php echo site_url('reports/export-inventory'); ?>" class="btn btn-pharmacy btn-sm me-2">
						<i class="fas fa-download me-1"></i>Export Inventory
					</a>
					<a href="<?php echo site_url('reports/export-top-products'); ?>" class="btn btn-pharmacy btn-sm">
						<i class="fas fa-download me-1"></i>Export Top Products
					</a>
				</div>
			</div>

		<!-- Sales Summary Cards -->
		<div class="row g-4 mb-4">
			<div class="col-12 col-sm-6 col-lg-3">
				<div class="card text-bg-primary h-100">
					<div class="card-body text-center">
						<i class="fas fa-calendar-day fa-2x mb-2"></i>
						<div class="small">Today's Sales</div>
						<div class="h4 mb-0">$<?php echo number_format((float)$salesToday, 2); ?></div>
					</div>
				</div>
			</div>
			<div class="col-12 col-sm-6 col-lg-3">
				<div class="card text-bg-success h-100">
					<div class="card-body text-center">
						<i class="fas fa-calendar-week fa-2x mb-2"></i>
						<div class="small">This Week's Sales</div>
						<div class="h4 mb-0">$<?php echo number_format((float)$salesWeek, 2); ?></div>
					</div>
				</div>
			</div>
			<div class="col-12 col-sm-6 col-lg-3">
				<div class="card text-bg-info h-100">
					<div class="card-body text-center">
						<i class="fas fa-calendar-alt fa-2x mb-2"></i>
						<div class="small">This Month's Sales</div>
						<div class="h4 mb-0">$<?php echo number_format((float)$salesMonth, 2); ?></div>
					</div>
				</div>
			</div>
			<div class="col-12 col-sm-6 col-lg-3">
				<div class="card text-bg-warning h-100">
					<div class="card-body text-center">
						<i class="fas fa-coins fa-2x mb-2"></i>
						<div class="small">Today's Profit</div>
						<div class="h4 mb-0">$<?php echo number_format((float)$profitToday, 2); ?></div>
					</div>
				</div>
			</div>
		</div>

		<div class="row g-4 mb-4">
			<!-- Sales Reports -->
			<div class="col-12 col-lg-6">
				<div class="card">
					<div class="card-header">
						<i class="fas fa-chart-line me-2"></i>
						Top Selling Products (This Month)
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-striped mb-0">
								<thead>
									<tr>
										<th>Product</th>
										<th class="text-end">Revenue</th>
										<th class="text-end">Qty Sold</th>
									</tr>
								</thead>
								<tbody>
									<?php if (empty($topSaleProducts)): ?>
										<tr><td colspan="3" class="text-center text-muted">No sales data this month</td></tr>
									<?php else: ?>
										<?php foreach ($topSaleProducts as $row): ?>
										<tr>
											<td><?php echo esc($row['name']); ?> <small class="text-muted">(#<?php echo (int)$row['product_id']; ?>)</small></td>
											<td class="text-end">$<?php echo number_format((float)$row['revenue'], 2); ?></td>
											<td class="text-end"><?php echo (int)$row['qty_sold']; ?></td>
										</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- Inventory Value Summary -->
			<div class="col-12 col-lg-6">
				<div class="card">
					<div class="card-header">
						<i class="fas fa-warehouse me-2"></i>
						Inventory Summary
					</div>
					<div class="card-body">
						<div class="row text-center">
							<div class="col-12">
								<h5 class="text-primary">Total Inventory Value</h5>
								<h3>$<?php echo number_format((float)$totalInventoryValue, 2); ?></h3>
								<p class="text-muted mb-0">Current stock valuation</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row g-4">
			<!-- Low Stock Alert -->
			<div class="col-12 col-lg-6">
				<div class="card">
					<div class="card-header bg-warning text-dark">
						<i class="fas fa-exclamation-triangle me-2"></i>
						Low Stock Alerts
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-striped mb-0">
								<thead>
									<tr>
										<th>Product</th>
										<th class="text-end">Current Stock</th>
										<th class="text-end">Reorder Threshold</th>
									</tr>
								</thead>
								<tbody>
									<?php if (empty($lowStockProducts)): ?>
										<tr><td colspan="3" class="text-center text-muted">All products are adequately stocked</td></tr>
									<?php else: ?>
										<?php foreach ($lowStockProducts as $product): ?>
										<tr class="<?php echo ($product['stock'] <= 0) ? 'table-danger' : 'table-warning'; ?>">
											<td><?php echo esc($product['name']); ?></td>
											<td class="text-end"><?php echo (int)$product['stock']; ?></td>
											<td class="text-end"><?php echo (int)$product['reorder_threshold']; ?></td>
										</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- Expiring Soon -->
			<div class="col-12 col-lg-6">
				<div class="card">
					<div class="card-header bg-danger text-white">
						<i class="fas fa-clock me-2"></i>
						Expiring Soon (Next 30 Days)
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-striped mb-0">
								<thead>
									<tr>
										<th>Product</th>
										<th class="text-end">Expiry Date</th>
										<th class="text-end">Stock</th>
									</tr>
								</thead>
								<tbody>
									<?php if (empty($expiringProducts)): ?>
										<tr><td colspan="3" class="text-center text-muted">No products expiring soon</td></tr>
									<?php else: ?>
										<?php foreach ($expiringProducts as $product): ?>
											<?php
											$daysLeft = ceil((strtotime($product['expiry_date']) - time()) / (60 * 60 * 24));
											$alertClass = ($daysLeft <= 7) ? 'table-danger' : 'table-warning';
											?>
										<tr class="<?php echo $alertClass; ?>">
											<td><?php echo esc($product['name']); ?></td>
											<td class="text-end">
												<?php echo date('M j, Y', strtotime($product['expiry_date'])); ?>
												<small class="text-muted">(<?php echo $daysLeft; ?> days)</small>
											</td>
											<td class="text-end"><?php echo (int)$product['stock']; ?></td>
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
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
