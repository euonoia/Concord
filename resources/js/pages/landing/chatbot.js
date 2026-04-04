export default function initLandingChatbot() {
    const init = () => {
        const trigger = document.getElementById('chat-trigger');
        const windowChat = document.getElementById('chat-window');
        const closeBtn = document.getElementById('close-chat');
        const sendBtn = document.getElementById('send-btn');
        const input = document.getElementById('chat-input');
        const messagesContainer = document.getElementById('chat-messages');

        if (!trigger || !windowChat || !closeBtn || !sendBtn || !input || !messagesContainer) {
            return;
        }

        const suggestionPhrases = [
        'Hi',
        'Hello',
        'Who are you?',
        'How do I book an appointment?',
        'Tell me about doctors',
        'What careers are available?'
    ];

    const appendMessage = (text, type) => {
        const messageEl = document.createElement('div');
        messageEl.className = `message ${type}`;
        messageEl.textContent = text;
        messagesContainer.appendChild(messageEl);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        return messageEl;
    };

    const fetchBotReply = async (question) => {
        const response = await fetch(`/api/chatbot?question=${encodeURIComponent(question)}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const payload = await response.json();
        return payload.answer || 'Sorry, I could not find a good answer for that.';
    };

    const sendMessage = async (manualText) => {
        const text = typeof manualText === 'string' ? manualText.trim() : input.value.trim();
        if (!text) return;

        appendMessage(text, 'user');
        input.value = '';
        const typingMessage = appendMessage('Thinking...', 'bot');

        try {
            const answer = await fetchBotReply(text);
            typingMessage.remove();
            appendMessage(answer, 'bot');
        } catch (error) {
            typingMessage.remove();
            appendMessage('Sorry, I could not fetch an answer right now. Please try again.', 'bot');
            console.error(error);
        }
    };

    const renderSuggestions = () => {
        const suggestionsEl = document.getElementById('chat-suggestions');
        if (!suggestionsEl) return;

        suggestionsEl.innerHTML = '';
        suggestionPhrases.forEach((phrase) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'suggestion-pill';
            button.textContent = phrase;
            button.addEventListener('click', () => sendMessage(phrase));
            suggestionsEl.appendChild(button);
        });
    };

    const toggleChat = () => {
        windowChat.classList.toggle('chat-hidden');
    };

    trigger.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', () => windowChat.classList.add('chat-hidden'));
    sendBtn.addEventListener('click', () => sendMessage());
    input.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendMessage();
        }
    });

    renderSuggestions();
};

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}
