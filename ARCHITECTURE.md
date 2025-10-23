# Architecture Documentation - Eneo AI Integration

This document describes the technical architecture of the Eneo AI Integration for Nextcloud.

## Overview

The integration consists of three main components:

1. **Nextcloud App (PHP Backend)**: Handles API requests, OAuth2, and Nextcloud integration
2. **Frontend (JavaScript/Vue)**: Provides user interfaces for settings and Smart Picker
3. **Eneo Platform**: External AI service that processes requests

```
┌─────────────────────────────────────────────────────────────┐
│                        Nextcloud                             │
│  ┌────────────────────────────────────────────────────────┐ │
│  │              Nextcloud Core                            │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌─────────────┐ │ │
│  │  │   Text App   │  │   Talk App   │  │   Mail App  │ │ │
│  │  └──────┬───────┘  └──────┬───────┘  └──────┬──────┘ │ │
│  │         │                  │                  │        │ │
│  │         └──────────────────┴──────────────────┘        │ │
│  │                            │                            │ │
│  │                    ┌───────▼────────┐                  │ │
│  │                    │  Smart Picker  │                  │ │
│  │                    └───────┬────────┘                  │ │
│  └────────────────────────────┼─────────────────────────┘ │
│                                │                            │
│  ┌────────────────────────────▼─────────────────────────┐ │
│  │          Integration Eneo App                        │ │
│  │  ┌──────────────────────────────────────────────┐   │ │
│  │  │  EneoReferenceProvider (Smart Picker)        │   │ │
│  │  └──────────────────┬───────────────────────────┘   │ │
│  │  ┌──────────────────▼───────────────────────────┐   │ │
│  │  │  EneoController (API Endpoints)              │   │ │
│  │  └──────────────────┬───────────────────────────┘   │ │
│  │  ┌──────────────────▼───────────────────────────┐   │ │
│  │  │  EneoAPIService (HTTP Client)                │   │ │
│  │  └──────────────────┬───────────────────────────┘   │ │
│  └─────────────────────┼──────────────────────────────┘ │
└────────────────────────┼─────────────────────────────────┘
                         │
                         │ HTTPS/OAuth2
                         │
┌────────────────────────▼─────────────────────────────────┐
│                    Eneo Platform                          │
│  ┌────────────────────────────────────────────────────┐  │
│  │  FastAPI Backend                                   │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐ │  │
│  │  │  OAuth2      │  │  Chat API    │  │  Files   │ │  │
│  │  │  Provider    │  │              │  │  API     │ │  │
│  │  └──────────────┘  └──────────────┘  └──────────┘ │  │
│  └────────────────────────────────────────────────────┘  │
│  ┌────────────────────────────────────────────────────┐  │
│  │  AI Services                                       │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐ │  │
│  │  │  LLM         │  │  Embeddings  │  │  Vector  │ │  │
│  │  │  (GPT/Local) │  │  Service     │  │  DB      │ │  │
│  │  └──────────────┘  └──────────────┘  └──────────┘ │  │
│  └────────────────────────────────────────────────────┘  │
└───────────────────────────────────────────────────────────┘
```

## Component Details

### 1. PHP Backend

#### Application.php

The main application class that bootstraps the app and registers components.

**Key responsibilities:**
- Register reference provider for Smart Picker
- Register event listeners
- Register capabilities

**Code location:** `lib/AppInfo/Application.php`

#### EneoAPIService

Service class that handles all communication with the Eneo backend.

**Key methods:**
- `sendChatMessage()`: Send a chat message to Eneo
- `indexFile()`: Index a Nextcloud file in Eneo
- `getIndexedFiles()`: Get list of indexed files
- `testConnection()`: Test connection to Eneo

**Authentication:**
- Uses OAuth2 access tokens stored in user preferences
- Tokens are sent as Bearer tokens in Authorization header

**Code location:** `lib/Service/EneoAPIService.php`

#### EneoReferenceProvider

Implements Nextcloud's reference provider interface for Smart Picker integration.

**Key methods:**
- `getId()`: Returns unique provider ID
- `getTitle()`: Returns provider title shown in Smart Picker
- `matchReference()`: Checks if a URL is an Eneo conversation
- `resolveReference()`: Resolves Eneo URLs to rich previews

