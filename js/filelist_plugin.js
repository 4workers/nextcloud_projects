(function (OCA) {

	_.extend(OC.Files.Client, {
		PROPERTY_FOREIGN_ID: '{' + OC.Files.Client.NS_OWNCLOUD + '}foreign-id',
	});


	OCA.Files = OCA.Files || {};

	/**
	 * Extends file list to highlight projects
	 *
	 * @namespace OCA.Files.TagsPlugin
	 */
	OCA.Files.ProjectsPlugin = {
		name: 'Projects',

		allowedLists: [
			'files'
		],

		attach: function (fileList) {
			if (this.allowedLists.indexOf(fileList.id) < 0) {
				return;
			}
		},
	};
})
(OCA);
OC.Plugins.register('OCA.Files.FileList', OCA.Files.ProjectsPlugin);