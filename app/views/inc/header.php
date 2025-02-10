<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white"> 
        <!-- Navigation -->
        <nav class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-2xl font-bold text-blue-500">NexusCryp</span>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="<?php echo URLROOT; ?>" class="text-blue-500 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="<?php echo URLROOT; ?>/markets" class="text-gray-300 hover:text-blue-500 px-3 py-2 rounded-md text-sm font-medium">Markets</a>
                            <a href="<?php echo URLROOT; ?>/trade" class="text-gray-300 hover:text-blue-500 px-3 py-2 rounded-md text-sm font-medium">Trade</a>
                            <a href="<?php echo URLROOT; ?>/wallet" class="text-gray-300 hover:text-blue-500 px-3 py-2 rounded-md text-sm font-medium">Wallet</a>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <button class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-md text-sm font-medium">Connect Wallet</button>
                </div>
            </div>
        </div>
    </nav>
