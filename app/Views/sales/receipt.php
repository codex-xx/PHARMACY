<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sale Receipt - #<?php echo esc($sale['id']); ?></title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<style>
		.receipt-container {
			max-width: 400px;
			margin: 20px auto;
			border: 1px solid #ddd;
			padding: 20px;
			font-family: 'Courier New', monospace;
			font-size: 14px;
			line-height: 1.4;
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
			body { margin: 0; }
			.receipt-container { border: none; max-width: none; margin: 0; }
			.btn { display: none !important; }
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="receipt-container">
			<div class="receipt-header">
				<h1 class="receipt-title">PHARMACY POS</h1>
				<p class="receipt-subtitle">Sale Receipt</p>
			</div>

			<div class="receipt-details">
				<p><strong>Sale ID:</strong> #<?php echo esc($sale['id']); ?></p>
				<p><strong>Date:</strong> <?php
					date_default_timezone_set('Asia/Taipei');
					echo date('M j, Y g:i A', strtotime($sale['created_at']));
				?></p>
				<p><strong>Cashier:</strong> <?php echo esc($username); ?></p>
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
				<tbody>
					<?php foreach ($items as $item): ?>
					<tr>
						<td><?php echo esc($item['product_name']); ?></td>
						<td class="text-right"><?php echo (int)$item['qty']; ?></td>
						<td class="text-right">$<?php echo number_format($item['price'], 2); ?></td>
						<td class="text-right">$<?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<div class="receipt-total">
				<p class="text-right">
					<strong>Grand Total: $<?php echo number_format($sale['total'], 2); ?></strong>
				</p>
			</div>

			<div class="receipt-footer">
				<p>Thank you for your business!</p>
				<p>Pharmacy POS System</p>
			</div>
		</div>

		<div class="text-center mt-4">
			<button class="btn btn-primary me-2" onclick="window.print()">
				<i class="fas fa-print me-2"></i>Print Receipt
			</button>
			<a href="<?php echo site_url('dashboard'); ?>" class="btn btn-secondary">
				<i class="fas fa-arrow-left me-2"></i>Back to Dashboard
			</a>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
