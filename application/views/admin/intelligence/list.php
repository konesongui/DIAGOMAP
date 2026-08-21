<style>
    .chat-container {
        max-width: 900px;
        margin: 20px auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 20px;
    }
    .chat-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .chat-header h3 {
        margin: 0;
        color: #3c8dbc;
    }
    .chat-header i {
        font-size: 28px;
        color: #3c8dbc;
    }
    .chat-box {
        height: 450px;
        overflow-y: auto;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        background: #fafafa;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
    }
    .message {
        max-width: 80%;
        margin-bottom: 12px;
        padding: 10px 16px;
        border-radius: 18px;
        word-wrap: break-word;
        line-height: 1.5;
        font-size: 14px;
    }
    .message.user {
        align-self: flex-end;
        background: #007bff;
        color: #fff;
        border-bottom-right-radius: 4px;
    }
    .message.ia {
        align-self: flex-start;
        background: #e9ecef;
        color: #333;
        border-bottom-left-radius: 4px;
    }
    .message .timestamp {
        font-size: 10px;
        opacity: 0.7;
        margin-top: 5px;
        display: block;
    }
    .message.user .timestamp {
        color: #d4e6ff;
    }
    .message.ia .timestamp {
        color: #666;
    }
    .input-group {
        display: flex;
        gap: 10px;
    }
    .input-group input {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #ccc;
        border-radius: 25px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.3s;
    }
    .input-group input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.2);
    }
    .input-group button {
        padding: 12px 28px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 25px;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .input-group button:hover {
        background: #0056b3;
    }
    .input-group button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .typing-indicator {
        padding: 8px 16px;
        color: #888;
        font-style: italic;
        font-size: 14px;
        display: none;
    }
    .typing-indicator i {
        animation: pulse 1.2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 0.2; }
        50% { opacity: 1; }
        100% { opacity: 0.2; }
    }
    .clear-history {
        margin-left: auto;
        background: #dc3545;
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        cursor: pointer;
    }
    .clear-history:hover {
        background: #c82333;
    }
    @media (max-width: 768px) {
        .message {
            max-width: 95%;
        }
        .input-group {
            flex-direction: column;
        }
        .input-group button {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-comments"></i> Assistant IA RH</h1>
    </section>
    <section class="content">
        <div class="chat-container">
            <div class="chat-header">
                <i class="fa fa-robot"></i>
                <h3>Posez votre question RH</h3>
                <button class="clear-history" id="clearHistoryBtn"><i class="fa fa-trash"></i> Effacer l'historique</button>
            </div>

            <div class="chat-box" id="chatBox">
                <div class="message ia">
                    Bonjour 👋 ! Je suis votre assistant RH. Je peux vous renseigner sur vos congés, votre paie, les politiques internes, ou tout autre sujet RH.
                    <span class="timestamp"><?= date('H:i') ?></span>
                </div>
            </div>

            <div id="typing" class="typing-indicator">
                <i class="fa fa-spinner fa-spin"></i> L'IA réfléchit...
            </div>

            <div class="input-group">
                <input type="text" id="questionInput" placeholder="Écrivez votre question ici..." autofocus>
                <button id="sendBtn"><i class="fa fa-paper-plane"></i> Envoyer</button>
            </div>
        </div>
    </section>
</div>

<!-- On définit base_url pour les scripts -->
<script>
    var base_url = '<?= base_url() ?>';
</script>

<script>
    $(document).ready(function() {
        var chatBox = $('#chatBox');
        var input = $('#questionInput');
        var sendBtn = $('#sendBtn');
        var typing = $('#typing');

        function appendMessage(text, isUser) {
            var msgDiv = $('<div class="message"></div>');
            var time = new Date().toLocaleTimeString();
            if (isUser) {
                msgDiv.addClass('user').html(text + '<span class="timestamp">' + time + '</span>');
            } else {
                msgDiv.addClass('ia').html(text + '<span class="timestamp">' + time + '</span>');
            }
            chatBox.append(msgDiv);
            chatBox.scrollTop(chatBox[0].scrollHeight);
        }

        function sendQuestion() {
            var question = input.val().trim();
            if (question === '') {
                input.focus();
                return;
            }
            input.val('');
            appendMessage(question, true);
            typing.show();
            sendBtn.prop('disabled', true);

            $.ajax({
                url: base_url + 'admin/intelligence/chat',
                type: 'POST',
                data: { question: question },
                dataType: 'json',
                success: function(res) {
                    typing.hide();
                    sendBtn.prop('disabled', false);
                    if (res.status === 'success') {
                        var answer = res.answer ? res.answer.replace(/\n/g, '<br>') : 'Réponse vide';
                        appendMessage(answer, false);
                    } else {
                        appendMessage('❌ Erreur : ' + (res.error || 'Réponse non disponible'), false);
                    }
                    input.focus();
                },
                error: function(xhr, status, error) {
                    typing.hide();
                    sendBtn.prop('disabled', false);
                    var errorMsg = '❌ Erreur de connexion au serveur. ';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg += xhr.responseJSON.error;
                    } else if (xhr.responseText) {
                        errorMsg += 'Réponse du serveur : ' + xhr.responseText;
                    } else {
                        errorMsg += 'Veuillez réessayer.';
                    }
                    appendMessage(errorMsg, false);
                    input.focus();
                }
            });
        }

        sendBtn.on('click', sendQuestion);
        input.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                sendQuestion();
            }
        });

        $('#clearHistoryBtn').on('click', function() {
            if (confirm('Voulez-vous vraiment effacer tous les messages affichés ?')) {
                chatBox.empty();
                chatBox.append(`
                    <div class="message ia">
                        Bonjour 👋 ! Je suis votre assistant RH. Je peux vous renseigner sur vos congés, votre paie, les politiques internes, ou tout autre sujet RH.
                        <span class="timestamp"><?= date('H:i') ?></span>
                    </div>
                `);
            }
        });
    });
</script>