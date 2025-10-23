# Testing Guide - Eneo AI Integration

This document describes how to test the Eneo AI Integration for Nextcloud.

## Test Environment Setup

### Prerequisites

1. **Nextcloud Test Instance**
   - Clean Nextcloud installation (version 26+)
   - Test database (separate from production)
   - Test user accounts

2. **Eneo Test Instance**
   - Running Eneo instance
   - Test OAuth2 client configured
   - Test AI model available

3. **Development Tools**
   - Browser with developer tools
   - curl or Postman for API testing
   - Access to server logs

## Manual Testing Checklist

### 1. Installation Testing

- [ ] App installs without errors
- [ ] App appears in apps list
- [ ] App can be enabled
- [ ] No PHP errors in Nextcloud log
- [ ] Frontend assets are built correctly

**Steps:**
```bash
# Install app
cd /var/www/nextcloud/apps
git clone <repo> integration_eneo
cd integration_eneo
npm install
npm run build

# Enable app
sudo -u www-data php /var/www/nextcloud/occ app:enable integration_eneo

# Check for errors
tail -f /var/www/nextcloud/data/nextcloud.log
```

### 2. Admin Configuration Testing

- [ ] Admin settings page loads
- [ ] Can save Eneo URL
- [ ] Can save OAuth2 credentials
- [ ] Test connection button works
- [ ] Settings persist after page reload
- [ ] Validation works for invalid URLs

**Steps:**
1. Log in as admin
2. Go to Settings → Administration → AI
3. Find "Eneo AI Integration" section
4. Enter test values:
   - Eneo URL: `http://localhost:8000`
   - Client ID: `test-client`
   - Client Secret: `test-secret`
5. Click "Save"
6. Verify success message
7. Reload page and verify values persist
8. Click "Test Connection"
9. Verify connection result

**Expected Results:**
- Settings save successfully
- No JavaScript errors in console
- Test connection shows appropriate status

### 3. Personal Settings Testing

- [ ] Personal settings page loads
- [ ] Can enable/disable Eneo
- [ ] Connect button appears when not connected
- [ ] OAuth2 flow initiates correctly
- [ ] Connection status updates after OAuth
- [ ] Disconnect button works

**Steps:**
1. Log in as regular user
2. Go to Settings → Personal → AI
3. Find "Eneo AI Assistant" section
4. Check "Enable Eneo AI assistant"
5. Click "Connect to Eneo"
6. Complete OAuth2 authorization in Eneo
7. Verify redirect back to Nextcloud
8. Verify "Connected" status shows
9. Click "Disconnect"
10. Verify status changes to "Not connected"

**Expected Results:**
- OAuth2 flow completes successfully
- Token is stored securely
- Status updates correctly

### 4. OAuth2 Flow Testing

- [ ] Authorization URL is correct
- [ ] User can authorize in Eneo
- [ ] Callback URL receives code
- [ ] Token exchange succeeds
- [ ] Token is stored in database
- [ ] Token is used in API requests

**Steps:**
1. Start OAuth2 flow from personal settings
2. Check browser network tab for redirect URL
3. Verify redirect to Eneo authorization page
4. Authorize the application
5. Check callback URL in browser
6. Verify redirect back to settings
7. Check database for stored token:
   ```sql
   SELECT * FROM oc_preferences 
   WHERE appid = 'integration_eneo' 
   AND configkey = 'oauth_access_token';
   ```

**Expected Results:**
- No errors during flow
- Token stored in database
- User can make authenticated requests

### 5. Smart Picker Integration Testing

- [ ] Smart Picker shows Eneo option
- [ ] Custom picker component loads
- [ ] Can type message
- [ ] Can select file context
- [ ] Submit button works
- [ ] Response displays correctly
- [ ] Error handling works

**Steps:**
1. Open Nextcloud Text app
2. Create new document
3. Type `/` to open Smart Picker
4. Verify "Eneo AI Assistant" appears in list
5. Select Eneo option
6. Type test question: "What is 2+2?"
7. Click "Ask Eneo"
8. Wait for response
9. Verify response displays

**Expected Results:**
- Smart Picker opens without errors
- Eneo option is visible
- Response appears within reasonable time
- Response is formatted correctly

### 6. Chat API Testing

- [ ] Can send chat messages
- [ ] Receives responses from Eneo
- [ ] Context is passed correctly
- [ ] Error handling works
- [ ] Timeout handling works

