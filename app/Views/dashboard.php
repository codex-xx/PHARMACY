<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pharmacy POS - Dashboard</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
    .product-card { cursor: pointer; }
    .cart-table td, .cart-table th { vertical-align: middle; }
    </style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">Pharmacy POS</a>
			<div class="d-flex">
				<span class="navbar-text me-3">Hello, <?php echo esc($username); ?></span>
				<a href="<?php echo site_url('logout'); ?>" class="btn btn-outline-light btn-sm">Logout</a>
			</div>
		</div>
	</nav>

	<div class="container my-4">
		<?php $role = session()->get('user')['role'] ?? ''; ?>
		<?php if ($role === 'admin'): ?>
			<div class="row g-4">
				<div class="col-12 col-lg-6">
					<div class="card text-center">
						<div class="card-body py-5">
							<h4 class="card-title mb-3">Inventory Management</h4>
							<p class="text-muted">Track stock levels, expiries, and low-stock alerts.</p>
							<a class="btn btn-warning" href="<?php echo site_url('inventory'); ?>">Open Inventory</a>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6">
					<div class="card text-center">
						<div class="card-body py-5">
							<h4 class="card-title mb-3">Reports & Analytics</h4>
							<p class="text-muted">View sales, profit, and inventory usage reports.</p>
							<a class="btn btn-info" href="<?php echo site_url('reports'); ?>">Open Reports</a>
						</div>
					</div>
				</div>
			</div>
		<?php else: ?>
			<div class="row g-4">
				<div class="col-12 col-lg-8">
					<h5 class="mb-3">Products</h5>
					<div class="mb-2">
						<a class="btn btn-sm btn-outline-secondary" href="<?php echo site_url('products'); ?>">Manage Products</a>
						<a class="btn btn-sm btn-outline-warning" href="<?php echo site_url('inventory'); ?>">Inventory Alerts</a>
					</div>
					<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="products"></div>
				</div>
				<div class="col-12 col-lg-4">
					<h5 class="mb-3">Cart</h5>
					<div class="card">
						<div class="card-body">
							<table class="table table-sm cart-table" id="cartTable">
								<thead>
									<tr>
										<th>Item</th>
										<th class="text-end">Qty</th>
										<th class="text-end">Price</th>
										<th class="text-end">Total</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="cartBody"></tbody>
							</table>
							<div class="d-flex justify-content-between align-items-center">
								<strong>Grand Total</strong>
								<h4 class="mb-0" id="grandTotal">$0.00</h4>
							</div>
							<hr>
							<button class="btn btn-success w-100" id="checkoutBtn">Checkout</button>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<script>
	const products = <?php echo json_encode(array_map(function($p) {
		return [
			'id' => (int) $p['id'],
			'name' => (string) $p['name'],
			'price' => (float) $p['price']
		];
	}, $dbProducts ?? []), JSON_UNESCAPED_SLASHES); ?>;

	const productsContainer = document.getElementById('products');
	const cartBody = document.getElementById('cartBody');
	const grandTotalEl = document.getElementById('grandTotal');
	const cart = new Map();

	function renderProducts() {
		productsContainer.innerHTML = products.map(p => `
			<div class="col">
				<div class="card product-card" onclick="addToCart(${p.id})">
					<div class="card-body">
						<h6 class="card-title mb-1">${p.name}</h6>
						<div class="text-muted">$${p.price.toFixed(2)}</div>
					</div>
				</div>
			</div>
		`).join('');
	}

	function addToCart(id) {
		const product = products.find(p => p.id === id);
		if (!product) return;
		const existing = cart.get(id) || { ...product, qty: 0 };
		existing.qty += 1;
		cart.set(id, existing);
		renderCart();
	}

	function removeFromCart(id) {
		cart.delete(id);
		renderCart();
	}

	function changeQty(id, delta) {
		const item = cart.get(id);
		if (!item) return;
		item.qty = Math.max(1, item.qty + delta);
		cart.set(id, item);
		renderCart();
	}

	function renderCart() {
		let grand = 0;
		cartBody.innerHTML = Array.from(cart.values()).map(item => {
			const total = item.qty * item.price;
			grand += total;
			return `
				<tr>
					<td>${item.name}</td>
					<td class="text-end">
						<div class="btn-group btn-group-sm" role="group">
							<button class="btn btn-outline-secondary" onclick="changeQty(${item.id}, -1)">-</button>
							<span class="btn btn-light disabled">${item.qty}</span>
							<button class="btn btn-outline-secondary" onclick="changeQty(${item.id}, 1)">+</button>
						</div>
					</td>
					<td class="text-end">$${item.price.toFixed(2)}</td>
					<td class="text-end">$${total.toFixed(2)}</td>
					<td class="text-end"><button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">Remove</button></td>
				</tr>`;
		}).join('');
		grandTotalEl.textContent = `$${grand.toFixed(2)}`;
	}

	document.getElementById('checkoutBtn').addEventListener('click', () => {
		if (cart.size === 0) { alert('Cart is empty'); return; }
		alert('Checkout successful!');
		cart.clear();
		renderCart();
	});

	renderProducts();
	renderCart();
	</script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