**Interfaces implemented:**
- `ADiscoverableReferenceProvider`: Makes provider discoverable in Smart Picker
- `ISearchableReferenceProvider`: Enables search functionality

**Code location:** `lib/Reference/EneoReferenceProvider.php`

#### Controllers

**ConfigController:**
- Admin configuration endpoints
- User configuration endpoints
- Connection testing

**EneoController:**
- Chat API endpoint
- File indexing endpoints
- Indexed files management

**Code location:** `lib/Controller/`

### 2. Frontend (JavaScript/Vue)

#### Admin Settings (admin.js)

Handles the admin configuration interface.

**Features:**
- Save Eneo URL and OAuth2 credentials
- Test connection to Eneo
- Enable/disable integration

**UI Elements:**
- Text inputs for URL and credentials
- Save button with status feedback
- Test connection button

**Code location:** `src/admin.js`

#### Personal Settings (personal.js)

Handles the user configuration interface.

**Features:**
- Enable/disable Eneo for current user
- Connect/disconnect OAuth2
- View connection status

**UI Elements:**
- Enable checkbox
- Connect/disconnect buttons
- Connection status indicator

**Code location:** `src/personal.js`

#### Smart Picker Component (reference.js)

Custom picker component for the Smart Picker interface.

**Features:**
- Text input for user questions
- File context selector
- Submit button
- Response display

**Integration:**
- Registers using `registerCustomPickerElement()`
- Returns `NcCustomPickerRenderResult` with DOM element
- Handles cleanup on destroy

**Code location:** `src/reference.js`

### 3. Data Flow

#### OAuth2 Authentication Flow

```
1. User clicks "Connect to Eneo" in personal settings
   ↓
2. Frontend redirects to /apps/integration_eneo/oauth/authorize
   ↓
3. Backend redirects to Eneo OAuth2 authorization URL
   ↓
4. User authorizes in Eneo
   ↓
5. Eneo redirects back to /apps/integration_eneo/oauth/callback?code=...
   ↓
6. Backend exchanges code for access token
   ↓
7. Backend stores token in user preferences
   ↓
8. User is redirected back to personal settings
```

#### Chat Message Flow

```
1. User types message in Smart Picker
   ↓
2. Frontend calls POST /apps/integration_eneo/api/chat
   ↓
3. EneoController receives request
   ↓
4. EneoAPIService sends request to Eneo
   ├─ Headers: Authorization: Bearer <token>
   ├─ Body: { message, context }
   └─ URL: <eneo_url>/api/v1/chat
   ↓
5. Eneo processes request with AI
   ↓
6. Eneo returns response
   ↓
7. EneoAPIService returns response to controller
   ↓
8. Controller returns JSON response to frontend
   ↓
9. Frontend displays response in Smart Picker
```

#### File Indexing Flow

```
1. User selects file to index
   ↓
2. Frontend calls POST /apps/integration_eneo/api/index-file
   ↓
3. EneoController receives request with file ID
   ↓
4. Controller retrieves file from Nextcloud Files
   ↓
5. Controller reads file content
   ↓
6. EneoAPIService sends to Eneo
   ├─ Headers: Authorization: Bearer <token>
   ├─ Body: { file_id, file_path, content }
   └─ URL: <eneo_url>/api/v1/documents/index
   ↓
7. Eneo creates embeddings and stores in vector DB
   ↓
8. Eneo returns indexing result
   ↓
9. Controller returns success to frontend
```

## Security Architecture

### Authentication

**OAuth2 Flow:**
- Uses standard OAuth2 Authorization Code flow
- Tokens are stored encrypted in Nextcloud database
- Tokens are never exposed to frontend JavaScript
- Tokens are sent only in server-to-server requests

**Token Storage:**
```php
// Stored in oc_preferences table
user_id: 'alice'
app_id: 'integration_eneo'
config_key: 'oauth_access_token'
config_value: '<encrypted_token>'
```

### Authorization

**File Access:**
- Files are accessed using Nextcloud's file system API
- User can only index files they have permission to read
- File content is transmitted over HTTPS
- Eneo receives user's OAuth token for verification

**API Endpoints:**
- All endpoints require authentication (`@NoAdminRequired`)
- Admin endpoints require admin privileges
- User endpoints check user session

### Data Privacy

**What is stored where:**

