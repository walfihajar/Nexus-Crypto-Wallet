<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
<div class="max-w-md w-full space-y-8 bg-gray-800 p-8 rounded-xl">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Verify Your Email
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                We've sent a verification code to<br>
                <span class="font-medium text-blue-500"><?php echo isset($data['email']) ? $data['email'] : ''; ?></span>
            </p>
        </div>

        <?php flash('verify_error'); ?>
        <?php flash('verify_message'); ?>

        <form class="mt-8 space-y-6" action="<?php echo URLROOT; ?>/auth/activate" method="POST">
            <div>
                <label for="code" class="block text-sm font-medium text-gray-300">
                    Verification Code
                </label>
                <div class="mt-1">
                    <input id="code" name="code" type="text" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-700 rounded-md shadow-sm bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Enter 6-digit code">
                </div>
            </div>

            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Verify Email
                </button>
            </div>

            <div class="text-center">
                <a href="<?php echo URLROOT; ?>/auth/resend" class="text-sm text-blue-500 hover:text-blue-400">
                    Resend verification code
                </a>
            </div>
        </form>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 