<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Eneo;

use OCP\Capabilities\ICapability;
use OCP\IConfig;

class Capabilities implements ICapability {
	private IConfig $config;

	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	public function getCapabilities(): array {
		$eneoUrl = $this->config->getAppValue('integration_eneo', 'eneo_url', '');
		$enabled = !empty($eneoUrl);

		return [
			'integration_eneo' => [
				'enabled' => $enabled,
				'version' => '1.0.0',
				'features' => [
					'smart_picker' => true,
					'reference_provider' => true,
					'file_context' => true,
					'oauth2_sso' => true,
				],
			],
		];
	}
}

