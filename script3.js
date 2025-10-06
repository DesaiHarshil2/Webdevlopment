// script3.js
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('login-form');
    const errorMessageDiv = document.getElementById('error-message');

    // Check for URL parameters to show messages
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('expired') === '1') {
        errorMessageDiv.textContent = 'Your session has expired. Please log in again.';
        errorMessageDiv.style.display = 'block';
    } else if (urlParams.get('loggedout') === '1') {
        errorMessageDiv.textContent = 'You have been successfully logged out.';
        errorMessageDiv.style.color = '#28a745';
        errorMessageDiv.style.display = 'block';
    } else if (urlParams.get('error') === 'session_invalid') {
        errorMessageDiv.textContent = 'Invalid session. Please log in again.';
        errorMessageDiv.style.display = 'block';
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            // Prevent the default browser form submission
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            // Clear any previous error messages
            errorMessageDiv.textContent = '';
            errorMessageDiv.style.color = '#dc3545'; // Reset to error color
            errorMessageDiv.style.display = 'none';

            // Show loading state on the button
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Signing in...';
            submitBtn.disabled = true;

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    // Check if the server response is not OK (e.g., 404, 500)
                    if (!response.ok) {
                        throw new Error('Network response was not ok.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // If login is successful, redirect the user to the dashboard
                        // Use the redirect URL from the response if available
                        const redirectUrl = data.redirect || 'dashboard.php';
                        window.location.href = redirectUrl;
                    } else {
                        // Display the error message from the PHP script
                        errorMessageDiv.textContent = data.message;
                        errorMessageDiv.style.display = 'block';
                        // Reset the button state
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    // This catches network errors or issues with the JSON response
                    console.error('Login failed:', error);
                    errorMessageDiv.textContent = 'An unexpected error occurred. Please try again.';
                    errorMessageDiv.style.display = 'block';
                    // Reset the button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    }
});