**API Test:**
```bash
# Get user token (from database or OAuth flow)
TOKEN="<user_oauth_token>"

# Test chat endpoint
curl -X POST https://nextcloud.example.com/apps/integration_eneo/api/chat \
  -H "Content-Type: application/json" \
  -H "Cookie: <nextcloud_session_cookie>" \
  -d '{
    "message": "What is the capital of Sweden?",
    "context": {}
  }'
```

**Expected Response:**
```json
{
  "response": "The capital of Sweden is Stockholm.",
  "conversation_id": "abc123",
  "model": "gpt-4"
}
```

### 7. File Indexing Testing

- [ ] Can index text files
- [ ] Can index PDF files
- [ ] Can index Office documents
- [ ] Large files handled correctly
- [ ] Error handling for unsupported files
- [ ] Indexed files list updates

**Steps:**
1. Create test file in Nextcloud Files
2. Right-click file (or use API)
3. Select "Index in Eneo"
4. Wait for indexing to complete
5. Go to Personal Settings → AI
6. Verify file appears in indexed files list
7. Try asking question about file content

**API Test:**
```bash
# Index a file
curl -X POST https://nextcloud.example.com/apps/integration_eneo/api/index-file \
  -H "Content-Type: application/json" \
  -H "Cookie: <nextcloud_session_cookie>" \
  -d '{
    "fileId": 123
  }'
```

**Expected Results:**
- File indexes successfully
- Appears in indexed files list
- Can be used as context in chat

### 8. Reference Provider Testing

- [ ] Eneo URLs are recognized
- [ ] Link previews display correctly
- [ ] Custom widget renders
- [ ] Caching works

**Steps:**
1. Paste Eneo conversation URL in Text app
2. Verify preview appears
3. Check preview content
4. Paste same URL again
5. Verify cached preview loads faster

**Test URLs:**
- `http://localhost:8000/conversation/abc123`
- `https://eneo.example.com/conversation/xyz789`

### 9. Error Handling Testing

- [ ] Invalid Eneo URL shows error
- [ ] Network errors handled gracefully
- [ ] OAuth errors show user-friendly messages
- [ ] API timeouts handled
- [ ] Invalid file IDs handled

**Test Cases:**

**Invalid Eneo URL:**
1. Set Eneo URL to `http://invalid.example.com`
2. Try to connect
3. Verify error message

**Network Error:**
1. Stop Eneo service
2. Try to send chat message
3. Verify error message

**OAuth Error:**
1. Use invalid OAuth credentials
2. Try to connect
3. Verify error message

**API Timeout:**
1. Send very complex query
2. Wait for timeout (60 seconds)
3. Verify timeout error

### 10. Security Testing

- [ ] OAuth tokens not exposed in logs
- [ ] OAuth tokens not exposed in frontend
- [ ] File access respects permissions
- [ ] Admin settings require admin role
- [ ] User can only access own data

**Security Checks:**

**Token Exposure:**
```bash
# Check logs for tokens
grep -r "oauth_access_token" /var/www/nextcloud/data/nextcloud.log

# Should not find any tokens
```

**File Permissions:**
1. Create file as user A
2. Try to index as user B (without access)
3. Verify error

**Admin Access:**
1. Log in as regular user
2. Try to access admin API:
   ```bash
   curl -X POST https://nextcloud.example.com/apps/integration_eneo/admin/config \
     -H "Cookie: <regular_user_session>"
   ```
3. Verify 403 Forbidden

### 11. Performance Testing

- [ ] Chat responses within acceptable time
- [ ] File indexing doesn't block UI
- [ ] Large files handled efficiently
- [ ] Concurrent requests handled
- [ ] Memory usage reasonable

**Performance Benchmarks:**

**Chat Response Time:**
- Simple query: < 5 seconds
- Complex query: < 30 seconds
- With file context: < 10 seconds

**File Indexing Time:**
- Small file (< 1 MB): < 5 seconds
- Medium file (1-10 MB): < 30 seconds
- Large file (> 10 MB): < 2 minutes

**Load Test:**
```bash
# Send 10 concurrent chat requests
for i in {1..10}; do
  curl -X POST https://nextcloud.example.com/apps/integration_eneo/api/chat \
    -H "Content-Type: application/json" \
    -H "Cookie: <session>" \
    -d '{"message":"Test '$i'"}' &
done
wait
```

### 12. Browser Compatibility Testing

Test in multiple browsers:
- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari
- [ ] Edge

**Check:**
- Smart Picker works
- Settings pages load
- No JavaScript errors
- UI renders correctly

### 13. Mobile Testing

Test on mobile devices:
- [ ] Android Chrome
- [ ] iOS Safari

**Check:**
- Smart Picker accessible
- Settings pages usable
- Touch interactions work

