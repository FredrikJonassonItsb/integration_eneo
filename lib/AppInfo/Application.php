<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Eneo\AppInfo;

use OCA\Eneo\Capabilities;
use OCA\Eneo\Reference\EneoReferenceProvider;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Collaboration\Reference\RenderReferenceEvent;

class Application extends App implements IBootstrap {
	public const APP_ID = 'integration_eneo';
	
	public const DEFAULT_ENEO_URL = 'http://localhost:8000';
	public const DEFAULT_REQUEST_TIMEOUT = 60;
	public const USER_AGENT = 'Nextcloud Eneo Integration';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		// Register reference provider for Smart Picker and link previews
		$context->registerReferenceProvider(EneoReferenceProvider::class);
		
		// Register capabilities
		$context->registerCapability(Capabilities::class);
		
		// Register event listener for rendering references
		$context->registerEventListener(
			RenderReferenceEvent::class,
			\OCA\Eneo\Listener\RenderReferenceListener::class
		);
	}

	public function boot(IBootContext $context): void {
		// Boot logic if needed
	}
}

