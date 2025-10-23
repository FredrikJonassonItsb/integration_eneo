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
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;

class ConfigController extends Controller {
	private IConfig $config;
	private IUserSession $userSession;
	private EneoAPIService $eneoAPIService;

	public function __construct(
		IRequest $request,
		IConfig $config,
		IUserSession $userSession,
		EneoAPIService $eneoAPIService
	) {
		parent::__construct(Application::APP_ID, $request);
		$this->config = $config;
		$this->userSession = $userSession;
		$this->eneoAPIService = $eneoAPIService;
	}

	/**
	 * Set admin configuration
	 * 
	 * @AuthorizedAdminSetting(settings=OCA\Eneo\Settings\Admin)
	 */
	public function setAdminConfig(array $values): DataResponse {
		foreach ($values as $key => $value) {
			$this->config->setAppValue(Application::APP_ID, $key, $value);
		}

		return new DataResponse(['status' => 'success']);
	}

	/**
	 * Set user configuration
	 * 
	 * @NoAdminRequired
	 */
	public function setUserConfig(array $values): DataResponse {
		$user = $this->userSession->getUser();
		if (!$user) {
			return new DataResponse(['error' => 'Not logged in'], Http::STATUS_UNAUTHORIZED);
		}

		$userId = $user->getUID();
		foreach ($values as $key => $value) {
			$this->config->setUserValue($userId, Application::APP_ID, $key, $value);
		}

		return new DataResponse(['status' => 'success']);
	}

	/**
	 * Get user configuration
	 * 
	 * @NoAdminRequired
	 */
	public function getUserConfig(): DataResponse {
		$user = $this->userSession->getUser();
		if (!$user) {
			return new DataResponse(['error' => 'Not logged in'], Http::STATUS_UNAUTHORIZED);
		}

		$userId = $user->getUID();
		$config = [
			'oauth_access_token' => $this->config->getUserValue($userId, Application::APP_ID, 'oauth_access_token', ''),
			'eneo_enabled' => $this->config->getUserValue($userId, Application::APP_ID, 'eneo_enabled', '1'),
		];

		return new DataResponse($config);
	}

	/**
	 * Test connection to Eneo
	 * 
	 * @NoAdminRequired
	 */
	public function testConnection(): DataResponse {
		$success = $this->eneoAPIService->testConnection();
		
		return new DataResponse([
			'success' => $success,
			'message' => $success 
				? 'Connection to Eneo successful' 
				: 'Failed to connect to Eneo'
		]);
	}
}

