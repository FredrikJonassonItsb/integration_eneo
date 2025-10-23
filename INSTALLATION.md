# Installation Guide - Eneo AI Integration for Nextcloud

This guide provides detailed instructions for installing and configuring the Eneo AI Integration app in Nextcloud.

## Prerequisites

Before installing this app, ensure you have:

1. **Nextcloud 26 or later** installed and running
2. **Eneo AI platform** installed and running (see [Eneo Installation Guide](https://github.com/eneo-ai/eneo))
3. **PHP 8.0 or later** with the following extensions:
   - curl
   - json
   - mbstring
4. **Node.js 16 or later** and npm (for building frontend assets)
5. **Composer** (for PHP dependencies, if needed)

## Step 1: Install the App

### Option A: From Source (Recommended for Development)

1. Navigate to your Nextcloud apps directory:
   ```bash
   cd /var/www/nextcloud/apps
   ```

2. Clone the repository:
   ```bash
   git clone https://github.com/FredrikJonassonItsb/ITSLyzer.git integration_eneo
   cd integration_eneo
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Build frontend assets:
   ```bash
   npm run build
   ```

5. Set correct permissions:
   ```bash
   chown -R www-data:www-data /var/www/nextcloud/apps/integration_eneo
   ```

### Option B: From Release Package

1. Download the latest release from GitHub
2. Extract to Nextcloud apps directory:
   ```bash
   cd /var/www/nextcloud/apps
   tar -xzf integration_eneo-1.0.0.tar.gz
   ```

3. Set correct permissions:
   ```bash
   chown -R www-data:www-data /var/www/nextcloud/apps/integration_eneo
   ```

## Step 2: Enable the App

Enable the app using the Nextcloud command-line tool:

```bash
sudo -u www-data php /var/www/nextcloud/occ app:enable integration_eneo
```

Or enable it through the Nextcloud web interface:
1. Go to **Apps** in the top menu
2. Search for "Eneo"
3. Click **Enable**

## Step 3: Configure Eneo

### 3.1 Set Up OAuth2 in Eneo

1. Log in to your Eneo instance as an administrator
2. Navigate to the OAuth2 settings (typically at `/admin/oauth2`)
3. Create a new OAuth2 application with these settings:

   - **Application Name**: Nextcloud Integration
   - **Redirect URIs**: 
     ```
     https://your-nextcloud-domain.com/apps/integration_eneo/oauth/callback
     ```
     (Replace with your actual Nextcloud domain)
   - **Scopes**: 
     - `read` - Read access to user data
     - `write` - Write access for chat and indexing
     - `files` - Access to file operations

4. Save the application and note down:
   - **Client ID** (e.g., `nextcloud-abc123`)
   - **Client Secret** (e.g., `secret-xyz789`)

### 3.2 Configure API Access

Ensure your Eneo instance is configured to accept API requests from Nextcloud:

1. Edit Eneo's configuration file (typically `config.yaml` or `.env`)
2. Add Nextcloud's domain to the allowed origins:
   ```yaml
   ALLOWED_ORIGINS:
     - https://your-nextcloud-domain.com
   ```

3. Restart Eneo:
   ```bash
   docker-compose restart
   # or
   systemctl restart eneo
   ```

## Step 4: Configure Nextcloud

### 4.1 Admin Configuration

1. Log in to Nextcloud as an administrator
2. Go to **Settings** → **Administration** → **AI**
3. Find the **Eneo AI Integration** section
4. Configure the following:

   **Eneo Server Configuration:**
   - **Eneo API URL**: Enter your Eneo instance URL
     - Example: `https://eneo.example.com`
     - For local development: `http://localhost:8000`
   - **Enable Eneo integration**: Check this box

   **OAuth2 Configuration:**
   - **OAuth2 Client ID**: Enter the Client ID from Step 3.1
   - **OAuth2 Client Secret**: Enter the Client Secret from Step 3.1
   - **OAuth2 Redirect URI**: This is auto-generated, copy it for use in Eneo

5. Click **Save**
6. Click **Test Connection** to verify the setup

### 4.2 Network Configuration

If Nextcloud and Eneo are on different networks, ensure:

1. **Firewall rules** allow communication:
   ```bash
   # On Eneo server, allow Nextcloud's IP
   sudo ufw allow from <nextcloud-ip> to any port 8000
   ```

2. **DNS resolution** works:
   ```bash
   # From Nextcloud server
   curl https://eneo.example.com/api/v1/health
   ```

3. **SSL certificates** are valid (if using HTTPS):
   - Use Let's Encrypt or your organization's CA
   - Ensure certificates are not self-signed (or add to trusted CAs)

## Step 5: User Setup

### 5.1 Connect User Accounts

Each user needs to connect their Nextcloud account with Eneo:

1. Log in to Nextcloud as a regular user
2. Go to **Settings** → **Personal** → **AI**
3. Find the **Eneo AI Assistant** section
4. Check **Enable Eneo AI assistant**
5. Click **Connect to Eneo**
6. You will be redirected to Eneo's authorization page
7. Log in to Eneo (if not already logged in)
8. Click **Authorize** to grant Nextcloud access
9. You will be redirected back to Nextcloud
10. Verify the connection status shows "Connected to Eneo"

### 5.2 Test the Integration

1. Open Nextcloud **Text** app
2. Create a new document
3. Type `/` to open the Smart Picker
4. Select **Eneo AI Assistant**
5. Type a test question: "What is Nextcloud?"
6. Click **Ask Eneo**
7. Verify you receive a response

## Step 6: Advanced Configuration

### 6.1 Docker Deployment

If running both Nextcloud and Eneo in Docker:

**docker-compose.yml example:**

```yaml
version: '3.8'

services:
  nextcloud:
    image: nextcloud:latest
    ports:
      - "8080:80"
    environment:
      - NEXTCLOUD_TRUSTED_DOMAINS=nextcloud.example.com
    volumes:
      - nextcloud_data:/var/www/html
    networks:
      - nextcloud_eneo

  eneo:
    image: eneo/eneo:latest
    ports:
      - "8000:8000"
    environment:
      - ENEO_URL=https://eneo.example.com
      - ALLOWED_ORIGINS=https://nextcloud.example.com
    volumes:
      - eneo_data:/app/data
    networks:
      - nextcloud_eneo

networks:
  nextcloud_eneo:
    driver: bridge

volumes:
  nextcloud_data:
  eneo_data:
```

### 6.2 Reverse Proxy Configuration

If using Nginx as a reverse proxy:

**Nextcloud configuration:**

```nginx
server {
    listen 443 ssl http2;
    server_name nextcloud.example.com;

    ssl_certificate /etc/ssl/certs/nextcloud.crt;
    ssl_certificate_key /etc/ssl/private/nextcloud.key;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

**Eneo configuration:**

```nginx
server {
    listen 443 ssl http2;
    server_name eneo.example.com;

    ssl_certificate /etc/ssl/certs/eneo.crt;
    ssl_certificate_key /etc/ssl/private/eneo.key;

    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # WebSocket support for streaming responses
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }
}
```

### 6.3 Performance Tuning

For optimal performance:

1. **Increase PHP memory limit** (in `php.ini`):
   ```ini
   memory_limit = 512M
   ```

2. **Configure Redis for caching** (in Nextcloud `config.php`):
   ```php
   'memcache.distributed' => '\OC\Memcache\Redis',
   'memcache.locking' => '\OC\Memcache\Redis',
   'redis' => [
       'host' => 'localhost',
       'port' => 6379,
   ],
   ```

3. **Enable background jobs** for file indexing:
   ```bash
   sudo -u www-data php /var/www/nextcloud/occ background:cron
   ```

## Step 7: Verification

### 7.1 Check Logs

**Nextcloud logs:**
```bash
tail -f /var/www/nextcloud/data/nextcloud.log
```

**Eneo logs:**
```bash
# Docker
docker-compose logs -f eneo

# Systemd
journalctl -u eneo -f
```

### 7.2 Test API Endpoints

```bash
# Test Eneo health endpoint
curl https://eneo.example.com/api/v1/health

# Test Nextcloud integration endpoint (requires authentication)
curl -H "Authorization: Bearer <token>" \
     https://nextcloud.example.com/apps/integration_eneo/test-connection
```

## Troubleshooting

### Issue: Connection Test Fails

**Symptoms:** "Failed to connect to Eneo" error

**Solutions:**
1. Verify Eneo is running: `curl http://localhost:8000/api/v1/health`
2. Check firewall rules
3. Verify the Eneo URL in admin settings
4. Check Nextcloud logs for detailed error messages

### Issue: OAuth2 Authorization Fails

**Symptoms:** "Authorization failed" or redirect loop

**Solutions:**
1. Verify OAuth2 credentials match exactly
2. Check redirect URI matches exactly (including https/http)
3. Ensure Eneo OAuth2 client is enabled
4. Clear browser cookies and try again

### Issue: File Indexing Fails

**Symptoms:** "Failed to index file" error

**Solutions:**
1. Check file permissions in Nextcloud
2. Verify file type is supported by Eneo
3. Check Eneo has sufficient disk space
4. Review Eneo logs for specific errors

### Issue: Smart Picker Doesn't Show Eneo

**Symptoms:** Eneo option missing from Smart Picker

**Solutions:**
1. Clear browser cache
2. Verify app is enabled: `php occ app:list`
3. Rebuild frontend assets: `npm run build`
4. Check JavaScript console for errors

## Security Recommendations

1. **Use HTTPS** for both Nextcloud and Eneo in production
2. **Rotate OAuth2 secrets** regularly
3. **Enable 2FA** for admin accounts
4. **Monitor logs** for suspicious activity
5. **Keep software updated** (Nextcloud, Eneo, PHP, etc.)
6. **Limit network access** using firewalls
7. **Use strong passwords** for all accounts

## Backup and Maintenance

### Backup

Include these in your backup strategy:

1. **Nextcloud database** (OAuth2 tokens, user settings)
2. **Eneo database** (indexed files, embeddings)
3. **Configuration files** (Nextcloud config.php, Eneo config)

### Updates

To update the app:

```bash
cd /var/www/nextcloud/apps/integration_eneo
git pull
npm install
npm run build
sudo -u www-data php /var/www/nextcloud/occ app:update integration_eneo
```

## Support

For additional help:

- **Documentation**: See README.md
- **Issues**: https://github.com/FredrikJonassonItsb/ITSLyzer/issues
- **Nextcloud Community**: https://help.nextcloud.com
- **Eneo Documentation**: https://github.com/eneo-ai/eneo

## Next Steps

After successful installation:

1. **Train users** on how to use Eneo AI
2. **Index important documents** for better AI responses
3. **Monitor usage** and adjust resources as needed
4. **Provide feedback** to improve the integration

Congratulations! Your Eneo AI Integration is now installed and ready to use.

