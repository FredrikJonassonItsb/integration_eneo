<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Eneo\Settings;

use OCA\Eneo\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;

class Personal implements ISettings {
	private IConfig $config;
	private ?string $userId;

	public function __construct(IConfig $config, ?string $userId) {
		$this->config = $config;
		$this->userId = $userId;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$parameters = [
			'eneo_enabled' => $this->config->getUserValue(
				$this->userId,
				Application::APP_ID,
				'eneo_enabled',
				'1'
			),
			'oauth_connected' => !empty($this->config->getUserValue(
				$this->userId,
				Application::APP_ID,
				'oauth_access_token',
				''
			)),
		];

		return new TemplateResponse(Application::APP_ID, 'personal', $parameters);
	}

	/**
	 * @return string
	 */
	public function getSection(): string {
		return 'ai';
	}

	/**
	 * @return int
	 */
	public function getPriority(): int {
		return 50;
	}
}

