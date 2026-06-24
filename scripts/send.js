// scripts/send.js
fetch('/api/chat/send_message.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        receiver_id: 23,
        message: 'Hello! How are you?',
        parent_message_id: null // optional
    })
})
.then(response => response.json())
.then(data => {
    if(data.success) {
        console.log('Message sent:', data.data);
    } else {
        console.error('Error:', data.message);
    }
});