(function (OCA) {

	_.extend(OC.Files.Client, {
		PROPERTY_FOREIGN_ID: '{https://squeegee.com/ns}foreign-id',
		PROPERTY_IS_PROJECT: '{https://squeegee.com/ns}is-project',
	});


	OCA.Files = OCA.Files || {};

	function renderProjectIcon () {
		//TODO: i18n
		//TODO: use templates instead
		return `<div class="project-mark permanent">
					<span class="icon icon-project"></span>
					<span class="hidden-visually">Project</span>
				</div>`;
	}
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
				if (fileData.foreignId) {
					$tr.attr('data-foreign-id', fileData.foreignId);
				}
				if (fileData.isProject) {
					var $icon = $(renderProjectIcon());
					$tr.find('td.filename .thumbnail').append($icon);
				}
				return $tr;
			};

			var oldElementToFile = fileList.elementToFile;
			fileList.elementToFile = function ($el) {
				var data = oldElementToFile.apply(this, arguments);
				data.foreignId = $el.attr('data-foreign-id');
				return data;
			};

			var oldGetWebdavProperties = fileList._getWebdavProperties;
			fileList._getWebdavProperties = function () {
				var props = oldGetWebdavProperties.apply(this, arguments);
				props.push(OC.Files.Client.PROPERTY_FOREIGN_ID);
				props.push(OC.Files.Client.PROPERTY_IS_PROJECT);
				return props;
			};

			fileList.filesClient.addFileInfoParser(function (response) {
				var data = {};
				var props = response.propStat[0].properties;
				var foreignId = props[OC.Files.Client.PROPERTY_FOREIGN_ID];
				if (foreignId) {
					data.foreignId = foreignId;
				}
				data.isProject = props[OC.Files.Client.PROPERTY_IS_PROJECT];
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
})(OCA);

OC.Plugins.register('OCA.Files.FileList', OCA.Files.ProjectsPlugin);