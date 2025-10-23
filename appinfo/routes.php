<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

return [
	'routes' => [
		// Config endpoints
		['name' => 'config#setAdminConfig', 'url' => '/admin/config', 'verb' => 'POST'],
		['name' => 'config#setUserConfig', 'url' => '/user/config', 'verb' => 'POST'],
		['name' => 'config#getUserConfig', 'url' => '/user/config', 'verb' => 'GET'],
		['name' => 'config#testConnection', 'url' => '/test-connection', 'verb' => 'GET'],
		
		// Eneo AI endpoints
		['name' => 'eneo#chat', 'url' => '/api/chat', 'verb' => 'POST'],
		['name' => 'eneo#indexFile', 'url' => '/api/index-file', 'verb' => 'POST'],
		['name' => 'eneo#getIndexedFiles', 'url' => '/api/indexed-files', 'verb' => 'GET'],
		['name' => 'eneo#removeFromIndex', 'url' => '/api/remove-from-index', 'verb' => 'DELETE'],
	],
];

