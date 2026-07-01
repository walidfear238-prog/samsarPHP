/**
 * scripts/chat.js — SAMSAR Messaging
 *
 * Depends on:
 *   window.currentUserId  — set by messages.php
 *   window.SAMSAR_BASE    — set by messages.php (e.g. "/samsar" or "")
 *
 * FIXES APPLIED:
 *  1. API path now uses window.SAMSAR_BASE so it resolves correctly
 *     whether the project lives at localhost/ or localhost/samsar/.
 *  2. Smart-refresh uses lastMsgId (not bubble count) — avoids
 *     wiping optimistic bubbles before the server confirms them.
 *  3. appendBubble removes any pending optimistic bubble before
 *     inserting the server-confirmed one.
 *  4. openChat resets lastMsgId and rebuilds the window cleanly.
 *  5. Error states are surfaced instead of silently swallowed.
 */

(function () {
    'use strict';

    /* ── Config ─────────────────────────────────────────────── */
    // CRITICAL FIX: was hardcoded '/api/chat/' (broke on XAMPP subdirectories).
    // Now uses the base path injected by PHP so it works at any depth.
    const BASE    = (window.SAMSAR_BASE || '').replace(/\/+$/, '');
    const API     = BASE + '/api/chat/';
    const POLL_MS = 5000;
    const LIMIT   = 50;

    /* ── State ───────────────────────────────────────────────── */
    const ME       = parseInt(window.currentUserId) || 0;
    let activeConv = null;   // { id, first, last, avt }
    let pollTimer  = null;
    let lastMsgId  = 0;      // highest message id currently rendered

    /* ── DOM refs ────────────────────────────────────────────── */
    let listEl, winEl;

    /* ── Helpers ─────────────────────────────────────────────── */
    const esc = s =>
        String(s ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

    function fmt(ts) {
        if (!ts) return '';
        return new Date(ts).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    }

    function fmtDate(ts) {
        if (!ts) return '';
        const d = new Date(ts), now = new Date();
        return d.toDateString() === now.toDateString()
            ? fmt(ts)
            : d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
    }

    function initials(f, l) {
        return ((f || '').charAt(0) + (l || '').charAt(0)).toUpperCase() || '?';
    }

    function avatarHTML(src, f, l, sz = 44) {
        if (src) {
            return `<img src="${esc(src)}" alt="${esc(f)} ${esc(l)}"
                style="width:${sz}px;height:${sz}px;border-radius:50%;object-fit:cover;flex-shrink:0;">`;
        }
        return `<div style="width:${sz}px;height:${sz}px;border-radius:50%;background:#C72C41;
            color:#fff;display:flex;align-items:center;justify-content:center;
            font-weight:700;font-size:${Math.round(sz * 0.36)}px;flex-shrink:0;">
            ${initials(f, l)}</div>`;
    }

    function setBadge(n) {
        const el = document.getElementById('bdg-msg');
        if (el) el.textContent = n > 0 ? n : 0;
    }

    /* ── Low-level fetch ─────────────────────────────────────── */
    async function apiFetch(path, opts = {}) {
        const res = await fetch(API + path, opts);
        if (!res.ok) throw new Error(`HTTP ${res.status} on ${path}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'API returned success:false');
        return json.data ?? null;
    }

    /* ─────────────────────────────────────────────────────────
     *  CONVERSATION LIST
     * ───────────────────────────────────────────────────────── */
    async function refreshList() {
        if (!listEl) return;

        let convs;
        try {
            convs = await apiFetch('get-conversations-list.php');
        } catch (err) {
            console.error('[chat] refreshList:', err);
            convs = null;
        }

        if (convs === null) {
            listEl.innerHTML = `<div class="loading" style="color:#C72C41">Could not load conversations.</div>`;
            return;
        }
        if (!convs.length) {
            listEl.innerHTML = `<div style="text-align:center;padding:40px 20px;color:#999;"><p>No conversations yet.</p></div>`;
            setBadge(0);
            return;
        }

        let totalUnread = 0;

        listEl.innerHTML = convs.map(c => {
            const unread   = parseInt(c.unread_count) || 0;
            const isActive = activeConv && String(c.conversation_id) === String(activeConv.id);
            const preview  = c.last_message
                ? (String(c.last_sender_id) === String(ME) ? 'You: ' : '')
                  + esc(c.last_message.substring(0, 45))
                  + (c.last_message.length > 45 ? '…' : '')
                : 'No messages yet';
            totalUnread += unread;

            return `
            <div class="chat-item${isActive ? ' active' : ''}"
                 data-conv="${esc(c.conversation_id)}"
                 data-first="${esc(c.other_firstname || '')}"
                 data-last="${esc(c.other_lastname || '')}"
                 data-avatar="${esc(c.other_avatar || '')}">
                ${avatarHTML(c.other_avatar, c.other_firstname, c.other_lastname)}
                <div class="chat-item-info">
                    <strong>${esc(c.other_firstname)} ${esc(c.other_lastname)}</strong>
                    <p>${preview}</p>
                    ${c.property_title
                        ? `<span style="font-size:11px;color:#C72C41;">${esc(c.property_title)}</span>`
                        : ''}
                </div>
                <div class="chat-item-meta">
                    ${fmtDate(c.last_message_time)}
                    ${unread > 0 && !isActive
                        ? `<div class="unread-badge">${unread}</div>`
                        : ''}
                </div>
            </div>`;
        }).join('');

        setBadge(totalUnread);

        // Attach click handlers
        listEl.querySelectorAll('.chat-item').forEach(el => {
            el.addEventListener('click', function () {
                const id    = parseInt(this.dataset.conv);
                const first = this.dataset.first;
                const last  = this.dataset.last;
                const avt   = this.dataset.avatar;

                listEl.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('.unread-badge')?.remove();

                openChat(id, first, last, avt);
            });
        });
    }

    /* ─────────────────────────────────────────────────────────
     *  MESSAGES
     * ───────────────────────────────────────────────────────── */
    function renderBubble(m) {
        const isMe = String(m.sender_id) === String(ME);
        return `<div class="bubble ${isMe ? 'me' : 'them'}" data-id="${esc(m.id)}">
            ${esc(m.message)}<small>${fmt(m.created_at)}</small>
        </div>`;
    }

    /**
     * Appends a single bubble for the optimistic send.
     * Removes any pending bubble first so we don't duplicate.
     */
    function appendBubble(bodyEl, msgData) {
        if (!bodyEl) return;
        // Remove previous optimistic bubble (data-id="pending") if any
        bodyEl.querySelector('[data-id="pending"]')?.remove();

        const div = document.createElement('div');
        div.className  = 'bubble me';
        div.dataset.id = msgData.id || 'pending';
        div.innerHTML  = `${esc(msgData.message)}<small>${fmt(msgData.created_at)}</small>`;
        bodyEl.appendChild(div);
        bodyEl.scrollTop = bodyEl.scrollHeight;

        // Advance our high-water mark so the next poll skips a pointless re-render
        if (msgData.id) lastMsgId = Math.max(lastMsgId, parseInt(msgData.id));
    }

    /**
     * FIX: uses lastMsgId (not bubble count) to decide whether to re-render.
     * This prevents the poll from wiping an optimistic bubble before the DB
     * round-trip finishes.
     */
    async function refreshMessages() {
        if (!activeConv) return;
        const bodyEl = document.getElementById('chat-body');
        if (!bodyEl) return;

        let msgs;
        try {
            msgs = await apiFetch(
                `get-conversation.php?conversation_id=${activeConv.id}&limit=${LIMIT}`
            );
        } catch {
            return; // silently ignore poll errors
        }

        if (!msgs || !msgs.length) return;

        const maxId = msgs.reduce((mx, m) => Math.max(mx, parseInt(m.id) || 0), 0);
        if (maxId > 0 && maxId === lastMsgId) return; // nothing new

        // Full re-render
        bodyEl.innerHTML = msgs.map(renderBubble).join('');
        bodyEl.scrollTop = bodyEl.scrollHeight;
        lastMsgId = maxId;

        markRead(activeConv.id);
    }

    /* ─────────────────────────────────────────────────────────
     *  OPEN CHAT WINDOW
     * ───────────────────────────────────────────────────────── */
    function openChat(id, first, last, avt) {
        activeConv = { id, first, last, avt };
        lastMsgId  = 0;   // reset so the first poll doesn't skip the initial render

        winEl.innerHTML = `
            <div style="display:flex;flex-direction:column;height:100%;">
                <div class="chat-head">
                    ${avatarHTML(avt, first, last, 42)}
                    <div>
                        <strong>${esc(first)} ${esc(last)}</strong>
                        <span id="chat-status">Loading…</span>
                    </div>
                </div>
                <div class="chat-body" id="chat-body">
                    <div class="loading">Loading messages…</div>
                </div>
                <div class="chat-form">
                    <input type="text" id="chat-input" placeholder="Type a message…" autocomplete="off">
                    <button id="send-btn">Send</button>
                </div>
            </div>`;

        const statusEl = document.getElementById('chat-status');

        /* ── Initial message load ── */
        (async () => {
            const bodyEl = document.getElementById('chat-body');
            let msgs;
            try {
                msgs = await apiFetch(
                    `get-conversation.php?conversation_id=${id}&limit=${LIMIT}`
                );
            } catch (err) {
                if (bodyEl) bodyEl.innerHTML =
                    `<div class="loading" style="color:#C72C41">Error loading messages.</div>`;
                console.error('[chat] initial load:', err);
                return;
            }

            if (!msgs || !msgs.length) {
                if (bodyEl) bodyEl.innerHTML =
                    `<div style="text-align:center;padding:40px;color:#999;">No messages yet. Say hello 👋</div>`;
            } else {
                if (bodyEl) {
                    bodyEl.innerHTML    = msgs.map(renderBubble).join('');
                    bodyEl.scrollTop    = bodyEl.scrollHeight;
                    lastMsgId = msgs.reduce((mx, m) => Math.max(mx, parseInt(m.id) || 0), 0);
                }
            }

            if (statusEl) statusEl.textContent = '';
            markRead(id);
        })();

        /* ── Send handler ── */
        async function doSend() {
            const inputEl = document.getElementById('chat-input');
            const btnEl   = document.getElementById('send-btn');
            const bodyEl  = document.getElementById('chat-body');

            if (!inputEl || !btnEl) return;
            const text = inputEl.value.trim();
            if (!text || !activeConv || activeConv.id !== id) return;

            inputEl.disabled  = true;
            btnEl.disabled    = true;
            btnEl.textContent = '…';

            let result = null;
            try {
                result = await apiFetch('send-message.php', {
                    method : 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body   : JSON.stringify({ conversation_id: id, message: text })
                });
            } catch (err) {
                console.error('[chat] sendMessage:', err);
                alert('Failed to send. Please try again.');
            }

            if (result) {
                inputEl.value = '';
                appendBubble(bodyEl, result);
                refreshList(); // update "last message" preview in sidebar
            }

            inputEl.disabled  = false;
            btnEl.disabled    = false;
            btnEl.textContent = 'Send';
            inputEl.focus();
        }

        document.getElementById('send-btn')
            ?.addEventListener('click', doSend);
        document.getElementById('chat-input')
            ?.addEventListener('keydown', e => {
                if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); doSend(); }
            });
        document.getElementById('chat-input')?.focus();

        markRead(id);
        startPolling();
    }

    /* ─────────────────────────────────────────────────────────
     *  POLLING
     * ───────────────────────────────────────────────────────── */
    function startPolling() {
        stopPolling();
        pollTimer = setInterval(() => {
            refreshMessages();
            refreshList();
        }, POLL_MS);
    }

    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    /* ─────────────────────────────────────────────────────────
     *  MARK READ
     * ───────────────────────────────────────────────────────── */
    async function markRead(convId) {
        try {
            await fetch(API + 'mark-messages-as-read.php', {
                method : 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body   : JSON.stringify({ conversation_id: convId })
            });
        } catch { /* silently ignore */ }
    }

    /* ─────────────────────────────────────────────────────────
     *  INIT
     * ───────────────────────────────────────────────────────── */
    function init() {
        listEl = document.getElementById('chat-list');
        winEl  = document.getElementById('chat-window');

        if (!listEl || !winEl) {
            console.warn('[chat] Required elements #chat-list / #chat-window not found.');
            return;
        }

        refreshList();

        // Auto-open from URL: messages.php?open=<conversation_id>
        const openId = new URLSearchParams(location.search).get('open');
        if (openId) {
            apiFetch('get-conversations-list.php')
                .then(convs => {
                    if (!convs) return;
                    const c = convs.find(x => String(x.conversation_id) === String(openId));
                    if (c) openChat(
                        parseInt(c.conversation_id),
                        c.other_firstname || '',
                        c.other_lastname  || '',
                        c.other_avatar    || ''
                    );
                })
                .catch(() => {});
        }

        window.addEventListener('beforeunload', stopPolling);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();