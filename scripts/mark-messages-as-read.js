// scripts/mark-messages-as-read.js
// Standalone helper — call this from anywhere to mark a conversation's
// incoming messages as read.
//
// Usage:  markConversationRead(42);

function markConversationRead(conversationId) {
    return fetch('/api/chat/mark-messages-as-read.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ conversation_id: conversationId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log('Marked as read:', data.data.marked_count, 'messages in conversation', conversationId);
        } else {
            console.warn('mark-read failed:', data.message);
        }
        return data;
    })
    .catch(err => console.error('mark-read error:', err));
}