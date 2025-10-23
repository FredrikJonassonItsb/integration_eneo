<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Eneo\Listener;

use OCA\Eneo\AppInfo\Application;
use OCP\Collaboration\Reference\RenderReferenceEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;

class RenderReferenceListener implements IEventListener {
	public function handle(Event $event): void {
		if (!$event instanceof RenderReferenceEvent) {
			return;
		}

		// Load the custom picker component scripts
		Util::addScript(Application::APP_ID, 'integration_eneo-reference');
	}
}