## Automated Testing

### PHP Unit Tests

Create PHPUnit tests for backend components:

```php
<?php
// tests/Unit/Service/EneoAPIServiceTest.php

namespace OCA\Eneo\Tests\Unit\Service;

use OCA\Eneo\Service\EneoAPIService;
use PHPUnit\Framework\TestCase;

class EneoAPIServiceTest extends TestCase {
    public function testSendChatMessage() {
        // Mock dependencies
        $clientService = $this->createMock(IClientService::class);
        $config = $this->createMock(IConfig::class);
        $logger = $this->createMock(LoggerInterface::class);
        $l10n = $this->createMock(IL10N::class);
        
        $service = new EneoAPIService($clientService, $config, $logger, $l10n);
        
        // Test chat message
        $result = $service->sendChatMessage('testuser', 'Hello');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('response', $result);
    }
}
```

Run tests:
```bash
vendor/bin/phpunit tests/
```

### JavaScript Tests

Create Jest tests for frontend:

```javascript
// tests/unit/admin.spec.js

import { mount } from '@vue/test-utils'
import AdminSettings from '@/components/AdminSettings.vue'

describe('AdminSettings', () => {
  it('saves settings correctly', async () => {
    const wrapper = mount(AdminSettings)
    
    await wrapper.find('#eneo-url').setValue('http://localhost:8000')
    await wrapper.find('#eneo-save-settings').trigger('click')
    
    // Assert API call was made
    expect(axios.post).toHaveBeenCalledWith(
      '/apps/integration_eneo/admin/config',
      expect.objectContaining({
        eneo_url: 'http://localhost:8000'
      })
    )
  })
})
```

Run tests:
```bash
npm test
```

### Integration Tests

Test full workflows:

```bash
#!/bin/bash
# tests/integration/oauth_flow.sh

# Test OAuth2 flow
echo "Testing OAuth2 flow..."

# 1. Start authorization
AUTH_URL=$(curl -s http://localhost/apps/integration_eneo/oauth/authorize \
  -H "Cookie: $NEXTCLOUD_SESSION" \
  -w "%{redirect_url}")

echo "Authorization URL: $AUTH_URL"

# 2. Authorize in Eneo (automated with headless browser)
# ...

# 3. Verify token stored
TOKEN=$(mysql -u nextcloud -p nextcloud \
  -e "SELECT configvalue FROM oc_preferences 
      WHERE appid='integration_eneo' 
      AND configkey='oauth_access_token'" \
  -s -N)

if [ -n "$TOKEN" ]; then
  echo "✓ OAuth2 flow successful"
else
  echo "✗ OAuth2 flow failed"
  exit 1
fi
```

## Regression Testing

Before each release, run full test suite:

```bash
#!/bin/bash
# tests/regression.sh

echo "Running regression tests..."

# 1. Installation
echo "Testing installation..."
./tests/test_install.sh

# 2. Configuration
echo "Testing configuration..."
./tests/test_config.sh

# 3. OAuth2
echo "Testing OAuth2..."
./tests/test_oauth.sh

# 4. Chat API
echo "Testing chat API..."
./tests/test_chat.sh

# 5. File indexing
echo "Testing file indexing..."
./tests/test_indexing.sh

echo "All tests completed!"
```

## Bug Reporting

When reporting bugs, include:

1. **Environment:**
   - Nextcloud version
   - PHP version
   - Browser and version
   - Eneo version

2. **Steps to Reproduce:**
   - Detailed steps
   - Expected behavior
   - Actual behavior

3. **Logs:**
   - Nextcloud log excerpt
   - Browser console errors
   - Network tab screenshot

4. **Screenshots:**
   - UI state when error occurred
   - Error messages

## Test Coverage Goals

- **Backend (PHP):** > 80% code coverage
- **Frontend (JavaScript):** > 70% code coverage
- **Integration tests:** All critical paths covered
- **Manual testing:** All features tested before release

## Continuous Integration

Set up CI/CD pipeline:

```yaml
# .github/workflows/test.yml

name: Tests

on: [push, pull_request]

jobs:
  php-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: vendor/bin/phpunit

  js-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup Node
        uses: actions/setup-node@v2
        with:
          node-version: '16'
      - name: Install dependencies
        run: npm install
      - name: Run tests
        run: npm test
      - name: Build
        run: npm run build
```

## Conclusion

Thorough testing ensures the Eneo AI Integration works reliably and securely. Follow this guide for each release and when making significant changes.

For questions about testing, see the main README.md or open an issue on GitHub.

