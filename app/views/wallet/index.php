<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Flash Messages -->
    <?php flash('deposit_success'); ?>
    <?php flash('deposit_error'); ?>
    <?php flash('trade_success'); ?>
    <?php flash('trade_error'); ?>
    <?php flash('transfer_success'); ?>
    <?php flash('transfer_error'); ?>

    <!-- Add this where you show errors -->
    <?php if(isset($data['errors'])): ?>
        <?php foreach($data['errors'] as $error): ?>
            <div class="bg-red-900/50 text-red-400 p-3 rounded-md text-sm mb-4">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Wallet Overview -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-white mb-6">Your Wallets</h2>
                <div class="space-y-4">
                    <?php if(!empty($data['wallets'])): ?>
                        <?php foreach($data['wallets'] as $wallet): ?>
                            <div class="bg-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="text-lg font-medium text-white"><?php echo $wallet->name; ?></h3>
                                        <p class="text-sm text-gray-400"><?php echo $wallet->symbol; ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-white">
                                            <?php echo number_format($wallet->balance, 8); ?>
                                        </p>
                                        <p class="text-sm text-gray-400">
                                            $<?php echo number_format($wallet->balance * $wallet->price, 2); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-400">No wallets found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="space-y-6">
            <!-- Deposit USDT -->
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-white mb-4">Deposit USDT</h3>
                <form action="<?php echo URLROOT; ?>/wallet/deposit" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Amount</label>
                            <input type="number" name="amount" step="0.01" min="0" 
                                   class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                        </div>
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Deposit
                        </button>
                    </div>
                </form>
            </div>

            <!-- Buy/Sell Crypto -->
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-white mb-4">Buy/Sell Crypto</h3>
                <form id="tradeForm" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Select Cryptocurrency</label>
                        <select name="crypto_id" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <?php foreach($data['cryptocurrencies'] as $crypto): ?>
                                <?php if($crypto->symbol !== 'USDT'): ?>
                                    <option value="<?php echo $crypto->id; ?>" data-price="<?php echo $crypto->price; ?>">
                                        <?php echo $crypto->name; ?> (<?php echo $crypto->symbol; ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Amount</label>
                        <input type="number" name="amount" step="any" min="0" 
                               class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                               required>
                        <p id="usdt-value" class="mt-1 text-sm text-gray-400"></p>
                    </div>
                    <div class="flex space-x-4">
                        <button type="submit" name="action" value="buy"
                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                            Buy
                        </button>
                        <button type="submit" name="action" value="sell"
                                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                            Sell
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transfer Crypto -->
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-white mb-4">Transfer Crypto</h3>
                <form action="<?php echo URLROOT; ?>/wallet/transfer" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Cryptocurrency</label>
                            <select name="crypto_id" 
                                    class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                    required>
                                <?php foreach($data['wallets'] as $wallet): ?>
                                    <option value="<?php echo $wallet->crypto_id; ?>">
                                        <?php echo $wallet->name; ?> (<?php echo $wallet->symbol; ?>) - Balance: <?php echo number_format($wallet->balance, 8); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Recipient (Email or NexusID)</label>
                            <input type="text" name="recipient" 
                                   class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Amount</label>
                            <input type="number" name="amount" step="0.00000001" min="0" 
                                   class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                        </div>
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mt-8">
        <div class="bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-white mb-6">Recent Transactions</h2>
            <?php if(!empty($data['transactions'])): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Crypto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php foreach($data['transactions'] as $transaction): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo getTransactionTypeClass($transaction['type']); ?>">
                                            <?php echo $transaction['type']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-white"><?php echo $transaction['name']; ?></div>
                                        <div class="text-sm text-gray-400"><?php echo $transaction['symbol']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <?php echo number_format($transaction['amount'], 8); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <?php echo $transaction['price'] ? '$'.number_format($transaction['price'], 2) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        <?php echo date('Y-m-d H:i', strtotime($transaction['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $transaction['status'] === 'COMPLETED' ? 'bg-green-900/50 text-green-400' : 'bg-yellow-900/50 text-yellow-400'; ?>">
                                            <?php echo $transaction['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-400">No transactions found</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Calculate USDT value when amount changes
document.querySelector('input[name="amount"]').addEventListener('input', function() {
    const cryptoSelect = document.querySelector('select[name="crypto_id"]');
    const selectedOption = cryptoSelect.options[cryptoSelect.selectedIndex];
    const price = parseFloat(selectedOption.dataset.price);
    const amount = parseFloat(this.value);
    
    if(!isNaN(amount) && !isNaN(price)) {
        const usdtValue = amount * price;
        document.getElementById('usdt-value').textContent = `≈ ${usdtValue.toFixed(2)} USDT`;
    }
});

// Handle form submission based on action
document.getElementById('tradeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const action = document.activeElement.value;
    this.action = `<?php echo URLROOT; ?>/wallet/${action}`;
    this.submit();
});
</script>

<?php 
// Helper function for transaction type styling
function getTransactionTypeClass($type) {
    switch(strtoupper($type)) {
        case 'DEPOSIT':
        case 'RECEIVE':
            return 'bg-green-900/50 text-green-400';
        case 'WITHDRAW':
        case 'SEND':
            return 'bg-red-900/50 text-red-400';
        case 'BUY':
            return 'bg-blue-900/50 text-blue-400';
        case 'SELL':
            return 'bg-yellow-900/50 text-yellow-400';
        default:
            return 'bg-gray-900/50 text-gray-400';
    }
}
?>

<?php require APPROOT . '/views/inc/footer.php'; ?> 