<div class="max-w-4xl mx-auto py-10 px-6 bg-white shadow-md rounded-lg mt-6">
    <h1 class="text-2xl font-bold mb-6 text-center text-green-600">Human Verification</h1>
    
    <?php if (isset($_SESSION['message_type']) && isset($_SESSION['message'])): ?>
        <div class="<?= $_SESSION['message_type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> px-4 py-3 rounded mb-4 border">
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['message']) ?></span>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <div class="mb-6">
        <p class="text-gray-600 mb-4">Please solve the CAPTCHA below to verify you are human.</p>
        
        <div class="bg-gray-100 p-4 rounded-lg mb-4">
            <div class="flex flex-col items-center">
                <div id="captcha-container" class="mb-4">
                    <img src="<?= htmlspecialchars($_SESSION['captcha_image'] ?? '') ?>" alt="CAPTCHA" class="border border-gray-300 rounded">
                </div>
                <button id="refresh-captcha" class="text-blue-500 hover:text-blue-700 text-sm">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh CAPTCHA
                </button>
            </div>
        </div>
        
        <form method="POST" action="index.php?route=captcha/verify" class="space-y-4">
            <div>
                <label for="captcha_answer" class="block text-gray-700 mb-1">Your Answer:</label>
                <input id="captcha_answer" name="captcha_answer" type="text" class="w-full border border-gray-300 px-4 py-2 rounded-lg" required>
            </div>
            
            <div class="text-center">
                <button type="submit" class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700">
                    Verify
                </button>
            </div>
        </form>
    </div>
    
    <div class="mt-6 text-sm text-gray-500">
        <p>This is a simple CAPTCHA implementation to verify that you are human.</p>
        <p>It uses a math problem that only humans can easily solve.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const refreshButton = document.getElementById('refresh-captcha');
    const captchaContainer = document.getElementById('captcha-container');
    
    refreshButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Show loading state
        captchaContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        
        // Fetch new CAPTCHA
        fetch('index.php?route=captcha/refresh')
            .then(response => response.json())
            .then(data => {
                // Update the CAPTCHA image
                captchaContainer.innerHTML = `<img src="${data.image_path}" alt="CAPTCHA" class="border border-gray-300 rounded">`;
            })
            .catch(error => {
                console.error('Error refreshing CAPTCHA:', error);
                captchaContainer.innerHTML = '<div class="text-red-500">Error loading CAPTCHA. Please try again.</div>';
            });
    });
});
</script>
