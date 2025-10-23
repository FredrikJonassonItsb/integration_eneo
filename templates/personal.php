<?php

/**
 * SPDX-FileCopyrightText: 2025 Sundsvalls kommun
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

script('integration_eneo', 'integration_eneo-personal');
style('integration_eneo', 'personal');

?>

<div id="eneo_personal_settings" class="section">
	<h2><?php p($l->t('Eneo AI Assistant')); ?></h2>
	
	<p class="settings-hint">
		<?php p($l->t('Configure your personal Eneo AI assistant settings.')); ?>
	</p>

	<div class="eneo-setting">
		<label>
			<input type="checkbox" 
				   id="eneo-user-enabled" 
				   name="eneo_enabled" 
				   <?php if ($_['eneo_enabled'] === '1'): ?>checked<?php endif; ?>>
			<?php p($l->t('Enable Eneo AI assistant')); ?>
		</label>
		<p class="settings-hint">
			<?php p($l->t('When enabled, you can use Eneo AI through the Smart Picker in Text, Talk, Mail and other apps.')); ?>
		</p>
	</div>

	<div class="eneo-oauth-status">
		<h3><?php p($l->t('Connection Status')); ?></h3>
		
		<?php if ($_['oauth_connected']): ?>
			<p class="eneo-status-connected">
				<span class="icon-checkmark"></span>
				<?php p($l->t('Connected to Eneo')); ?>
			</p>
			<button id="eneo-disconnect" class="button">
				<?php p($l->t('Disconnect')); ?>
			</button>
		<?php else: ?>
			<p class="eneo-status-disconnected">
				<span class="icon-close"></span>
				<?php p($l->t('Not connected to Eneo')); ?>
			</p>
			<button id="eneo-connect" class="button primary">
				<?php p($l->t('Connect to Eneo')); ?>
			</button>
			<p class="settings-hint">
				<?php p($l->t('You need to connect your Nextcloud account with Eneo to use the AI assistant.')); ?>
			</p>
		<?php endif; ?>
	</div>

	<div class="eneo-usage-info">
		<h3><?php p($l->t('How to use Eneo')); ?></h3>
		<ul>
			<li><?php p($l->t('Use the "/" command in Text, Talk, or Mail to open the Smart Picker')); ?></li>
			<li><?php p($l->t('Select "Eneo AI Assistant" from the provider list')); ?></li>
			<li><?php p($l->t('Type your question or request')); ?></li>
			<li><?php p($l->t('Optionally select files to give Eneo context')); ?></li>
		</ul>
	</div>

	<div class="eneo-privacy-notice">
		<h3><?php p($l->t('Privacy & Data Processing')); ?></h3>
		<p>
			<?php p($l->t('Eneo processes your data locally on your organization\'s infrastructure. No data is sent to external cloud services.')); ?>
		</p>
		<p>
			<?php p($l->t('When you index files for AI context, Eneo creates semantic embeddings that are stored in your organization\'s database.')); ?>
		</p>
		<p>
			<?php p($l->t('You can remove indexed files at any time from your personal settings.')); ?>
		</p>
	</div>
</div>

