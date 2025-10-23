<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Eneo\Reference;

use OCA\Eneo\AppInfo\Application;
use OCA\Eneo\Service\EneoAPIService;
use OCP\Collaboration\Reference\ADiscoverableReferenceProvider;
use OCP\Collaboration\Reference\ISearchableReferenceProvider;
use OCP\Collaboration\Reference\Reference;
use OCP\IL10N;
use OCP\IURLGenerator;

class EneoReferenceProvider extends ADiscoverableReferenceProvider implements ISearchableReferenceProvider {
	private IL10N $l10n;
	private IURLGenerator $urlGenerator;
	private EneoAPIService $eneoAPIService;

	public function __construct(
		IL10N $l10n,
		IURLGenerator $urlGenerator,
		EneoAPIService $eneoAPIService
	) {
		$this->l10n = $l10n;
		$this->urlGenerator = $urlGenerator;
		$this->eneoAPIService = $eneoAPIService;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return Application::APP_ID;
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle(): string {
		return $this->l10n->t('Eneo AI Assistant');
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder(): int {
		return 10;
	}

	/**
	 * @inheritDoc
	 */
	public function getIconUrl(): string {
		return $this->urlGenerator->imagePath(Application::APP_ID, 'app.svg');
	}

	/**
	 * @inheritDoc
	 */
	public function getSupportedSearchProviderIds(): array {
		// We'll implement our own custom picker component
		// so we return an empty array here
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function matchReference(string $referenceText): bool {
		// Match Eneo conversation URLs
		// Example: http://localhost:8000/conversation/abc123
		return preg_match('/^https?:\/\/[^\/]+\/conversation\/[a-zA-Z0-9-]+$/', $referenceText) === 1;
	}

	/**
	 * @inheritDoc
	 */
	public function resolveReference(string $referenceText): ?Reference {
		if (!$this->matchReference($referenceText)) {
			return null;
		}

		// Extract conversation ID from URL
		preg_match('/\/conversation\/([a-zA-Z0-9-]+)$/', $referenceText, $matches);
		$conversationId = $matches[1] ?? null;

		if (!$conversationId) {
			return null;
		}

		$reference = new Reference($referenceText);
		$reference->setTitle($this->l10n->t('Eneo AI Conversation'));
		$reference->setDescription($this->l10n->t('AI-powered conversation with Eneo assistant'));
		$reference->setImageUrl($this->urlGenerator->imagePath(Application::APP_ID, 'eneo-logo.png'));
		$reference->setUrl($referenceText);

		return $reference;
	}

	/**
	 * @inheritDoc
	 */
	public function getCachePrefix(string $referenceId): string {
		return Application::APP_ID;
	}

	/**
	 * @inheritDoc
	 */
	public function getCacheKey(string $referenceId): ?string {
		return $referenceId;
	}
}

