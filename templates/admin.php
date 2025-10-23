<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

script('integration_eneo', 'integration_eneo-admin');
style('integration_eneo', 'admin');

?>

<div id="eneo_settings" class="section">
	<h2><?php p($l->t('Eneo AI Integration')); ?></h2>
	
	<p class="settings-hint">
		<?php p($l->t('Configure the connection to your Eneo AI platform instance.')); ?>
	</p>

	<div class="eneo-settings-group">
		<h3><?php p($l->t('Eneo Server Configuration')); ?></h3>
		
		<div class="eneo-setting">
			<label for="eneo-url"><?php p($l->t('Eneo API URL')); ?></label>
			<input type="url" 
				   id="eneo-url" 
				   name="eneo_url" 
				   value="<?php p($_['eneo_url']); ?>" 
				   placeholder="http://localhost:8000">
			<p class="settings-hint">
				<?php p($l->t('The base URL of your Eneo installation (e.g., http://eneo.example.com)')); ?>
			</p>
		</div>

		<div class="eneo-setting">
			<label>
				<input type="checkbox" 
					   id="eneo-enabled" 
					   name="enabled" 
					   <?php if ($_['enabled'] === '1'): ?>checked<?php endif; ?>>
				<?php p($l->t('Enable Eneo integration')); ?>
			</label>
		</div>
	</div>

	<div class="eneo-settings-group">
		<h3><?php p($l->t('OAuth2 Configuration')); ?></h3>
		
		<p class="settings-hint">
			<?php p($l->t('Configure OAuth2 credentials for Single Sign-On between Nextcloud and Eneo.')); ?>
			<?php p($l->t('You need to register Nextcloud as an OAuth2 client in your Eneo instance.')); ?>
		</p>

		<div class="eneo-setting">
			<label for="oauth-client-id"><?php p($l->t('OAuth2 Client ID')); ?></label>
			<input type="text" 
				   id="oauth-client-id" 
				   name="oauth_client_id" 
				   value="<?php p($_['oauth_client_id']); ?>" 
				   placeholder="nextcloud-client">
		</div>

		<div class="eneo-setting">
			<label for="oauth-client-secret"><?php p($l->t('OAuth2 Client Secret')); ?></label>
			<input type="password" 
				   id="oauth-client-secret" 
				   name="oauth_client_secret" 
				   value="<?php p($_['oauth_client_secret']); ?>" 
				   placeholder="••••••••••••••••">
		</div>

		<div class="eneo-setting">
			<label><?php p($l->t('OAuth2 Redirect URI')); ?></label>
			<input type="text" 
				   readonly 
				   value="<?php p(\OC::$server->getURLGenerator()->linkToRouteAbsolute('integration_eneo.oauth.callback')); ?>">
			<p class="settings-hint">
				<?php p($l->t('Use this URL when registering Nextcloud as OAuth2 client in Eneo')); ?>
			</p>
		</div>
	</div>

	<div class="eneo-settings-actions">
		<button id="eneo-save-settings" class="button primary">
			<?php p($l->t('Save')); ?>
		</button>
		<button id="eneo-test-connection" class="button">
			<?php p($l->t('Test Connection')); ?>
		</button>
		<span id="eneo-settings-status"></span>
	</div>
</div>

