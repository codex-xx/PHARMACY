<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Inventory Management</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<style>
		.inventory-card {
			border-radius: 1rem;
			box-shadow: 0 4px 24px rgba(0,0,0,0.08);
			border: none;
			margin-bottom: 2rem;
		}
		.inventory-card.low-stock {
			border-left: 8px solid #dc3545;
			background: #fff0f3;
		}
		.inventory-card.expiring {
			border-left: 8px solid #ffc107;
			background: #fffbe6;
		}
		.inventory-card.all-products {
			border-left: 8px solid #198754;
			background: #f0fff4;
		}
		.inventory-section-title {
			font-weight: 600;
			letter-spacing: 0.5px;
			margin-bottom: 0.5rem;
		}
		.table th, .table td {
			vertical-align: middle;
		}
	</style>
</head>
<body data-bs-theme="<?php echo session()->get('theme') ?? 'light'; ?>">
	<?php $currentPage = 'inventory'; $role = session()->get('user')['role'] ?? ''; $username = esc(session()->get('user')['username'] ?? 'User'); ?>
	<?php echo view('partials/sidebar', ['currentPage' => $currentPage, 'role' => $role, 'username' => $username]); ?>

	<div class="main-content bg-light">
		<div class="container py-4">
			<div class="mb-3">
				<h3 class="mb-0">Inventory Management</h3>
			</div>

		<div class="row g-4">
		<div class="col-12">
			<div class="inventory-card all-products card">
				<div class="card-body p-0">
					<div class="p-3 pb-0 inventory-section-title text-success">All Products in Inventory</div>
					<table class="table table-striped mb-0">
						<thead><tr><th>Name</th><th>SKU</th><th>Barcode</th><th class="text-end">Price</th><th class="text-end">Stock</th></tr></thead>
						<tbody>
							<?php foreach ($allProducts as $p): ?>
							<tr>
								<td><?php echo esc($p['name']); ?></td>
								<td><?php echo esc($p['sku']); ?></td>
								<td><?php echo esc($p['barcode']); ?></td>
								<td class="text-end">₱<?php echo number_format($p['price'], 2); ?></td>
								<td class="text-end fw-bold text-success"><?php echo (int)$p['stock']; ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
			<div class="col-12 col-lg-6">
					<div class="inventory-card low-stock card">
						<div class="card-body p-0">
							<div class="p-3 pb-0 inventory-section-title text-danger">Low Stock</div>
							<table class="table table-striped mb-0">
								<thead><tr><th>Name</th><th class="text-end">Stock</th><th class="text-end">Threshold</th></tr></thead>
								<tbody>
									<?php foreach ($lowStock as $p): ?>
									<tr>
										<td><?php echo esc($p['name']); ?></td>
										<td class="text-end fw-bold text-danger"><?php echo (int)$p['stock']; ?></td>
										<td class="text-end"><?php echo (int)($p['reorder_threshold'] ?? 0); ?></td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
			</div>
			<div class="col-12 col-lg-6">
					<div class="inventory-card expiring card">
						<div class="card-body p-0">
							<div class="p-3 pb-0 inventory-section-title text-warning">Expiring Soon (30 days)</div>
							<table class="table table-striped mb-0">
								<thead><tr><th>Name</th><th>Expiry</th><th class="text-end">Stock</th></tr></thead>
								<tbody>
									<?php foreach ($expiring as $p): ?>
									<tr>
										<td><?php echo esc($p['name']); ?></td>
										<td><?php echo esc($p['expiry_date']); ?></td>
										<td class="text-end fw-bold text-warning"><?php echo (int)$p['stock']; ?></td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
