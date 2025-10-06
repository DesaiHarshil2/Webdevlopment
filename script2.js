document.addEventListener("DOMContentLoaded", function () {
    const signupForm = document.getElementById("signup-form");

    if (signupForm) {
        signupForm.addEventListener("submit", function (e) {
            e.preventDefault(); // prevent normal form submit

            const firstName = document.getElementById("first-name").value.trim();
            const lastName = document.getElementById("last-name").value.trim();
            const email = document.getElementById("signup-email").value.trim();
            const phone = document.getElementById("phone").value.trim();
            const password = document.getElementById("signup-password").value;
            const confirmPassword = document.getElementById("confirm-password").value;
            const terms = document.querySelector('input[name="terms"]').checked;

            // ✅ Basic validations
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                alert("Please fill in all required fields.");
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert("Invalid email address.");
                return;
            }

            if (password.length < 8) {
                alert("Password must be at least 8 characters.");
                return;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return;
            }

            if (!terms) {
                alert("Please accept the Terms and Conditions.");
                return;
            }

            // ✅ Disable submit button while processing
            const submitBtn = signupForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = "Creating account...";
            submitBtn.disabled = true;

            // ✅ Prepare form data
            const formData = new FormData(signupForm);

            // ✅ Send using Fetch API
            fetch("register.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    // Show success or error message
                    document.body.innerHTML = data; // Replace with PHP response
                })
                .catch(error => {
                    alert("Error: " + error);
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    }
});
