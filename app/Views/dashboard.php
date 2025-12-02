<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pharmacy POS - Dashboard</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<style>
	.product-card { cursor: pointer; transition: box-shadow 0.2s, transform 0.2s; border: none; }
	.product-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,0.12); transform: translateY(-2px) scale(1.03); }
	.product-card .card-body {
		border-radius: 0.75rem;
		background: linear-gradient(135deg, #e0f7fa 0%, #f1f8e9 100%);
		border: 1px solid #b2dfdb;
	}
	.product-card:nth-child(3n+1) .card-body { background: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 100%); border-color: #f8bbd0; }
	.product-card:nth-child(3n+2) .card-body { background: linear-gradient(135deg, #fffde7 0%, #e1f5fe 100%); border-color: #ffe082; }
	.product-card:nth-child(3n) .card-body { background: linear-gradient(135deg, #e8f5e9 0%, #e3f2fd 100%); border-color: #a5d6a7; }
	.cart-table td, .cart-table th { vertical-align: middle; }

	/* Receipt styles */
	.receipt-container {
		max-width: 400px;
		margin: 0 auto;
		border: 1px solid #ddd;
		padding: 20px;
		font-family: 'Courier New', monospace;
		font-size: 14px;
		line-height: 1.4;
		background: white;
	}
	.receipt-header {
		text-align: center;
		border-bottom: 2px solid #000;
		padding-bottom: 10px;
		margin-bottom: 15px;
	}
	.receipt-title {
		font-size: 18px;
		font-weight: bold;
		margin: 0;
	}
	.receipt-subtitle {
		font-size: 12px;
		margin: 5px 0;
	}
	.receipt-details {
		margin-bottom: 15px;
	}
	.receipt-table {
		width: 100%;
		border-collapse: collapse;
		margin-bottom: 15px;
	}
	.receipt-table th,
	.receipt-table td {
		padding: 5px 0;
		text-align: left;
		border-bottom: 1px dotted #999;
	}
	.receipt-table .text-right {
		text-align: right;
	}
	.receipt-total {
		border-top: 2px solid #000;
		padding-top: 10px;
		font-weight: bold;
		font-size: 16px;
	}
	.receipt-footer {
		text-align: center;
		margin-top: 20px;
		font-size: 12px;
		border-top: 1px dashed #999;
		padding-top: 10px;
	}
	@media print {
		.receipt-container { border: none; max-width: none; margin: 0; }
	}
	</style>
</head>
<body data-bs-theme="<?php echo session()->get('theme') ?? 'light'; ?>">
	<?php $currentPage = 'dashboard'; ?>
	<?php $role = session()->get('user')['role'] ?? ''; ?>
	<?php echo view('partials/sidebar', ['currentPage' => $currentPage, 'role' => $role, 'username' => $username ?? '']); ?>

	<div class="main-content">
		<div class="container my-4">
			<?php if ($role === 'admin'): ?>
				<!-- Admin View -->
				<h4 class="mb-4 text-primary"><i class="fas fa-cogs me-2"></i>Pharmacy Management Dashboard</h4>
				<div class="row g-4 mb-4">
					<!-- Quick Stats -->
					<div class="col-12">
						<div class="row g-3">
							<div class="col-6 col-md-3">
								<div class="card pharmacy-card text-center">
									<div class="card-body py-3">
										<i class="fas fa-boxes fa-2x text-primary mb-2"></i>
										<div class="fw-bold">₱<?php echo number_format($totalInventory ?? 0, 2); ?></div>
										<small class="text-muted">Inventory Value</small>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-3">
								<div class="card pharmacy-card text-center">
									<div class="card-body py-3">
										<i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
										<div class="fw-bold">₱<?php echo number_format($todaySales ?? 0, 2); ?></div>
										<small class="text-muted">Today's Sales</small>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-3">
								<div class="card pharmacy-card text-center">
									<div class="card-body py-3">
										<i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
										<div class="fw-bold"><?php echo $lowStock ?? 0; ?></div>
										<small class="text-muted">Low Stock Items</small>
									</div>
								</div>
							</div>
							<div class="col-6 col-md-3">
								<div class="card pharmacy-card text-center">
									<div class="card-body py-3">
										<i class="fas fa-clock fa-2x text-danger mb-2"></i>
										<div class="fw-bold"><?php echo $expiring ?? 0; ?></div>
										<small class="text-muted">Expiring Soon</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Management Tools -->
				<div class="row g-4">
					<div class="col-12 col-md-6 col-lg-4">
						<div class="card pharmacy-card text-center">
							<div class="card-body py-4">
								<i class="fas fa-boxes fa-3x text-primary mb-3"></i>
								<h5 class="card-title mb-3">Inventory Management</h5>
								<p class="text-muted">Track stock levels, expiries, and low-stock alerts.</p>
								<a class="btn btn-pharmacy w-100" href="<?php echo site_url('inventory'); ?>"><i class="fas fa-arrow-right me-2"></i>Manage Inventory</a>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-4">
						<div class="card pharmacy-card text-center">
							<div class="card-body py-4">
								<i class="fas fa-flask fa-3x text-info mb-3"></i>
								<h5 class="card-title mb-3">Product Management</h5>
								<p class="text-muted">Add, edit, and manage pharmacy products.</p>
								<a class="btn btn-pharmacy w-100" href="<?php echo site_url('products'); ?>"><i class="fas fa-arrow-right me-2"></i>Manage Products</a>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-4">
						<div class="card pharmacy-card text-center">
							<div class="card-body py-4">
								<i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
								<h5 class="card-title mb-3">Reports & Analytics</h5>
								<p class="text-muted">View sales, profit, and inventory usage reports.</p>
								<a class="btn btn-pharmacy w-100" href="<?php echo site_url('reports'); ?>"><i class="fas fa-arrow-right me-2"></i>View Reports</a>
							</div>
						</div>
					</div>
					<div class="col-12">
						<div class="row justify-content-center g-4">
							<div class="col-12 col-md-6 col-lg-4 col-xl-3">
								<div class="card pharmacy-card text-center">
									<div class="card-body py-4">
										<i class="fas fa-users fa-3x text-warning mb-3"></i>
										<h5 class="card-title mb-3">User Management</h5>
										<p class="text-muted">Manage staff accounts and permissions.</p>
										<a class="btn btn-pharmacy w-100" href="<?php echo site_url('users'); ?>"><i class="fas fa-arrow-right me-2"></i>Manage Users</a>
									</div>
								</div>
							</div>
							<div class="col-12 col-md-6 col-lg-4 col-xl-3">
								<div class="card pharmacy-card text-center">
									<div class="card-body py-4">
										<i class="fas fa-shopping-cart fa-3x text-secondary mb-3"></i>
										<h5 class="card-title mb-3">Sales Overview</h5>
										<p class="text-muted">Monitor sales transactions and performance.</p>
										<a class="btn btn-pharmacy w-100" href="<?php echo site_url('sales'); ?>"><i class="fas fa-arrow-right me-2"></i>View Sales</a>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			<?php else: ?>
				<!-- Cashier View -->
				<!-- Barcode Scanner Section -->
				<div class="row g-4 mb-4">
					<div class="col-12">
						<div class="card pharmacy-card">
							<div class="card-body text-center py-4">
								<h5 class="card-title mb-3">
									<i class="fas fa-barcode fa-2x text-primary mb-2"></i><br>
									Quick Scan
								</h5>
								<!-- ✅ Barcode Scanner Input -->
								<div class="d-flex justify-content-center mb-3">
									<input type="text" id="barcodeInput"
										class="form-control text-center w-75"
										style="max-width: 500px; min-width: 300px;"
										placeholder="Scan barcode or enter product code..." autofocus>
								</div>

								<!-- ✅ Product Preview -->
								<div id="productPreview" class="mt-3" style="display:none;">
									<div class="card border-success mx-auto" style="max-width: 400px;">
										<div class="card-body py-2">
											<h6 id="previewName" class="mb-1"></h6>
											<div><strong>Price:</strong> ₱<span id="previewPrice"></span></div>
											<div><strong>Stock:</strong> <span id="previewStock"></span></div>
											<button class="btn btn-success btn-sm mt-2 w-100" id="addPreviewBtn">Add to Cart</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row g-4">
					<!-- Products Section -->
					<div class="col-12 col-lg-7">
						<div class="d-flex justify-content-between align-items-center mb-3">
							<h5 class="mb-0">Products</h5>
							<div class="input-group" style="max-width: 300px;">
								<span class="input-group-text"><i class="fas fa-search"></i></span>
								<input type="text" class="form-control" id="productSearch" placeholder="Search products...">
							</div>
						</div>
						<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="products"></div>
					</div>

					<!-- Cart Section -->
					<div class="col-12 col-lg-5">
						<h5 class="mb-3">Cart</h5>
						<div class="card pharmacy-card">
							<div class="card-body d-flex flex-column" style="max-height: 80vh;">
								<div class="flex-grow-1 overflow-auto">
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
								</div>
								<hr class="my-2">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<strong>Grand Total</strong>
									<h4 class="mb-0" id="grandTotal">₱0.00</h4>
								</div>
								<button class="btn btn-pharmacy w-100" id="checkoutBtn">Checkout</button>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Receipt Modal -->
	<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="receiptModalLabel">Sale Receipt</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="receipt-container">
						<div class="receipt-header text-center">
							<h1 class="receipt-title">PHARMACY POS</h1>
							<p class="receipt-subtitle">Sale Receipt</p>
						</div>

						<div class="receipt-details">
							<p><strong>Sale ID:</strong> <span id="receiptSaleId"></span></p>
							<p><strong>Date:</strong> <span id="receiptDate"></span></p>
							<p><strong>Cashier:</strong> <?php echo esc($username ?? 'User'); ?></p>
						</div>

						<table class="receipt-table">
							<thead>
								<tr>
									<th>Item</th>
									<th class="text-right">Qty</th>
									<th class="text-right">Price</th>
									<th class="text-right">Total</th>
								</tr>
							</thead>
							<tbody id="receiptItems"></tbody>
						</table>

						<div class="receipt-total">
							<p class="text-right">
								<strong>Grand Total: ₱<span id="receiptTotal"></span></strong>
							</p>
						</div>

						<div class="receipt-footer text-center">
							<p>Thank you for your business!</p>
							<p>Pharmacy POS System</p>
						</div>
					</div>
				</div>
				<div class="modal-footer" id="modalFooter">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-success" id="completeCheckoutBtn" onclick="completeCheckout()">
						<i class="fas fa-shopping-cart me-2"></i>Complete Checkout & Update Inventory
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
	// Products from backend
	const products = <?php echo json_encode(array_map(function($p) {
		return [
			'id' => (int) $p['id'],
			'name' => (string) $p['name'],
			'price' => (float) $p['price'],
			'stock' => (int) $p['stock'],
			'barcode' => (string) $p['barcode']
		];
	}, $dbProducts ?? []), JSON_UNESCAPED_SLASHES); ?>;

	const productsContainer = document.getElementById('products');
	const cartBody = document.getElementById('cartBody');
	const grandTotalEl = document.getElementById('grandTotal');
	const cart = new Map();

	// 🔹 Render product cards
	function renderProducts() {
		   productsContainer.innerHTML = products.map((p, i) => `
			   <div class="col">
				   <div class="card product-card" onclick="addToCart(${p.id})">
					   <div class="card-body">
						   <h6 class="card-title mb-1">${p.name}</h6>
						   <div class="text-muted">₱${p.price.toFixed(2)}</div>
					   </div>
				   </div>
			   </div>
		   `).join('');
	}

	// 🔹 Normalize product object coming from API or from local products array
	function normalizeProduct(raw) {
		if (!raw) return null;
		// Some endpoints may wrap the product in {data: {...}} or similar
		const p = raw.data && typeof raw.data === 'object' ? raw.data : raw;
		const id = p.id || p.product_id || p['id'] || null;
		if (!id) return null;
		return {
			id: Number(id),
			name: String(p.name || p.title || ''),
			price: Number(p.price || 0),
			stock: Number(p.stock || 0),
			barcode: String(p.barcode || ''),
		};
	}

	// 🔹 Add to cart (works for both normal & barcode scanned products)
	function addToCart(id, productObj = null) {
		let product = null;
		if (productObj) {
			product = normalizeProduct(productObj);
		} else {
			// find in local products array
			product = products.find(p => Number(p.id) === Number(id));
		}
		if (!product) return;

		// If product already in cart, just increase quantity
		const existing = cart.get(product.id) || { ...product, qty: 0 };
		existing.qty = (existing.qty || 0) + 1;
		cart.set(product.id, existing);
		renderCart();
	}

	// 🔹 Remove item
	function removeFromCart(id) {
		cart.delete(id);
		renderCart();
	}

	// 🔹 Change quantity
	function changeQty(id, delta) {
		const item = cart.get(id);
		if (!item) return;
		item.qty = Math.max(1, item.qty + delta);
		cart.set(id, item);
		renderCart();
	}

	// 🔹 Render cart
	function renderCart() {
		let grand = 0;
		cartBody.innerHTML = Array.from(cart.values()).map(item => {
			const total = item.qty * item.price;
			grand += total;

			// Check stock availability
			const product = products.find(p => p.id === item.id);
			const isLowStock = product && item.qty > product.stock;
			const stockWarning = isLowStock ? `<small class="text-danger">Only ${product.stock} available</small>` : '';

			return `
				<tr class="${isLowStock ? 'table-danger' : ''}">
					<td>${item.name}${stockWarning ? '<br>' + stockWarning : ''}</td>
					<td class="text-end">
						<input type="number" class="form-control form-control-sm text-center ${isLowStock ? 'border-danger' : ''}"
							   style="width: 80px;" min="1" value="${item.qty}"
							   onchange="updateQty(${item.id}, this.value)">
					</td>
					<td class="text-end">₱${item.price.toFixed(2)}</td>
					<td class="text-end">₱${total.toFixed(2)}</td>
					<td class="text-end"><button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">Remove</button></td>
				</tr>`;
		}).join('');
		grandTotalEl.textContent = `₱${grand.toFixed(2)}`;
	}

	// 🔹 Update quantity from manual input
	function updateQty(id, newQty) {
		const qty = parseInt(newQty);
		if (isNaN(qty) || qty < 1) return;

		const item = cart.get(id);
		if (!item) return;

		item.qty = qty;
		cart.set(id, item);
		renderCart();
	}

	// 🔹 Filter products based on search
	function filterProducts(searchTerm) {
		const filteredProducts = searchTerm ?
			products.filter(p => p.name.toLowerCase().includes(searchTerm.toLowerCase())) :
			products;

		productsContainer.innerHTML = filteredProducts.map((p, i) => `
			<div class="col">
				<div class="card product-card" onclick="addToCart(${p.id})">
					<div class="card-body">
						<h6 class="card-title mb-1">${p.name}</h6>
						<div class="text-muted">₱${p.price.toFixed(2)}</div>
					</div>
				</div>
			</div>
		`).join('');
	}

// 🔹 Preview Receipt — show receipt modal first
	document.getElementById('checkoutBtn').addEventListener('click', () => {
		if (cart.size === 0) { alert('Cart is empty'); return; }

		// Validate stock availability
		let stockErrors = [];
		for (let [productId, cartItem] of cart) {
			const product = products.find(p => p.id === productId);
			if (product && cartItem.qty > product.stock) {
				stockErrors.push(`${product.name}: Requested ${cartItem.qty}, Available ${product.stock}`);
			}
		}

		// Show stock errors if any
		if (stockErrors.length > 0) {
			alert('Insufficient Stock:\n\n' + stockErrors.join('\n') + '\n\nPlease adjust quantities or remove items with insufficient stock.');
			return;
		}

		// Store cart items for later checkout
		pendingCartItems = Array.from(cart.values());

		// Show receipt modal with cart items for preview
		showReceiptModal(null, pendingCartItems); // null sale_id for preview
	});

	// Store cart items for checkout (will be set when modal opens)
	let pendingCartItems = [];

// 🔹 Complete Checkout — actually process the sale
	function completeCheckout() {
		const cartItems = pendingCartItems; // Use stored cart items
		const items = cartItems.map(i => ({ id: i.id, qty: i.qty, price: i.price }));
		const payload = { items };

		// Disable the complete checkout button
		document.getElementById('completeCheckoutBtn').disabled = true;
		document.getElementById('completeCheckoutBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

		fetch("<?php echo site_url('sales/checkout'); ?>", {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify(payload)
		}).then(res => res.json()).then(res => {
			if (res && res.sale_id) {
				// Update local product stock immediately
				items.forEach(item => {
					const product = products.find(p => p.id === item.id);
					if (product) {
						product.stock = Math.max(0, product.stock - item.qty);
					}
				});

				// Update receipt with sale ID
				document.getElementById('receiptSaleId').textContent = '#' + res.sale_id;

				// Re-render products to show updated stock
				renderProducts();

				// Update modal footer to show success
				document.querySelector('.modal-footer').innerHTML = `
					<div class="alert alert-success w-100 mb-0">
						<i class="fas fa-check-circle me-2"></i>
						Checkout completed successfully! Sale ID: #${res.sale_id}
					</div>
					<div class="w-100 text-center mt-3">
						<button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" onclick="printReceipt()">
							<i class="fas fa-print me-2"></i>Print Receipt
						</button>
					</div>
				`;

				// Clear cart after successful checkout
				cart.clear();
				renderCart();

			} else if (res && res.error) {
				alert('Error: ' + res.error);
				// Re-enable button on error
				document.getElementById('completeCheckoutBtn').disabled = false;
				document.getElementById('completeCheckoutBtn').innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Complete Checkout';
			} else {
				alert('Unexpected response from server');
				// Re-enable button on error
				document.getElementById('completeCheckoutBtn').disabled = false;
				document.getElementById('completeCheckoutBtn').innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Complete Checkout';
			}
		}).catch(err => {
			console.error(err);
			alert('Checkout failed');
			// Re-enable button on error
			document.getElementById('completeCheckoutBtn').disabled = false;
			document.getElementById('completeCheckoutBtn').innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Complete Checkout';
		});
	}

	// ✅ Barcode Scanner Logic with Preview
	const barcodeInput = document.getElementById('barcodeInput');
	const previewBox = document.getElementById('productPreview');
	const previewName = document.getElementById('previewName');
	const previewPrice = document.getElementById('previewPrice');
	const previewStock = document.getElementById('previewStock');
	const addPreviewBtn = document.getElementById('addPreviewBtn');
	let previewProduct = null;

	// ✅ Barcode Scanner Direct-to-Cart
barcodeInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const code = barcodeInput.value.trim();
        if (!code) return;

		fetch("<?php echo site_url('api/barcode/find'); ?>/" + encodeURIComponent(code))
			.then(res => res.json())
			.then(product => {
				const p = normalizeProduct(product);
				if (p && p.id) {
					// If product exists → add/increase qty in cart
					addToCart(p.id, p);
				} else {
					alert("❌ Product not found for barcode: " + code);
				}
				barcodeInput.value = '';
			})
            .catch(err => {
                console.error(err);
                alert("⚠️ Error fetching product");
                barcodeInput.value = '';
            });
    }
});
	// ✅ Barcode Scanner with Preview
let scanTimer; // timer to wait until scanner finished typing
barcodeInput.addEventListener('input', function () {
    clearTimeout(scanTimer);
    scanTimer = setTimeout(() => {
        const code = barcodeInput.value.trim();
        if (!code) return;

        // Fetch product from DB by barcode
        fetch("<?php echo site_url('api/barcode/find'); ?>/" + encodeURIComponent(code))
            .then(res => res.json())
            .then(product => {
				const p = normalizeProduct(product);
				if (p && p.id) {
					// 🔹 Show preview box
					previewProduct = p;
					previewName.textContent = p.name;
					previewPrice.textContent = parseFloat(p.price).toFixed(2);
					previewStock.textContent = p.stock;
					previewBox.style.display = "block";
				} else {
					previewBox.style.display = "none";
					alert("❌ Product not found: " + code);
				}
                barcodeInput.value = ""; // clear field for next scan
            })
            .catch(err => {
                console.error(err);
                alert("⚠️ Error fetching product");
                previewBox.style.display = "none";
                barcodeInput.value = "";
            });
    }, 300); // wait 300ms after typing stops
});

// ✅ Add previewed product to cart
addPreviewBtn.addEventListener('click', () => {
    if (previewProduct) {
        addToCart(previewProduct.id, previewProduct);
        previewBox.style.display = "none";
        previewProduct = null;
    }
});

// 🔹 Show Receipt Modal
function showReceiptModal(saleId, cartItems) {
    // Set sale ID and current date
    document.getElementById('receiptSaleId').textContent = '#' + saleId;

    // Set current date
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    document.getElementById('receiptDate').textContent = dateStr;

    // Render cart items in receipt
    const receiptItemsEl = document.getElementById('receiptItems');
    let total = 0;

    receiptItemsEl.innerHTML = cartItems.map(item => {
        const itemTotal = item.qty * item.price;
        total += itemTotal;
        return `
            <tr>
                <td>${item.name}</td>
                <td class="text-right">${item.qty}</td>
                <td class="text-right">₱${item.price.toFixed(2)}</td>
                <td class="text-right">₱${itemTotal.toFixed(2)}</td>
            </tr>
        `;
    }).join('');

    // Set total
    document.getElementById('receiptTotal').textContent = total.toFixed(2);

    // Clear cart and update UI
    cart.clear();
    renderCart();

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
    modal.show();

    // Add event listener to refresh page when modal is closed
    document.getElementById('receiptModal').addEventListener('hidden.bs.modal', function () {
        location.reload(); // Refresh to show updated stock
    });
}

// 🔹 Print Receipt
function printReceipt() {
    const printContent = document.querySelector('.receipt-container').innerHTML;
    const originalContent = document.body.innerHTML;

    document.body.innerHTML = `
        <div style="font-family: 'Courier New', monospace; font-size: 14px; line-height: 1.4; max-width: 400px; margin: 20px auto;">
            ${printContent}
        </div>
    `;

    window.print();
    document.body.innerHTML = originalContent;

    // Reinitialize after print
    renderProducts();
    renderCart();
}

	// 🔹 Product search functionality
	document.getElementById('productSearch').addEventListener('input', function(e) {
		filterProducts(e.target.value);
	});

	// Initialize
	renderProducts();
	renderCart();
	</script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<?php echo view('partials/chat_widget'); ?>
</body>
</html>
