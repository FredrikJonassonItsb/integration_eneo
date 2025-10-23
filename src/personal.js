/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showSuccess, showError } from '@nextcloud/dialogs'

document.addEventListener('DOMContentLoaded', () => {
	const enabledCheckbox = document.getElementById('eneo-user-enabled')
	const connectButton = document.getElementById('eneo-connect')
	const disconnectButton = document.getElementById('eneo-disconnect')

	if (enabledCheckbox) {
		enabledCheckbox.addEventListener('change', async (event) => {
			const enabled = event.target.checked ? '1' : '0'

			try {
				await axios.post(generateUrl('/apps/integration_eneo/user/config'), {
					eneo_enabled: enabled,
				})

				showSuccess(t('integration_eneo', 'Settings saved'))
			} catch (error) {
				console.error('Failed to save settings:', error)
				showError(t('integration_eneo', 'Failed to save settings'))
				// Revert checkbox
				event.target.checked = !event.target.checked
			}
		})
	}

	if (connectButton) {
		connectButton.addEventListener('click', () => {
			// Redirect to OAuth2 authorization
			// This would typically open Eneo's OAuth authorization page
			const eneoAuthUrl = generateUrl('/apps/integration_eneo/oauth/authorize')
			window.location.href = eneoAuthUrl
		})
	}

	if (disconnectButton) {
		disconnectButton.addEventListener('click', async () => {
			try {
				await axios.post(generateUrl('/apps/integration_eneo/user/config'), {
					oauth_access_token: '',
				})

				showSuccess(t('integration_eneo', 'Disconnected from Eneo'))
				
				// Reload page to update UI
				window.location.reload()
			} catch (error) {
				console.error('Failed to disconnect:', error)
				showError(t('integration_eneo', 'Failed to disconnect'))
			}
		})
	}
})

