<style>
  /* Chatbot Floating Widget Styles */
  #ds-chatbot-widget {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    font-family: 'DM Sans', sans-serif;
  }
  
  #ds-chat-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--green);
    color: var(--lime);
    border: none;
    box-shadow: 0 4px 12px rgba(23, 63, 54, 0.3);
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: transform 0.2s ease;
  }
  
  #ds-chat-toggle:hover {
    transform: scale(1.05);
  }

  #ds-chat-window {
    display: none;
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    border: 1px solid #eee;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease, transform 0.3s ease;
    transform: translateY(10px);
  }

  #ds-chat-window.is-open {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
  }

  .ds-chat-header {
    background: var(--green);
    color: #fff;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .ds-chat-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 16px;
  }
  
  .ds-chat-header-title i {
    color: var(--lime);
  }

  .ds-chat-close {
    background: transparent;
    border: none;
    color: #fff;
    cursor: pointer;
    display: flex;
  }

  .ds-chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background: #f9fafa;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .ds-chat-msg {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
    white-space: pre-wrap;
  }

  .ds-chat-msg.bot {
    background: #fff;
    border: 1px solid #e0e4e2;
    color: var(--ink);
    border-bottom-left-radius: 4px;
    align-self: flex-start;
  }

  .ds-chat-msg.user {
    background: var(--lime);
    color: var(--ink);
    font-weight: 500;
    border-bottom-right-radius: 4px;
    align-self: flex-end;
  }

  .ds-chat-input-area {
    padding: 12px;
    background: #fff;
    border-top: 1px solid #eee;
    display: flex;
    gap: 8px;
  }

  .ds-chat-input-area input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
    font-family: inherit;
  }
  
  .ds-chat-input-area input:focus {
    border-color: var(--green);
  }

  .ds-chat-send {
    background: var(--green);
    color: #fff;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .ds-chat-send:disabled {
    background: #ccc;
    cursor: not-allowed;
  }

  .ds-typing-indicator {
    display: none;
    align-self: flex-start;
    padding: 10px 14px;
    background: #fff;
    border: 1px solid #e0e4e2;
    border-radius: 12px;
    border-bottom-left-radius: 4px;
  }
  .ds-typing-indicator span {
    display: inline-block;
    width: 6px;
    height: 6px;
    background: #bbb;
    border-radius: 50%;
    margin-right: 4px;
    animation: typing 1.4s infinite ease-in-out both;
  }
  .ds-typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
  .ds-typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
  .ds-typing-indicator span:nth-child(3) { margin-right: 0; }
  
  @keyframes typing {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
  }
</style>

<div id="ds-chatbot-widget">
  <div id="ds-chat-window">
    <div class="ds-chat-header">
      <div class="ds-chat-header-title">
        <i class="material-icons">auto_awesome</i>
        CarShare AI Assistant
      </div>
      <button class="ds-chat-close" id="ds-chat-close-btn">
        <i class="material-icons">close</i>
      </button>
    </div>
    
    <div class="ds-chat-messages" id="ds-chat-messages">
      <div class="ds-chat-msg bot">
        Hello! I'm your CarShare AI assistant. I can help you find rides, check your bookings, or cancel a ride. How can I help you today?
      </div>
      <div class="ds-typing-indicator" id="ds-chat-typing">
        <span></span><span></span><span></span>
      </div>
    </div>
    
    <div class="ds-chat-input-area">
      <input type="text" id="ds-chat-input" placeholder="Ask me anything..." autocomplete="off" />
      <button class="ds-chat-send" id="ds-chat-send-btn">
        <i class="material-icons" style="font-size: 20px;">send</i>
      </button>
    </div>
  </div>

  <button id="ds-chat-toggle">
    <i class="material-icons" style="font-size: 28px;">chat_bubble</i>
  </button>
</div>

<script>
  $(document).ready(function() {
    const $toggleBtn = $('#ds-chat-toggle');
    const $closeBtn = $('#ds-chat-close-btn');
    const $chatWindow = $('#ds-chat-window');
    const $input = $('#ds-chat-input');
    const $sendBtn = $('#ds-chat-send-btn');
    const $messages = $('#ds-chat-messages');
    const $typing = $('#ds-chat-typing');

    function toggleChat() {
      if ($chatWindow.hasClass('is-open')) {
        $chatWindow.removeClass('is-open');
        setTimeout(() => $chatWindow.css('display', 'none'), 300);
      } else {
        $chatWindow.css('display', 'flex');
        // Trigger reflow
        $chatWindow[0].offsetHeight;
        $chatWindow.addClass('is-open');
        $input.focus();
      }
    }

    $toggleBtn.on('click', toggleChat);
    $closeBtn.on('click', toggleChat);

    function scrollToBottom() {
      $messages.scrollTop($messages[0].scrollHeight);
    }

    function addMessage(text, sender) {
      // Escape HTML first to prevent XSS
      let formatted = $('<div>').text(text).html();
      
      // Parse markdown bold **text**
      formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
      // Parse markdown bullets * 
      formatted = formatted.replace(/(^|\n)\*\s/g, '$1• ');
      
      const msgDiv = $('<div class="ds-chat-msg"></div>').addClass(sender).html(formatted);
      msgDiv.insertBefore($typing);
      scrollToBottom();
    }

    function sendMessage() {
      const text = $input.val().trim();
      if (!text) return;

      // Add user message
      addMessage(text, 'user');
      $input.val('');
      $input.prop('disabled', true);
      $sendBtn.prop('disabled', true);
      
      // Show typing indicator
      $typing.css('display', 'block');
      scrollToBottom();

      // Send to backend
      $.ajax({
        url: 'api_chatbot.php',
        method: 'POST',
        data: JSON.stringify({ message: text }),
        contentType: 'application/json',
        success: function(response) {
          $typing.css('display', 'none');
          if (response && response.reply) {
            addMessage(response.reply, 'bot');
          } else {
            addMessage("Sorry, I encountered an error. Please try again.", 'bot');
          }
        },
        error: function() {
          $typing.css('display', 'none');
          addMessage("Network error connecting to the AI server.", 'bot');
        },
        complete: function() {
          $input.prop('disabled', false);
          $sendBtn.prop('disabled', false);
          $input.focus();
        }
      });
    }

    $sendBtn.on('click', sendMessage);
    $input.on('keypress', function(e) {
      if (e.which == 13) {
        sendMessage();
      }
    });
  });
</script>

