<?php
$currentUser = session()->get('user');
if (!is_array($currentUser) || empty($currentUser['logged_in'])) {
    return; // do not render for guests
}

// Decide the counterpart user ID by role: admin <-> cashier
// For simplicity, pick the first user with the opposite role.
$counterpartId = 0;
$counterpartLabel = '';
try {
    $db = \Config\Database::connect();
    $role = (string) ($currentUser['role'] ?? '');
    $otherRole = $role === 'admin' ? 'cashier' : 'admin';
    $row = $db->table('users')->select('id, username')->where('role', $otherRole)->orderBy('id', 'ASC')->get(1)->getRowArray();
    if ($row) {
        $counterpartId = (int) $row['id'];
        $counterpartLabel = (string) $row['username'];
    }
} catch (\Throwable $e) {
    $counterpartId = 0;
}

if ($counterpartId === 0) {
    // No counterpart found; do not render widget
    return;
}
?>
<style>
.chat-fab{position:fixed;right:20px;bottom:20px;width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#0084ff,#00b2ff);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 24px rgba(0,132,255,.45);cursor:pointer;z-index:1050;border:0;transition:transform .1s ease, box-shadow .2s ease}
.chat-fab:hover{transform:translateY(-1px);box-shadow:0 14px 28px rgba(0,132,255,.55)}
.chat-fab:active{transform:translateY(0)}
.chat-fab svg{filter:drop-shadow(0 1px 2px rgba(0,0,0,.25))}
.chat-fab .badge{position:absolute;right:-2px;bottom:-2px;width:18px;height:18px;border-radius:50%;background:#ff3b30;color:#fff;display:none;align-items:center;justify-content:center;font-size:10px;font-weight:700;border:2px solid #fff}
.chat-fab.has-unread .badge{display:flex}
.chat-window{position:fixed;right:20px;bottom:84px;width:320px;max-height:480px;background:#fff;border:1px solid #ddd;border-radius:8px;display:none;flex-direction:column;overflow:hidden;z-index:1050;box-shadow:0 6px 24px rgba(0,0,0,.2)}
.chat-header{background:#0d6efd;color:#fff;padding:10px 12px;display:flex;align-items:center;justify-content:space-between}
.chat-messages{padding:10px;height:300px;overflow-y:auto;background:#f8f9fa}
.chat-input{display:flex;gap:8px;padding:10px;border-top:1px solid #e9ecef}
.msg{max-width:80%;margin:6px 0;padding:8px 10px;border-radius:12px;font-size:14px;word-wrap:break-word}
.msg.me{margin-left:auto;background:#d1e7dd}
.msg.them{margin-right:auto;background:#e9ecef}
.msg .sender{display:block;font-size:12px;font-weight:600;margin-bottom:4px;opacity:.9}
.msg.me .sender{color:#0f5132}
.msg.them .sender{color:#084298}
</style>

<div class="chat-fab" id="chatFab" title="Chat">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12 2C6.48 2 2 6 2 11.12c0 2.73 1.22 5.19 3.23 6.95v3.41c0 .39.42.64.76.43l2.96-1.82c.96.27 1.98.42 3.05.42 5.52 0 10-4 10-9.12S17.52 2 12 2Zm5.12 7.78-2.26 3.6a.75.75 0 0 1-1.08.2l-2.18-1.64a.75.75 0 0 0-.9 0l-3.02 2.28a.75.75 0 0 1-1.08-.2l-1.04-1.66a.75.75 0 0 1 1.08-1.02l.5.8 2.49-1.88a2.25 2.25 0 0 1 2.7 0l1.64 1.24 1.72-2.75a.75.75 0 1 1 1.28.81Z"/>
    </svg>
    <span class="badge" id="chatFabBadge">1</span>
</div>

<div class="chat-window" id="chatWindow">
    <div class="chat-header">
        <strong>Chat with <?php echo esc($counterpartLabel); ?></strong>
        <button type="button" class="btn btn-sm btn-light" id="chatCloseBtn">×</button>
    </div>
    <div class="chat-messages" id="chatMessages"></div>
    <div class="chat-input">
        <input type="text" class="form-control" id="chatMessageInput" placeholder="Type a message...">
        <button class="btn btn-primary" id="chatSendBtn">Send</button>
    </div>
    <?php if (function_exists('csrf_token')): ?>
    <input type="hidden" id="csrfTokenName" value="<?php echo csrf_token(); ?>">
    <input type="hidden" id="csrfTokenHash" value="<?php echo csrf_hash(); ?>">
    <?php endif; ?>
</div>

<script>
(function(){
    const fab = document.getElementById('chatFab');
    const win = document.getElementById('chatWindow');
    const closeBtn = document.getElementById('chatCloseBtn');
    const messagesEl = document.getElementById('chatMessages');
    const inputEl = document.getElementById('chatMessageInput');
    const sendBtn = document.getElementById('chatSendBtn');

    const currentUserId = <?php echo (int) $currentUser['id']; ?>;
    const otherUserId = <?php echo (int) $counterpartId; ?>;
    let lastId = null;
    let polling = null;
    const csrfNameEl = document.getElementById('csrfTokenName');
    const csrfHashEl = document.getElementById('csrfTokenHash');

    function toggleWindow(){
        const visible = win.style.display === 'flex';
        win.style.display = visible ? 'none' : 'flex';
        if (!visible) {
            // Fresh open: reset state and load full history
            lastId = null;
            messagesEl.innerHTML = '';
            fetchMessages();
            startPolling();
            setTimeout(scrollToBottom, 50);
        } else {
            stopPolling();
        }
    }
    fab.addEventListener('click', toggleWindow);
    closeBtn.addEventListener('click', toggleWindow);

    function renderMessages(msgs){
        if (!Array.isArray(msgs) || msgs.length === 0) return;
        const frag = document.createDocumentFragment();
        msgs.forEach(m => {
            const div = document.createElement('div');
            const mine = m.sender_id == currentUserId;
            div.className = 'msg ' + (mine ? 'me' : 'them');
            const role = (m.sender_role || '').toLowerCase() === 'admin' ? 'Admin' : 'Cashier';
            const nameEl = document.createElement('span');
            nameEl.className = 'sender';
            nameEl.textContent = role;
            const textEl = document.createElement('div');
            textEl.textContent = m.message;
            div.appendChild(nameEl);
            div.appendChild(textEl);
            frag.appendChild(div);
            lastId = m.id;
        });
        messagesEl.appendChild(frag);
        scrollToBottom();

        // Show unread badge if widget is closed
        const fab = document.getElementById('chatFab');
        const isClosed = win.style.display !== 'flex';
        if (isClosed) {
            fab.classList.add('has-unread');
        }
    }

    function scrollToBottom(){
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    async function fetchMessages(){
        try {
            const url = new URL('<?php echo site_url('chat/fetch'); ?>', window.location.origin);
            url.searchParams.set('otherUserId', otherUserId);
            if (lastId !== null) url.searchParams.set('sinceId', lastId);
            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }});
            if (!res.ok) return;
            const data = await res.json();
            console.log('fetchMessages ->', data);
            renderMessages(data.messages || []);
        } catch (e) {}
    }

    async function sendMessage(){
        const text = inputEl.value.trim();
        if (!text) return;
        inputEl.value = '';
        try {
            const params = new URLSearchParams({ receiver_id: String(otherUserId), message: text });
            if (csrfNameEl && csrfHashEl) params.set(csrfNameEl.value, csrfHashEl.value);
            const res = await fetch('<?php echo site_url('chat/send'); ?>', { method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' }, body: params });
            if (!res.ok) return;
            const sent = await res.json().catch(()=>({}));
            console.log('sendMessage ->', sent);
            await fetchMessages();
        } catch (e) {}
    }

    sendBtn.addEventListener('click', sendMessage);
    inputEl.addEventListener('keydown', function(ev){ if (ev.key === 'Enter') sendMessage(); });

    function startPolling(){
        if (polling) return;
        polling = setInterval(fetchMessages, 2000);
    }
    function stopPolling(){
        if (polling) { clearInterval(polling); polling = null; }
    }

    // Clear unread badge when opening
    document.getElementById('chatFab').addEventListener('click', function(){
        if (win.style.display !== 'flex') {
            this.classList.remove('has-unread');
        }
    });
})();
</script>

