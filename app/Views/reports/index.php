<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Reports & Analytics</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container py-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="mb-0">Reports & Analytics</h3>
			<a href="<?php echo site_url('dashboard'); ?>" class="btn btn-outline-secondary">Back to POS</a>
		</div>

		<div class="row g-4 mb-4">
			<div class="col-md-3">
				<div class="card text-bg-primary"><div class="card-body">
					<div class="small">Sales Today</div>
					<div class="h4 mb-0">$<?php echo number_format((float)$salesToday, 2); ?></div>
				</div></div>
			</div>
			<div class="col-md-3">
				<div class="card text-bg-success"><div class="card-body">
					<div class="small">Profit Today</div>
					<div class="h4 mb-0">$<?php echo number_format((float)$profitToday, 2); ?></div>
				</div></div>
			</div>
			<div class="col-md-3">
				<div class="card text-bg-info"><div class="card-body">
					<div class="small">Sales This Month</div>
					<div class="h4 mb-0">$<?php echo number_format((float)$salesMonth, 2); ?></div>
				</div></div>
			</div>
			<div class="col-md-3">
				<div class="card text-bg-warning"><div class="card-body">
					<div class="small">Profit This Month</div>
					<div class="h4 mb-0">$<?php echo number_format((float)$profitMonth, 2); ?></div>
				</div></div>
			</div>
		</div>

		<div class="card">
			<div class="card-header">Top Products (by qty sold this month)</div>
			<div class="card-body p-0">
				<table class="table table-striped mb-0">
					<thead><tr><th>Product</th><th class="text-end">Price</th><th class="text-end">Qty Sold</th></tr></thead>
					<tbody>
						<?php foreach ($topProducts as $row): ?>
						<tr>
							<td><?php echo esc($row['name'] ?? ''); ?> <small class="text-muted">(#<?php echo (int)$row['product_id']; ?>)</small></td>
							<td class="text-end">$<?php echo number_format((float)($row['price'] ?? 0), 2); ?></td>
							<td class="text-end"><?php echo (int)$row['qty_sold']; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


