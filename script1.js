document.getElementById('add-event-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const form = event.target;
    const formData = new FormData(form);

    fetch('events.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Check for HTTP errors first
        if (!response.ok) {
            // If the response is not OK (e.g., 404, 500), throw an error
            throw new Error('Network response was not ok. Status: ' + response.status);
        }
        return response.text(); // Use .text() to see the raw output
    })
    .then(data => {
        console.log('Server response:', data); // Log the response to the console

        // Check the server response for success or failure message
        // This is a simple example; you might use JSON for a better approach
        if (data.includes('successfully')) {
            alert('Event added successfully!');
            form.reset(); // Clear the form
            // You might want to reload the events list here
        } else {
            // This is the likely source of your current error
            alert('An error occurred. Please try again.'); 
        }
    })
    .catch(error => {
        // This catch block handles network errors or errors from the .then() block
        console.error('Fetch error:', error);
        alert('An error occurred. Please try again.'); // This is the alert you are seeing
    });
});