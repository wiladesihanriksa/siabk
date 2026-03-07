// ePoint Chatbot JavaScript
class ePointChatbot {
    constructor() {
        this.isOpen = false;
        this.isTyping = false;
        this.messages = [];
        this.lastMessage = null;
        this.lastMessageTime = 0;
        this.initializeElements();
        this.bindEvents();
        this.hideNotification();
    }

    initializeElements() {
        this.toggle = document.getElementById('chatbot-toggle');
        this.container = document.getElementById('chatbot-container');
        this.close = document.getElementById('chatbot-close');
        this.input = document.getElementById('chatbot-input');
        this.sendBtn = document.getElementById('chatbot-send');
        this.messagesContainer = document.getElementById('chatbot-messages');
        this.notificationBadge = document.getElementById('notification-badge');
    }

    bindEvents() {
        this.toggle.addEventListener('click', () => this.toggleChat());
        this.close.addEventListener('click', () => this.closeChat());
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Quick action buttons - remove existing listeners first
        document.querySelectorAll('.quick-btn').forEach(btn => {
            // Remove any existing listeners
            btn.replaceWith(btn.cloneNode(true));
        });
        
        // Add new listeners
        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const message = e.target.getAttribute('data-message');
                // Add user message to chat first
                this.addMessage(message, 'user');
                // Then send to backend
                this.sendMessageToBackend(message);
            });
        });

        // Auto-resize input
        this.input.addEventListener('input', () => {
            this.input.style.height = 'auto';
            this.input.style.height = this.input.scrollHeight + 'px';
        });
    }

    toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }

    openChat() {
        this.container.classList.add('active');
        this.isOpen = true;
        this.input.focus();
        this.hideNotification();
        this.scrollToBottom();
    }

    closeChat() {
        this.container.classList.remove('active');
        this.isOpen = false;
    }

    hideNotification() {
        this.notificationBadge.style.display = 'none';
    }

    showNotification() {
        this.notificationBadge.style.display = 'flex';
    }

    async sendMessage() {
        const message = this.input.value.trim();
        if (!message || this.isTyping) return;

        // Add user message
        this.addMessage(message, 'user');
        this.input.value = '';
        this.input.style.height = 'auto';

        // Send to backend
        await this.sendMessageToBackend(message);
    }
    
    async sendMessageToBackend(message) {
        if (this.isTyping) return;
        
        // Prevent duplicate messages
        if (this.lastMessage === message && this.lastMessageTime > Date.now() - 1000) {
            return;
        }
        this.lastMessage = message;
        this.lastMessageTime = Date.now();
        
        // Show typing indicator
        this.showTypingIndicator();

        try {
            // Send to backend
            const response = await this.sendToBackend(message);
            this.hideTypingIndicator();
            this.addMessage(response, 'bot');
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
            console.error('Chatbot error:', error);
        }
    }

    addMessage(content, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;

        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        avatar.innerHTML = sender === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';

        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';

        const messageText = document.createElement('p');
        messageText.textContent = content;

        const messageTime = document.createElement('div');
        messageTime.className = 'message-time';
        messageTime.textContent = this.getCurrentTime();

        messageContent.appendChild(messageText);
        messageContent.appendChild(messageTime);

        messageDiv.appendChild(avatar);
        messageDiv.appendChild(messageContent);

        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    showTypingIndicator() {
        this.isTyping = true;
        this.sendBtn.disabled = true;

        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot-message typing-indicator';
        typingDiv.id = 'typing-indicator';

        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        avatar.innerHTML = '<i class="fas fa-robot"></i>';

        const typingContent = document.createElement('div');
        typingContent.className = 'message-content';
        typingContent.innerHTML = `
            <div class="typing-dots">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;

        typingDiv.appendChild(avatar);
        typingDiv.appendChild(typingContent);
        this.messagesContainer.appendChild(typingDiv);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        this.isTyping = false;
        this.sendBtn.disabled = false;
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    async sendToBackend(message) {
        const response = await fetch('./chatbot/chatbot_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        return data.response;
    }

    getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ePointChatbot();
});

// Add some demo functionality for testing
function addDemoMessages() {
    const chatbot = new ePointChatbot();
    
    // Simulate some demo interactions
    setTimeout(() => {
        chatbot.addMessage('Selamat datang di ePoint! Saya siap membantu Anda.', 'bot');
    }, 1000);
}

// Auto-show notification after 3 seconds (demo)
setTimeout(() => {
    const badge = document.getElementById('notification-badge');
    if (badge) {
        badge.style.display = 'flex';
    }
}, 3000);