| Data | Location | Encryption |
|------|----------|------------|
| OAuth2 tokens | Nextcloud DB | Yes (Nextcloud encryption) |
| User settings | Nextcloud DB | No (non-sensitive) |
| File embeddings | Eneo DB | No (semantic vectors) |
| Chat history | Eneo DB | No (opt-in feature) |
| Raw file content | Not stored | N/A (processed on-the-fly) |

**GDPR Compliance:**
- Users control which files are indexed
- Users can remove indexed files at any time
- No data is sent to external services
- All processing happens on-premises

## API Specification

### Nextcloud API Endpoints

#### POST /apps/integration_eneo/api/chat

Send a chat message to Eneo.

**Request:**
```json
{
  "message": "What is the capital of Sweden?",
  "context": {
    "file_ids": [123, 456],
    "current_file": true
  }
}
```

**Response:**
```json
{
  "response": "The capital of Sweden is Stockholm.",
  "conversation_id": "abc123",
  "model": "gpt-4"
}
```

#### POST /apps/integration_eneo/api/index-file

Index a file for AI context.

**Request:**
```json
{
  "fileId": 123
}
```

**Response:**
```json
{
  "status": "success",
  "file_id": "123",
  "indexed_at": "2025-01-23T10:30:00Z",
  "chunks": 5
}
```

#### GET /apps/integration_eneo/api/indexed-files

Get list of indexed files.

**Response:**
```json
{
  "files": [
    {
      "file_id": "123",
      "file_path": "/Documents/report.pdf",
      "indexed_at": "2025-01-23T10:30:00Z",
      "chunks": 5
    }
  ]
}
```

### Eneo API Endpoints (Expected)

#### POST /api/v1/chat

Send a chat message.

**Request:**
```json
{
  "message": "What is the capital of Sweden?",
  "context": {
    "file_ids": ["123", "456"]
  },
  "stream": false
}
```

**Response:**
```json
{
  "response": "The capital of Sweden is Stockholm.",
  "conversation_id": "abc123",
  "model": "gpt-4",
  "tokens_used": 150
}
```

#### POST /api/v1/documents/index

Index a document.

**Request:**
```json
{
  "file_id": "123",
  "file_path": "/Documents/report.pdf",
  "content": "Document content here...",
  "source": "nextcloud"
}
```

**Response:**
```json
{
  "status": "success",
  "file_id": "123",
  "indexed_at": "2025-01-23T10:30:00Z",
  "chunks": 5,
  "embeddings": 5
}
```

## Database Schema

### Nextcloud Tables

**oc_preferences** (User settings):
```sql
CREATE TABLE oc_preferences (
  userid VARCHAR(64),
  appid VARCHAR(32),
  configkey VARCHAR(64),
  configvalue LONGTEXT,
  PRIMARY KEY (userid, appid, configkey)
);

-- Example rows:
-- ('alice', 'integration_eneo', 'oauth_access_token', '<encrypted>')
-- ('alice', 'integration_eneo', 'eneo_enabled', '1')
```

**oc_appconfig** (App settings):
```sql
CREATE TABLE oc_appconfig (
  appid VARCHAR(32),
  configkey VARCHAR(64),
  configvalue LONGTEXT,
  PRIMARY KEY (appid, configkey)
);

-- Example rows:
-- ('integration_eneo', 'eneo_url', 'https://eneo.example.com')
-- ('integration_eneo', 'oauth_client_id', 'nextcloud-abc123')
-- ('integration_eneo', 'oauth_client_secret', '<encrypted>')
```

### Eneo Tables (Expected)

**users**:
- User accounts and OAuth2 mappings

**documents**:
- Indexed documents metadata

**embeddings**:
- Vector embeddings for semantic search

**conversations**:
- Chat history (if enabled)

## Performance Considerations

### Caching

**Nextcloud side:**
- OAuth2 tokens cached in memory during request
- Reference provider results cached by Nextcloud core

**Eneo side:**
- Embeddings cached in vector database
- Model responses can be cached

### Optimization

**File Indexing:**
- Large files processed in chunks
- Indexing happens asynchronously
- Progress feedback to user

**Chat Requests:**
- Timeout set to 60 seconds
- Streaming responses supported (future)
- Connection pooling for HTTP requests

### Scalability

