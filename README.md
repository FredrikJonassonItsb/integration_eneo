# Eneo AI Integration for Nextcloud

This Nextcloud app integrates the [Eneo AI platform](https://github.com/eneo-ai/eneo) with Nextcloud Hub, providing seamless access to generative AI capabilities directly within your Nextcloud environment.

## Features

- **Single Sign-On (SSO)**: OAuth2-based authentication between Nextcloud and Eneo
- **Smart Picker Integration**: Access Eneo AI assistant from Text, Talk, Mail, and other Nextcloud apps
- **File Context**: Let Eneo analyze and answer questions about your Nextcloud files
- **Reference Provider**: Preview Eneo conversations and content in Nextcloud
- **Privacy-First**: All AI processing happens on your own infrastructure
- **GDPR Compliant**: Full control over data processing and storage

## Requirements

- Nextcloud 26 or later
- A running Eneo instance (see [Eneo documentation](https://github.com/eneo-ai/eneo))
- PHP 8.0 or later
- Node.js 16 or later (for building frontend assets)

## Installation

### From Source

1. Clone this repository into your Nextcloud apps directory:
   ```bash
   cd /var/www/nextcloud/apps
   git clone https://github.com/FredrikJonassonItsb/ITSLyzer.git integration_eneo
   cd integration_eneo
   ```

2. Install dependencies and build frontend assets:
   ```bash
   npm install
   npm run build
   ```

3. Enable the app in Nextcloud:
   ```bash
   sudo -u www-data php occ app:enable integration_eneo
   ```

### From App Store

*(Coming soon)*

## Configuration

### Admin Configuration

1. Go to **Settings** → **Administration** → **AI**
2. Configure the following settings:
   - **Eneo API URL**: The base URL of your Eneo installation (e.g., `http://eneo.example.com`)
   - **OAuth2 Client ID**: The client ID registered in Eneo for Nextcloud
   - **OAuth2 Client Secret**: The client secret from Eneo
   - **Enable Eneo integration**: Toggle to enable/disable the integration

3. Click **Save** to apply the settings
4. Click **Test Connection** to verify the connection to Eneo

### Setting up OAuth2 in Eneo

You need to register Nextcloud as an OAuth2 client in your Eneo instance:

1. Log in to your Eneo admin panel
2. Navigate to **Settings** → **OAuth2 Clients**
3. Create a new OAuth2 client with the following settings:
   - **Client Name**: Nextcloud
   - **Redirect URI**: Copy from Nextcloud admin settings (e.g., `https://nextcloud.example.com/apps/integration_eneo/oauth/callback`)
   - **Scopes**: `read`, `write`, `files`
4. Copy the **Client ID** and **Client Secret** to Nextcloud admin settings

### User Configuration

1. Go to **Settings** → **Personal** → **AI**
2. Enable **Eneo AI assistant**
3. Click **Connect to Eneo** to authorize your Nextcloud account with Eneo
4. Follow the OAuth2 authorization flow

## Usage

### Using the Smart Picker

1. Open any Nextcloud app that supports the Smart Picker (Text, Talk, Mail, etc.)
2. Type `/` to open the Smart Picker
3. Select **Eneo AI Assistant** from the provider list
4. Type your question or request
5. Optionally select files to give Eneo context
6. Press **Ask Eneo** to get a response

### Indexing Files for Context

To allow Eneo to analyze your files:

1. Right-click on a file in Nextcloud Files
2. Select **Index in Eneo** from the context menu
3. Wait for the indexing to complete
4. The file is now available as context for Eneo AI

### Managing Indexed Files

1. Go to **Settings** → **Personal** → **AI**
2. View the list of indexed files
3. Remove files from the index if needed

## Architecture

This app consists of the following components:

### Backend (PHP)

- **Application.php**: App bootstrap and registration
- **EneoAPIService**: Communication with Eneo backend
- **EneoReferenceProvider**: Smart Picker and link preview provider
- **ConfigController**: Configuration API endpoints
- **EneoController**: AI functionality API endpoints
- **Settings**: Admin and personal settings pages

### Frontend (JavaScript/Vue)

- **admin.js**: Admin settings interface
- **personal.js**: Personal settings interface
- **reference.js**: Custom Smart Picker component

### API Endpoints

- `POST /apps/integration_eneo/api/chat`: Send chat message to Eneo
- `POST /apps/integration_eneo/api/index-file`: Index a file for AI context
- `GET /apps/integration_eneo/api/indexed-files`: Get list of indexed files
- `DELETE /apps/integration_eneo/api/remove-from-index`: Remove file from index

## Development

### Building Frontend Assets

```bash
npm install
npm run dev  # Watch mode for development
npm run build  # Production build
```

### Code Style

This project follows Nextcloud coding standards:

```bash
npm run lint  # Check code style
npm run lint:fix  # Fix code style issues
```

### Testing

```bash
# PHP tests
composer install
vendor/bin/phpunit

# JavaScript tests
npm test
```

## Security

### OAuth2 Flow

1. User clicks "Connect to Eneo" in personal settings
2. User is redirected to Eneo's OAuth2 authorization page
3. User authorizes Nextcloud to access their Eneo account
4. Eneo redirects back to Nextcloud with an authorization code
5. Nextcloud exchanges the code for an access token
6. Access token is stored securely in user preferences

### File Access

- Files are accessed using the user's Nextcloud credentials
- Eneo uses the OAuth2 access token to request files via Nextcloud's WebDAV API
- Only files the user has access to can be indexed
- File content is transmitted over HTTPS

### Data Storage

- OAuth2 tokens are stored encrypted in Nextcloud's database
- File embeddings are stored in Eneo's database
- No raw file content is permanently stored in Eneo

## Privacy & GDPR

This integration is designed with privacy and GDPR compliance in mind:

- **Local Processing**: All AI processing happens on your own infrastructure
- **No External Services**: No data is sent to external cloud services
- **User Control**: Users decide which files to index
- **Data Minimization**: Only semantic embeddings are stored, not raw content
- **Right to Erasure**: Users can remove their data at any time

## Troubleshooting

### Connection Failed

- Verify the Eneo API URL is correct
- Check that Eneo is running and accessible from Nextcloud
- Verify firewall rules allow communication between Nextcloud and Eneo

### OAuth2 Authorization Failed

- Verify OAuth2 credentials are correct
- Check that the redirect URI matches exactly
- Ensure the OAuth2 client is enabled in Eneo

### File Indexing Failed

- Check file permissions in Nextcloud
- Verify the file type is supported by Eneo
- Check Eneo logs for errors

## Support

For issues and questions:

- GitHub Issues: https://github.com/FredrikJonassonItsb/ITSLyzer/issues
- Nextcloud Community: https://help.nextcloud.com
- Eneo Documentation: https://github.com/eneo-ai/eneo

## License

This project is licensed under the AGPL-3.0-or-later license.

## Credits

Developed by Sundsvalls kommun as part of the Hubs digital collaboration platform initiative.

Based on the Eneo AI platform: https://github.com/eneo-ai/eneo

## Contributing

Contributions are welcome! Please read our contributing guidelines before submitting pull requests.

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and linting
5. Submit a pull request

## Changelog

### Version 1.0.0 (2025-01-23)

- Initial release
- OAuth2 SSO integration
- Smart Picker provider
- File indexing and context
- Admin and personal settings
- Reference provider for link previews

