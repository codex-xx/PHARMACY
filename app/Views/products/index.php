
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Manage Products</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
	<div class="container py-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="mb-0">Manage Products</h3>
			<div>
				<a href="<?php echo site_url('dashboard'); ?>" class="btn btn-outline-secondary me-2">Back to Dashboard</a>
				<a href="<?php echo site_url('products/create'); ?>" class="btn btn-success">Add Product</a>
			</div>
		</div>
		<div class="card">
			<div class="card-body p-0">
				<table class="table table-striped mb-0">
					<thead>
						<tr>
							<th>Name</th>
							<th>SKU</th>
							<th class="text-end">Price</th>
							<th class="text-end">Stock</th>
							<th class="text-end">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($products)): ?>
							<?php foreach ($products as $p): ?>
								<tr>
									<td><?php echo esc($p['name']); ?></td>
									<td><?php echo esc($p['sku']); ?></td>
									<td class="text-end">₱<?php echo number_format($p['price'], 2); ?></td>
									<td class="text-end"><?php echo (int)$p['stock']; ?></td>
									<td class="text-end">
										<a href="<?php echo site_url('products/edit/' . $p['id']); ?>" class="btn btn-sm btn-primary">Edit</a>
										<form action="<?php echo site_url('products/delete/' . $p['id']); ?>" method="post" style="display:inline-block" onsubmit="return confirm('Delete this product?');">
											<button type="submit" class="btn btn-sm btn-danger">Delete</button>
										</form>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr><td colspan="5" class="text-center">No products found.</td></tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

