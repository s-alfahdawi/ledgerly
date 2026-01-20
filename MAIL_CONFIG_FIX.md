# Mail Configuration Fix

## Issue Found
The error log shows SMTP authentication failure. The problem is:
- **Port 465 requires SSL encryption, not TLS**
- Port 587 uses TLS encryption

## Fix Your .env File

### Option 1: Use Port 465 with SSL (Recommended for most hosting)
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.3eyon-host.com
MAIL_PORT=465
MAIL_USERNAME=info@3eyon-host.com
MAIL_PASSWORD="l+#NgE3t@%b["
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=info@3eyon-host.com
MAIL_FROM_NAME="Billing App"
```

**Note:** Wrap password in quotes if it contains special characters.

### Option 2: Use Port 587 with TLS (Alternative)
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.3eyon-host.com
MAIL_PORT=587
MAIL_USERNAME=info@3eyon-host.com
MAIL_PASSWORD="l+#NgE3t@%b["
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@3eyon-host.com
MAIL_FROM_NAME="Billing App"
```

## After Changing .env

1. Clear config cache:
   ```bash
   php artisan config:clear
   ```

2. Test sending an invitation again

## Common Issues

1. **Password with special characters**: Wrap it in quotes in .env
2. **Wrong encryption**: Port 465 = SSL, Port 587 = TLS
3. **Wrong credentials**: Double-check username and password
4. **Firewall**: Some hosting providers block port 465, try 587 instead
