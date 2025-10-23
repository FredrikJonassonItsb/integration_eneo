<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Eneo\Controller;

use OCA\Eneo\AppInfo\Application;
use OCA\Eneo\Service\EneoAPIService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\Files\IRootFolder;
use OCP\IRequest;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

class EneoController extends Controller {
	private IUserSession $userSession;
	private EneoAPIService $eneoAPIService;
	private IRootFolder $rootFolder;
	private LoggerInterface $logger;

	public function __construct(
		IRequest $request,
		IUserSession $userSession,
		EneoAPIService $eneoAPIService,
		IRootFolder $rootFolder,
		LoggerInterface $logger
	) {
		parent::__construct(Application::APP_ID, $request);
		$this->userSession = $userSession;
		$this->eneoAPIService = $eneoAPIService;
		$this->rootFolder = $rootFolder;
		$this->logger = $logger;
	}

	/**
	 * Send a chat message to Eneo
	 * 
	 * @NoAdminRequired
	 */
	public function chat(string $message, array $context = []): DataResponse {
		$user = $this->userSession->getUser();
		if (!$user) {
			return new DataResponse(['error' => 'Not logged in'], Http::STATUS_UNAUTHORIZED);
		}

		try {
			$userId = $user->getUID();
			$response = $this->eneoAPIService->sendChatMessage($userId, $message, $context);
			
			return new DataResponse($response);
		} catch (\Exception $e) {
			$this->logger->error('Chat request failed: ' . $e->getMessage(), [
				'app' => Application::APP_ID,
			]);
			
			return new DataResponse(
				['error' => $e->getMessage()],
				Http::STATUS_INTERNAL_SERVER_ERROR
			);
		}
	}

	/**
	 * Index a file for AI context
	 * 
	 * @NoAdminRequired
	 */
	public function indexFile(int $fileId): DataResponse {
		$user = $this->userSession->getUser();
		if (!$user) {
			return new DataResponse(['error' => 'Not logged in'], Http::STATUS_UNAUTHORIZED);
		}

		try {
			$userId = $user->getUID();
			$userFolder = $this->rootFolder->getUserFolder($userId);
			
			$files = $userFolder->getById($fileId);
			if (empty($files)) {
				return new DataResponse(
					['error' => 'File not found'],
					Http::STATUS_NOT_FOUND
				);
			}

			$file = $files[0];
			if ($file->getType() !== \OCP\Files\FileInfo::TYPE_FILE) {
				return new DataResponse(
					['error' => 'Not a file'],
					Http::STATUS_BAD_REQUEST
				);
			}

			// Read file content
			$content = $file->getContent();
			$filePath = $userFolder->getRelativePath($file->getPath());
			
			// Index in Eneo
			$result = $this->eneoAPIService->indexFile(
				$userId,
				(string)$fileId,
				$filePath,
				$content
			);
			
			return new DataResponse($result);
		} catch (\Exception $e) {
			$this->logger->error('File indexing failed: ' . $e->getMessage(), [
				'app' => Application::APP_ID,
				'file_id' => $fileId,
			]);
			
			return new DataResponse(
				['error' => $e->getMessage()],
				Http::STATUS_INTERNAL_SERVER_ERROR
			);
		}
	}

	/**
	 * Get list of indexed files
	 * 
	 * @NoAdminRequired
	 */
	public function getIndexedFiles(): DataResponse {
		$user = $this->userSession->getUser();
		if (!$user) {
			return new DataResponse(['error' => 'Not logged in'], Http::STATUS_UNAUTHORIZED);
		}

		try {
			$userId = $user->getUID();
			$files = $this->eneoAPIService->getIndexedFiles($userId);
			
			return new DataResponse($files);
		} catch (\Exception $e) {
			$this->logger->error('Failed to get indexed files: ' . $e->getMessage(), [
				'app' => Application::APP_ID,
			]);
			
			return new DataResponse(
				['error' => $e->getMessage()],
				Http::STATUS_INTERNAL_SERVER_ERROR
			);
		}
	}

	/**
	 * Remove a file from index
	 * 
	 * @NoAdminRequired
	 */
	public function removeFromIndex(string $fileId): DataResponse {
		$user = $this->userSession->getUser();
		if (!$user) {
			return new DataResponse(['error' => 'Not logged in'], Http::STATUS_UNAUTHORIZED);
		}

		// TODO: Implement remove from index in Eneo API
		return new DataResponse(['status' => 'not_implemented'], Http::STATUS_NOT_IMPLEMENTED);
	}
}

