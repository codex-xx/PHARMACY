
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Edit Product</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body data-bs-theme="<?php echo session()->get('theme') ?? 'light'; ?>">
	<?php $currentPage = 'products'; $role = session()->get('user')['role'] ?? ''; $username = esc(session()->get('user')['username'] ?? 'User'); ?>
	<?php echo view('partials/sidebar', ['currentPage' => $currentPage, 'role' => $role, 'username' => $username]); ?>

	<div class="main-content bg-light">
		<div class="container py-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3 class="mb-0">Edit Product</h3>
			<a href="<?php echo site_url('products'); ?>" class="btn btn-outline-secondary">Back</a>
		</div>
		<div class="card">
			<div class="card-body">
				<form method="post" action="<?php echo site_url('products/update/' . $product['id']); ?>">
					<div class="mb-3">
						<label for="name" class="form-label">Product Name</label>
						<input type="text" class="form-control" id="name" name="name" value="<?php echo esc($product['name']); ?>" required>
					</div>
					<div class="mb-3">
						<label for="sku" class="form-label">SKU</label>
						<input type="text" class="form-control" id="sku" name="sku" value="<?php echo esc($product['sku']); ?>" required>
					</div>
					<div class="mb-3">
						<label for="price" class="form-label">Price</label>
						<input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo esc($product['price']); ?>" required>
					</div>
					<div class="mb-3">
						<label for="stock" class="form-label">Stock</label>
						<input type="number" class="form-control" id="stock" name="stock" value="<?php echo esc($product['stock']); ?>" required>
					</div>
					<button type="submit" class="btn btn-primary">Update Product</button>
				</form>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
