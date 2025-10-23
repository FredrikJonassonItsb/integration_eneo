/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { registerCustomPickerElement, NcCustomPickerRenderResult } from '@nextcloud/vue/dist/Components/NcRichText.js'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

// Register custom picker component for Eneo
registerCustomPickerElement('integration_eneo', async (el, { providerId, accessible }) => {
	const container = document.createElement('div')
	container.className = 'eneo-picker-container'
	
	// Create input for user query
	const input = document.createElement('textarea')
	input.className = 'eneo-picker-input'
	input.placeholder = t('integration_eneo', 'Ask Eneo AI assistant...')
	input.rows = 3
	
	// Create file context selector
	const fileSelector = document.createElement('div')
	fileSelector.className = 'eneo-file-selector'
	fileSelector.innerHTML = `
		<label>
			<input type="checkbox" id="eneo-use-context" />
			${t('integration_eneo', 'Use current file as context')}
		</label>
	`
	
	// Create submit button
	const submitButton = document.createElement('button')
	submitButton.className = 'primary'
	submitButton.textContent = t('integration_eneo', 'Ask Eneo')
	submitButton.disabled = true
	
	// Create response container
	const responseContainer = document.createElement('div')
	responseContainer.className = 'eneo-response-container'
	responseContainer.style.display = 'none'
	
	// Enable submit when input has content
	input.addEventListener('input', () => {
		submitButton.disabled = input.value.trim().length === 0
	})
	
	// Handle submit
	submitButton.addEventListener('click', async () => {
		const message = input.value.trim()
		if (!message) return
		
		submitButton.disabled = true
		submitButton.textContent = t('integration_eneo', 'Asking Eneo...')
		
		try {
			const useContext = document.getElementById('eneo-use-context').checked
			const context = useContext ? { current_file: true } : {}
			
			const response = await axios.post(generateUrl('/apps/integration_eneo/api/chat'), {
				message,
				context,
			})
			
			// Display response
			responseContainer.style.display = 'block'
			responseContainer.innerHTML = `
				<div class="eneo-response">
					<strong>${t('integration_eneo', 'Eneo says:')}</strong>
					<p>${response.data.response || response.data.message}</p>
				</div>
			`
			
			// Clear input
			input.value = ''
			submitButton.textContent = t('integration_eneo', 'Ask Eneo')
			submitButton.disabled = true
		} catch (error) {
			console.error('Eneo chat failed:', error)
			showError(t('integration_eneo', 'Failed to get response from Eneo'))
			submitButton.textContent = t('integration_eneo', 'Ask Eneo')
			submitButton.disabled = false
		}
	})
	
	// Assemble UI
	container.appendChild(input)
	container.appendChild(fileSelector)
	container.appendChild(submitButton)
	container.appendChild(responseContainer)
	
	el.appendChild(container)
	
	return new NcCustomPickerRenderResult(container, (vueInstance) => {
		// Cleanup function
		container.remove()
	})
}, (el, { providerId, accessible }) => {
	// Destroy callback
	el.innerHTML = ''
})

