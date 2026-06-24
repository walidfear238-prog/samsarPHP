// scripts/get-conversation.js
fetch('/api/chat/get_conversation.php?user_id=23&limit=50&offset=0')
.then(response => response.json())
.then(data => {
    if(data.success) {
        console.log('Messages:', data.data);
    }
});

fetch('/api/chat/get_conversations_list.php')
.then(response => response.json())
.then(data => {
    if(data.success) {
        console.log('Conversations:', data.data);
    }
});