**Horizontal Scaling:**
- Nextcloud: Multiple web servers behind load balancer
- Eneo: Multiple API servers with shared database

**Vertical Scaling:**
- Increase PHP memory limit for large files
- Increase Eneo workers for concurrent requests

## Deployment Architectures

### Small Deployment (Single Server)

```
┌─────────────────────────────────────┐
│         Single Server               │
│  ┌──────────────────────────────┐  │
│  │  Nextcloud (Apache/Nginx)    │  │
│  │  Port 80/443                 │  │
│  └──────────────────────────────┘  │
│  ┌──────────────────────────────┐  │
│  │  Eneo (Docker)               │  │
│  │  Port 8000                   │  │
│  └──────────────────────────────┘  │
│  ┌──────────────────────────────┐  │
│  │  PostgreSQL                  │  │
│  │  Port 5432                   │  │
│  └──────────────────────────────┘  │
└─────────────────────────────────────┘
```

### Medium Deployment (Separate Servers)

```
┌──────────────────┐     ┌──────────────────┐
│  Nextcloud       │────▶│  Eneo Server     │
│  Server          │     │  (Docker)        │
│  Apache/PHP      │     │  FastAPI         │
│  Port 443        │     │  Port 8000       │
└──────────────────┘     └────────┬─────────┘
         │                         │
         │                         │
         ▼                         ▼
┌──────────────────┐     ┌──────────────────┐
│  PostgreSQL      │     │  PostgreSQL      │
│  (Nextcloud)     │     │  (Eneo)          │
│  Port 5432       │     │  Port 5432       │
└──────────────────┘     └──────────────────┘
```

### Large Deployment (High Availability)

```
                    ┌──────────────────┐
                    │  Load Balancer   │
                    │  (HAProxy)       │
                    └────────┬─────────┘
                             │
              ┌──────────────┴──────────────┐
              │                             │
     ┌────────▼────────┐           ┌───────▼────────┐
     │  Nextcloud 1    │           │  Nextcloud 2   │
     │  Apache/PHP     │           │  Apache/PHP    │
     └────────┬────────┘           └───────┬────────┘
              │                             │
              └──────────────┬──────────────┘
                             │
                    ┌────────▼─────────┐
                    │  PostgreSQL      │
                    │  (Primary)       │
                    └────────┬─────────┘
                             │
                    ┌────────▼─────────┐
                    │  PostgreSQL      │
                    │  (Replica)       │
                    └──────────────────┘

                    ┌──────────────────┐
                    │  Load Balancer   │
                    │  (Eneo)          │
                    └────────┬─────────┘
                             │
              ┌──────────────┴──────────────┐
              │                             │
     ┌────────▼────────┐           ┌───────▼────────┐
     │  Eneo 1         │           │  Eneo 2        │
     │  FastAPI        │           │  FastAPI       │
     └────────┬────────┘           └───────┬────────┘
              │                             │
              └──────────────┬──────────────┘
                             │
                    ┌────────▼─────────┐
                    │  PostgreSQL      │
                    │  + pgvector      │
                    └──────────────────┘
```

## Future Enhancements

### Planned Features

1. **Streaming Responses**: Real-time token-by-token responses
2. **Multi-file Context**: Select multiple files for context
3. **Conversation History**: Browse and continue past conversations
4. **File Type Support**: PDF, Word, Excel, PowerPoint
5. **Image Analysis**: Analyze images with vision models
6. **Voice Input**: Speech-to-text for voice queries
7. **Collaborative Spaces**: Share AI conversations with team

### Technical Improvements

1. **WebSocket Support**: For real-time streaming
2. **Background Jobs**: Async file indexing
3. **Caching Layer**: Redis for performance
4. **Metrics**: Prometheus/Grafana monitoring
5. **Rate Limiting**: Prevent abuse
6. **Quota Management**: Per-user limits

## Contributing

See CONTRIBUTING.md for development guidelines and how to contribute to this project.

## References

- [Nextcloud App Development Documentation](https://docs.nextcloud.com/server/stable/developer_manual/)
- [Nextcloud Reference Provider API](https://docs.nextcloud.com/server/stable/developer_manual/digging_deeper/reference.html)
- [Eneo Platform Documentation](https://github.com/eneo-ai/eneo)
- [OAuth2 RFC 6749](https://tools.ietf.org/html/rfc6749)

