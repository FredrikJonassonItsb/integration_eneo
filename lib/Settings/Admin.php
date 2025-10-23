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

class Admin implements ISettings {
	private IConfig $config;

	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$parameters = [
			'eneo_url' => $this->config->getAppValue(
				Application::APP_ID,
				'eneo_url',
				Application::DEFAULT_ENEO_URL
			),
			'oauth_client_id' => $this->config->getAppValue(
				Application::APP_ID,
				'oauth_client_id',
				''
			),
			'oauth_client_secret' => $this->config->getAppValue(
				Application::APP_ID,
				'oauth_client_secret',
				''
			),
			'enabled' => $this->config->getAppValue(
				Application::APP_ID,
				'enabled',
				'1'
			),
		];

		return new TemplateResponse(Application::APP_ID, 'admin', $parameters);
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

