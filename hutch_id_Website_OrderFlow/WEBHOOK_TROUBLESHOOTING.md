# 🔍 VERIFY N8N WEBHOOK - TROUBLESHOOTING

**Status**: Webhook offline atau URL tidak tepat  
**Error**: ERR_NGROK_3200 (endpoint offline)

---

## ⚠️ KEMUNGKINAN MASALAH:

### 1. **Ngrok Session Expired**

- Ngrok session mungkin sudah kadaluarsa
- Perlu start ngrok ulang
- Webhook URL berubah

### 2. **URL Path Tidak Tepat**

- Path yang di-copy dari N8N berbeda
- Ada extra `/webhook` di path

### 3. **N8N Service Offline**

- Service di belakang ngrok sudah mati
- N8N container crashed

---

## ✅ VERIFICATION STEPS (Lakukan Sekarang!)

### STEP 1: Check N8N Dashboard Status

1. Buka N8N: **http://localhost:5678**
    - Jika tidak bisa → N8N offline/tidak running
2. Jika bisa akses:
    - Login dengan: `adrianronald99@gmail.com` / `Drian11099`
3. Buka workflow "Hutch Chatbot"

4. Klik **Webhook node** (node pertama)

5. Di panel kanan, lihat **"Webhook URL"**
    - Catat exact URL yang ditampilkan
    - Bandingkan dengan .env

---

### STEP 2: Verify Webhook URL Matches

**Current .env:**

```
N8N_CHATBOT_WEBHOOK_URL=https://henna-empty-plywood.ngrok-free.dev/webhook/hutch-chatbot/webhook/hutch-chatbot
```

**Check di N8N:**

- Buka Webhook node
- Copy exact URL dari N8N UI
- URL harus **EXACTLY** match dengan .env

**Common Issues:**

- ❌ Extra `/webhook` di path
- ❌ Different ngrok session ID
- ❌ Path structure berbeda

---

### STEP 3: Restart Ngrok (Jika Diperlukan)

**Jika ngrok session expired:**

Buka Docker container:

```bash
# Check if ngrok running
docker ps -a | grep ngrok

# Or check N8N environment
docker-compose logs n8n | tail -30
```

**Solution:**

1. Stop docker: `docker-compose down`
2. Start ulang: `docker-compose up -d`
3. Wait 1-2 minutes untuk ngrok reconnect
4. Get new webhook URL dari N8N
5. Update .env dengan URL baru

---

### STEP 4: Test Connection Again

Setelah verify URL benar:

```bash
php test_n8n_webhook.php
```

**Expected:**

```
✅ CONNECTION SUCCESS!
Status Code: 200
🎉 WEBHOOK WORKING!
```

---

## 🆘 QUICK FIX CHECKLIST

- [ ] Akses http://localhost:5678 berhasil?
- [ ] N8N dashboard bisa dibuka?
- [ ] Login berhasil?
- [ ] Workflow "Hutch Chatbot" ditemukan?
- [ ] Webhook node visible?
- [ ] Webhook URL di N8N = URL di .env?
- [ ] Workflow **Active** (toggle switch ON)?
- [ ] Webhook node **Listen** mode ON?

---

## 🔧 JIKA MASIH ERROR:

### Option 1: Get Fresh Webhook URL

1. Di N8N, delete Webhook node lama
2. Add new Webhook node
3. Copy new URL
4. Update .env
5. Test lagi

### Option 2: Restart Everything

```bash
# Stop everything
docker-compose down

# Wait 10 seconds

# Start fresh
docker-compose up -d

# Wait 2 minutes untuk initialization

# Get new webhook URL dari N8N
# Update .env
# Test dengan test_n8n_webhook.php
```

### Option 3: Check N8N Logs

```bash
docker-compose logs -f n8n
```

Look for errors atau warnings tentang webhook.

---

## 📋 DEBUGGING INFO NEEDED

Untuk troubleshoot lebih lanjut, saya butuh tahu:

1. **N8N Dashboard accessible?** (Yes/No)
2. **Workflow active?** (Yes/No)
3. **Exact webhook URL dari N8N UI?** (copy paste)
4. **Error message di N8N logs?** (if any)

---

**Next Action:**

1. Verify webhook URL di N8N UI
2. Update .env dengan exact URL
3. Run: `php test_n8n_webhook.php` lagi
4. Report hasil ✅ atau ❌

---

**Document**: June 20, 2026  
**Version**: 1.0
