// scripts/mark-messages-as-read.js
fetch('/api/chat/mark_messages_read.php', {
    method: 'PUT',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        sender_id: 23
    })
})
.then(response => response.json())
.then(data => {
    if(data.success) {
        console.log('Messages marked as read:', data.data);
    }
});