<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Hero Section -->
<div class="relative bg-gray-900">
    <!-- Decorative background -->
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 opacity-10"></div>
        <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-gray-900 to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="md:w-3/5">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6">
                Trade Crypto with 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600">
                    Confidence
                </span>
            </h1>
            
            <p class="text-xl text-gray-300 mb-8">
                Experience seamless trading with advanced tools, real-time data, and institutional-grade security.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="<?php echo URLROOT; ?>/register" 
                   class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:text-lg">
                    Get Started
                </a>
                <a href="<?php echo URLROOT; ?>/markets" 
                   class="inline-flex items-center justify-center px-8 py-3 border border-gray-700 text-base font-medium rounded-md text-gray-300 bg-gray-800 hover:bg-gray-700 md:text-lg">
                    View Markets
                </a>
            </div>

            <!-- Trading Stats -->
            <div class="mt-12 grid grid-cols-2 gap-6 sm:grid-cols-3">
                <div>
                    <p class="text-2xl md:text-3xl font-bold text-white">$2.4B+</p>
                    <p class="text-gray-400">24h Trading Volume</p>
                </div>
                <div>
                    <p class="text-2xl md:text-3xl font-bold text-white">2M+</p>
                    <p class="text-gray-400">Registered Users</p>
                </div>
                <div>
                    <p class="text-2xl md:text-3xl font-bold text-white">100+</p>
                    <p class="text-gray-400">Trading Pairs</p>
                </div>
            </div>
        </div>
    </div>
</div>

    

<?php require APPROOT . '/views/inc/footer.php'; ?>