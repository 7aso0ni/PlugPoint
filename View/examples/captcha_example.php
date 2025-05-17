<div class="max-w-4xl mx-auto py-10 px-6 bg-white shadow-md rounded-lg mt-6">
    <h1 class="text-2xl font-bold mb-6 text-center text-green-600">Form with CAPTCHA</h1>
    
    <form id="demo-form" class="space-y-6" method="POST" action="#">
        <div>
            <label class="block text-gray-700 mb-1">Your Name</label>
            <input type="text" name="name" class="w-full border border-gray-300 px-4 py-2 rounded-lg" required>
        </div>
        
        <div>
            <label class="block text-gray-700 mb-1">Your Email</label>
            <input type="email" name="email" class="w-full border border-gray-300 px-4 py-2 rounded-lg" required>
        </div>
        
        <div>
            <label class="block text-gray-700 mb-1">Message</label>
            <textarea name="message" class="w-full border border-gray-300 px-4 py-2 rounded-lg h-32" required></textarea>
        </div>
        
        <!-- CAPTCHA Container -->
        <div id="captcha-container" class="my-4"></div>
        
        <div class="text-center">
            <button type="submit" id="submit-btn" class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700" disabled>
                Submit
            </button>
        </div>
    </form>
</div>

<script src="assets/js/simple-captcha.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the CAPTCHA
    const captcha = new SimpleCaptcha('captcha-container', {
        theme: 'light',
        size: 'normal',
        onVerify: function(token) {
            // Enable the submit button when CAPTCHA is verified
            document.getElementById('submit-btn').disabled = false;
        },
        onExpire: function() {
            // Disable the submit button when CAPTCHA expires
            document.getElementById('submit-btn').disabled = true;
            alert('CAPTCHA verification has expired. Please verify again.');
        }
    });
    
    // Form submission handler
    document.getElementById('demo-form').addEventListener('submit', function(e) {
        // Prevent form submission if CAPTCHA is not verified
        if (!captcha.isVerified()) {
            e.preventDefault();
            alert('Please verify that you are not a robot before submitting.');
            return false;
        }
        
        // For demo purposes, prevent actual form submission and show success message
        e.preventDefault();
        alert('Form would be submitted with CAPTCHA token: ' + captcha.getToken());
        
        // In a real application, you would submit the form normally
        // and validate the token on the server side
    });
});
</script>
