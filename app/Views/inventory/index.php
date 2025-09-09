<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Inventory Alerts</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container py-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="mb-0">Inventory Alerts</h3>
			<a href="<?php echo site_url('dashboard'); ?>" class="btn btn-outline-secondary">Back to POS</a>
		</div>

		<div class="row g-4">
			<div class="col-12 col-lg-6">
				<h5>Low Stock</h5>
				<div class="card"><div class="card-body p-0">
					<table class="table table-striped mb-0">
						<thead><tr><th>Name</th><th class="text-end">Stock</th><th class="text-end">Threshold</th></tr></thead>
						<tbody>
							<?php foreach ($lowStock as $p): ?>
							<tr>
								<td><?php echo esc($p['name']); ?></td>
								<td class="text-end"><?php echo (int)$p['stock']; ?></td>
								<td class="text-end"><?php echo (int)($p['reorder_threshold'] ?? 0); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div></div>
			</div>
			<div class="col-12 col-lg-6">
				<h5>Expiring Soon (30 days)</h5>
				<div class="card"><div class="card-body p-0">
					<table class="table table-striped mb-0">
						<thead><tr><th>Name</th><th>Expiry</th><th class="text-end">Stock</th></tr></thead>
						<tbody>
							<?php foreach ($expiring as $p): ?>
							<tr>
								<td><?php echo esc($p['name']); ?></td>
								<td><?php echo esc($p['expiry_date']); ?></td>
								<td class="text-end"><?php echo (int)$p['stock']; ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div></div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


