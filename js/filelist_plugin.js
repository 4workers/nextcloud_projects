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

        _extendFileList: function (fileList) {
            // extend row prototype
            var oldCreateRow = fileList._createRow;
            fileList._createRow = function (fileData) {
                var $tr = oldCreateRow.apply(this, arguments);
                var isFavorite = false;
                if (fileData.foreignId) {
                    $tr.attr('data-foreign-id', fileData.foreignId);
                    $tr.addClass('is-project');
                }
                return $tr;
            };

            var oldGetWebdavProperties = fileList._getWebdavProperties;
            fileList._getWebdavProperties = function () {
                var props = oldGetWebdavProperties.apply(this, arguments);
                props.push(OC.Files.Client.PROPERTY_FOREIGN_ID);
                return props;
            };

            fileList.filesClient.addFileInfoParser(function (response) {
                var data = {};
                var props = response.propStat[0].properties;
                var foreignId = props[OC.Files.Client.PROPERTY_FOREIGN_ID];
                if (foreignId) {
                    data.foreignId = foreignId;
                    data.icon = OCP.InitialState.loadState('projects', 'project-icon');
                }
                return data;
            });
        },

        attach: function (fileList) {
			if (this.allowedLists.indexOf(fileList.id) < 0) {
				return;
			}
			this._extendFileList(fileList);
		},
	};
})
(OCA);
OC.Plugins.register('OCA.Files.FileList', OCA.Files.ProjectsPlugin);
