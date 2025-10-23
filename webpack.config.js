const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry = {
	'integration_eneo-admin': path.join(__dirname, 'src', 'admin.js'),
	'integration_eneo-personal': path.join(__dirname, 'src', 'personal.js'),
	'integration_eneo-reference': path.join(__dirname, 'src', 'reference.js'),
}

module.exports = webpackConfig

