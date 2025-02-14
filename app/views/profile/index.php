<?php
$wallet = $data['wallet'];
?>


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profil Info -->
        <div class="lg:col-span-1">
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <div class="text-center mb-6">
                    <img class="h-32 w-32 rounded-full mx-auto mb-4"
                         src="https://ui-avatars.com/api/?name=<?php echo urlencode($data['user']->firstname . ' ' . $data['user']->lastname); ?>&size=128&background=random"
                         alt="Profile">
                    <h2 class="text-2xl font-bold text-white">
                        <?php echo $data['user']->firstname . ' ' . $data['user']->lastname; ?>
                    </h2>
                    <p class="text-gray-400"><?php echo $data['user']->nexus_id; ?></p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-gray-400">Email</label>
                        <p class="text-white"><?php echo $data['user']->email; ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-400">Date de naissance</label>
                        <p class="text-white"><?php echo date('d/m/Y', strtotime($data['user']->birth_date)); ?></p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-400">Membre depuis</label>
                        <p class="text-white"><?php echo date('d/m/Y', strtotime($data['user']->created_at)); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activité Récente -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-white mb-4">Activité Récente</h3>
                <?php if(!empty($data['transactions'])): ?>
                    <div class="space-y-4">
                        <?php foreach($data['transactions'] as $transaction): ?>
                            <div class="flex items-center justify-between bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="<?php echo $transaction['type'] === 'BUY' ? 'bg-green-500/20' : 'bg-red-500/20'; ?> p-3 rounded-full mr-4">
                                        <i class="fas <?php echo $transaction['type'] === 'BUY' ? 'fa-arrow-down' : 'fa-arrow-up'; ?> text-<?php echo $transaction['type'] === 'BUY' ? 'green' : 'red'; ?>-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-white"><?php echo $transaction['type']; ?> <?php echo $transaction['symbol']; ?></p>
                                        <p class="text-sm text-gray-400"><?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-white"><?php echo number_format($transaction['amount'], 4); ?> <?php echo $transaction['symbol']; ?></p>
                                    <p class="text-sm text-gray-400">$<?php echo number_format($transaction['amount'] * $transaction['price'], 2); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-400">Aucune transaction récente</p>
                <?php endif; ?>
            </div>

            <!-- Graphique -->
            <div class="bg-gray-800 rounded-lg shadow p-6 mt-8">
                <h3 class="text-xl font-bold text-white mb-4">Historique des Transactions</h3>
                <canvas id="transactionChart" class="w-full h-64"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    var transactionChart = document.getElementById('transactionChart').getContext('2d');

    // Prepare wallet data for the chart
    var walletData = <?php
        $labels = [];
        $balances = [];
        foreach($data['wallet'] as $wallet) {
            $labels[] = $wallet->name;
            $balances[] = $wallet->balance;
        }
        echo json_encode([
            'labels' => $labels,
            'data' => $balances
        ]);
        ?>;
    console.log(walletData);

    new Chart(transactionChart, {
        type: 'bar',
        data: {
            labels: walletData.labels,
            datasets: [{
                label: 'Wallet Balance',
                data: walletData.data,
                borderWidth: 1,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>

