(function () {
    var thread = document.getElementById('chat-thread');
    var messages = document.getElementById('chat-messages');

    if (!thread || !messages) {
        return;
    }

    function latestMessageId() {
        var lastMessage = messages.lastElementChild;
        return lastMessage && lastMessage.dataset.messageId
            ? Number(lastMessage.dataset.messageId)
            : 0;
    }

    function appendMessage(message) {
        var emptyState = document.getElementById('chat-empty');
        if (emptyState) {
            emptyState.remove();
        }

        var isOwnMessage = message.sender_type === thread.dataset.viewerRole;
        var article = document.createElement('article');
        article.className = 'chat-message ' + (isOwnMessage ? 'chat-message-own' : 'chat-message-other');
        article.dataset.messageId = message.id;

        var author = document.createElement('span');
        author.className = 'chat-message-author';
        author.textContent = isOwnMessage
            ? 'You'
            : message.sender_name;

        var body = document.createElement('p');
        body.textContent = message.message_text;

        var time = document.createElement('time');
        time.textContent = message.created_at;

        article.appendChild(author);
        article.appendChild(body);
        article.appendChild(time);
        messages.appendChild(article);
    }

    function pollMessages() {
        var url = new URL(thread.dataset.pollUrl, window.location.href);
        url.searchParams.set('after_id', String(latestMessageId()));
        url.searchParams.set('user_id', thread.dataset.userId);

        fetch(url.toString(), {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Chat refresh failed with status ' + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                data.messages.forEach(appendMessage);
                if (data.messages.length > 0) {
                    messages.scrollTop = messages.scrollHeight;
                }
            })
            .catch(function (error) {
                console.error('Could not refresh chat messages:', error);
            });
    }

    messages.scrollTop = messages.scrollHeight;
    window.setInterval(pollMessages, 5000);
}());
