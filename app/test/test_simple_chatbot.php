<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Chatbot</title>
    <link rel="stylesheet" href="chatbot/chatbot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <h1>Test Chatbot ePoint</h1>
    <p>Halaman ini untuk test chatbot secara terpisah.</p>
    
    <?php
    // Include chatbot widget
    include 'chatbot_widget.php';
    ?>
    
    <script>
    // Debug script
    console.log('Chatbot test page loaded');
    
    // Test API endpoint
    async function testAPI() {
        try {
            const response = await fetch('./chatbot/chatbot_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: 'test' })
            });
            
            if (response.ok) {
                const data = await response.json();
                console.log('API Response:', data);
                return data;
            } else {
                console.error('API Error:', response.status);
                return null;
            }
        } catch (error) {
            console.error('API Request Failed:', error);
            return null;
        }
    }
    
    // Test API when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Testing API...');
        testAPI().then(result => {
            if (result) {
                console.log('✅ API working correctly');
            } else {
                console.log('❌ API not working');
            }
        });
    });
    </script>
</body>
</html>
