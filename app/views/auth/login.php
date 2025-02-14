<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-gray-800 p-8 rounded-xl">
        <!-- Header -->
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Welcome back
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Or
                <a href="<?php echo URLROOT; ?>/auth/register" class="font-medium text-blue-500 hover:text-blue-400">
                    create a new account
                </a>
            </p>
        </div>

        <!-- Login Form -->
        <form class="mt-8 space-y-6" action="<?php echo URLROOT; ?>/auth/login" method="POST">
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">
                    Email address
                </label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">
                    Password
                </label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <!-- Remember me & Forgot password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" 
                        class="h-4 w-4 text-blue-500 focus:ring-blue-500 border-gray-700 rounded bg-gray-700">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-300">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="<?php echo URLROOT; ?>/auth/forgot" class="font-medium text-blue-500 hover:text-blue-400">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <!-- Error Message (if any) -->
            <?php if(isset($data['error'])) : ?>
                <div class="bg-red-900/50 text-red-400 p-3 rounded-md text-sm">
                    <?php echo $data['error']; ?>
                </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Sign in
                </button>
            </div>
        </form>

<?php require APPROOT . '/views/inc/footer.php'; ?>