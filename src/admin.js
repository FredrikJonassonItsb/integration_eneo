/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showSuccess, showError } from '@nextcloud/dialogs'

document.addEventListener('DOMContentLoaded', () => {
	const saveButton = document.getElementById('eneo-save-settings')
	const testButton = document.getElementById('eneo-test-connection')
	const statusElement = document.getElementById('eneo-settings-status')

	if (saveButton) {
		saveButton.addEventListener('click', async () => {
			const eneoUrl = document.getElementById('eneo-url').value
			const oauthClientId = document.getElementById('oauth-client-id').value
			const oauthClientSecret = document.getElementById('oauth-client-secret').value
			const enabled = document.getElementById('eneo-enabled').checked ? '1' : '0'

			try {
				statusElement.textContent = t('integration_eneo', 'Saving...')
				
				await axios.post(generateUrl('/apps/integration_eneo/admin/config'), {
					eneo_url: eneoUrl,
					oauth_client_id: oauthClientId,
					oauth_client_secret: oauthClientSecret,
					enabled: enabled,
				})

				showSuccess(t('integration_eneo', 'Settings saved successfully'))
				statusElement.textContent = ''
			} catch (error) {
				console.error('Failed to save settings:', error)
				showError(t('integration_eneo', 'Failed to save settings'))
				statusElement.textContent = ''
			}
		})
	}

	if (testButton) {
		testButton.addEventListener('click', async () => {
			try {
				statusElement.textContent = t('integration_eneo', 'Testing connection...')
				
				const response = await axios.get(generateUrl('/apps/integration_eneo/test-connection'))
				
				if (response.data.success) {
					showSuccess(t('integration_eneo', 'Connection successful'))
				} else {
					showError(t('integration_eneo', 'Connection failed'))
				}
				
				statusElement.textContent = ''
			} catch (error) {
				console.error('Connection test failed:', error)
				showError(t('integration_eneo', 'Connection test failed'))
				statusElement.textContent = ''
			}
		})
	}
})

