<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Eneo\Service;

use OCA\Eneo\AppInfo\Application;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use Exception;

class EneoAPIService {
	private IClientService $clientService;
	private IConfig $config;
	private LoggerInterface $logger;
	private IL10N $l10n;

	public function __construct(
		IClientService $clientService,
		IConfig $config,
		LoggerInterface $logger,
		IL10N $l10n
	) {
		$this->clientService = $clientService;
		$this->config = $config;
		$this->logger = $logger;
		$this->l10n = $l10n;
	}

	/**
	 * Get the Eneo API base URL from configuration
	 */
	private function getEneoUrl(): string {
		return $this->config->getAppValue(
			Application::APP_ID,
			'eneo_url',
			Application::DEFAULT_ENEO_URL
		);
	}

	/**
	 * Get the OAuth2 access token for the user
	 */
	private function getAccessToken(string $userId): ?string {
		return $this->config->getUserValue(
			$userId,
			Application::APP_ID,
			'oauth_access_token',
			null
		);
	}

	/**
	 * Send a chat message to Eneo AI assistant
	 *
	 * @param string $userId The Nextcloud user ID
	 * @param string $message The message to send
	 * @param array $context Optional context (files, previous messages, etc.)
	 * @return array The response from Eneo
	 * @throws Exception If the request fails
	 */
	public function sendChatMessage(string $userId, string $message, array $context = []): array {
		$accessToken = $this->getAccessToken($userId);
		if (!$accessToken) {
			throw new Exception($this->l10n->t('Not authenticated with Eneo. Please configure OAuth2 in settings.'));
		}

		$eneoUrl = $this->getEneoUrl();
		$endpoint = $eneoUrl . '/api/v1/chat';

		$client = $this->clientService->newClient();
		
		$payload = [
			'message' => $message,
			'context' => $context,
			'stream' => false,
		];

		try {
			$response = $client->post($endpoint, [
				'headers' => [
					'Authorization' => 'Bearer ' . $accessToken,
					'Content-Type' => 'application/json',
					'User-Agent' => Application::USER_AGENT,
				],
				'json' => $payload,
				'timeout' => Application::DEFAULT_REQUEST_TIMEOUT,
			]);

			$body = $response->getBody();
			$data = json_decode($body, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new Exception('Invalid JSON response from Eneo');
			}

			return $data;
		} catch (Exception $e) {
			$this->logger->error('Eneo API request failed: ' . $e->getMessage(), [
				'app' => Application::APP_ID,
				'exception' => $e,
			]);
			throw new Exception($this->l10n->t('Failed to communicate with Eneo: %s', [$e->getMessage()]));
		}
	}

	/**
	 * Index a file in Eneo for semantic search
	 *
	 * @param string $userId The Nextcloud user ID
	 * @param string $fileId The Nextcloud file ID
	 * @param string $filePath The file path in Nextcloud
	 * @param string $fileContent The file content
	 * @return array The indexing result
	 * @throws Exception If the request fails
	 */
	public function indexFile(string $userId, string $fileId, string $filePath, string $fileContent): array {
		$accessToken = $this->getAccessToken($userId);
		if (!$accessToken) {
			throw new Exception($this->l10n->t('Not authenticated with Eneo'));
		}

		$eneoUrl = $this->getEneoUrl();
		$endpoint = $eneoUrl . '/api/v1/documents/index';

		$client = $this->clientService->newClient();
		
		$payload = [
			'file_id' => $fileId,
			'file_path' => $filePath,
			'content' => $fileContent,
			'source' => 'nextcloud',
		];

		try {
			$response = $client->post($endpoint, [
				'headers' => [
					'Authorization' => 'Bearer ' . $accessToken,
					'Content-Type' => 'application/json',
					'User-Agent' => Application::USER_AGENT,
				],
				'json' => $payload,
				'timeout' => Application::DEFAULT_REQUEST_TIMEOUT,
			]);

			$body = $response->getBody();
			return json_decode($body, true);
		} catch (Exception $e) {
			$this->logger->error('Eneo file indexing failed: ' . $e->getMessage(), [
				'app' => Application::APP_ID,
				'file_id' => $fileId,
				'exception' => $e,
			]);
			throw new Exception($this->l10n->t('Failed to index file in Eneo: %s', [$e->getMessage()]));
		}
	}

	/**
	 * Get user's indexed files from Eneo
	 *
	 * @param string $userId The Nextcloud user ID
	 * @return array List of indexed files
	 * @throws Exception If the request fails
	 */
	public function getIndexedFiles(string $userId): array {
		$accessToken = $this->getAccessToken($userId);
		if (!$accessToken) {
			throw new Exception($this->l10n->t('Not authenticated with Eneo'));
		}

		$eneoUrl = $this->getEneoUrl();
		$endpoint = $eneoUrl . '/api/v1/documents';

		$client = $this->clientService->newClient();

		try {
			$response = $client->get($endpoint, [
				'headers' => [
					'Authorization' => 'Bearer ' . $accessToken,
					'User-Agent' => Application::USER_AGENT,
				],
				'timeout' => Application::DEFAULT_REQUEST_TIMEOUT,
			]);

			$body = $response->getBody();
			return json_decode($body, true);
		} catch (Exception $e) {
			$this->logger->error('Failed to get indexed files from Eneo: ' . $e->getMessage(), [
				'app' => Application::APP_ID,
				'exception' => $e,
			]);
			throw new Exception($this->l10n->t('Failed to get indexed files: %s', [$e->getMessage()]));
		}
	}

	/**
	 * Test the connection to Eneo
	 *
	 * @return bool True if connection is successful
	 */
	public function testConnection(): bool {
		$eneoUrl = $this->getEneoUrl();
		$endpoint = $eneoUrl . '/api/v1/health';

		$client = $this->clientService->newClient();

		try {
			$response = $client->get($endpoint, [
				'headers' => [
					'User-Agent' => Application::USER_AGENT,
				],
				'timeout' => 10,
			]);

			return $response->getStatusCode() === 200;
		} catch (Exception $e) {
			$this->logger->warning('Eneo connection test failed: ' . $e->getMessage(), [
				'app' => Application::APP_ID,
			]);
			return false;
		}
	}
}

