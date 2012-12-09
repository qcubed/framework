<?php
	require_once('../qcubed.inc.php');

	// Security check for ALLOW_REMOTE_ADMIN
	// To allow access REGARDLESS of ALLOW_REMOTE_ADMIN, simply remove the line below
	QApplication::CheckRemoteAdmin();

	// Iterate through the files in this "form_drafts" folder, looking for files
	// that end in _edit.php or _list.php
	$strSuffixes = array('_edit.php', '_list.php');
	$strObjectArray = array();
	$objDirectory = opendir(__DOCROOT__ . __FORM_DRAFTS__);
	while ($strFilename = readdir($objDirectory)) {
		if (($strFilename != '.') && ($strFilename != '..')) {
			$blnFound = false;
			// strip the suffix (if applicable)
			foreach ($strSuffixes as $strSuffix) {
				if ((!$blnFound) && 
					(substr($strFilename, strlen($strFilename) - strlen($strSuffix)) == $strSuffix)) {
					$strFilename = substr($strFilename, 0, strlen($strFilename) - strlen($strSuffix));
					$blnFound = true;
				}
			}

			if ($blnFound) {
				$strObjectArray[$strFilename] = true;
			}
		}
	}

	// Sort the list of objects
	ksort($strObjectArray);

	$strPageTitle = 'List of Form Drafts';
	require(__CONFIGURATION__ . '/header.inc.php');
?>
	<div id="draftList">
<?php
		foreach ($strObjectArray as $strObject=>$blnValue) {
			printf('<h1>%s</h1><p class="create"><a href="%s/%s_list.php">%s</a> &nbsp;|&nbsp; <a href="%s/%s_edit.php">%s</a></p>',
				$strObject, __VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__, $strObject, QApplication::Translate('View List'),
				__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__, $strObject, QApplication::Translate('Create New'));
		}
?>
		<hr />
		<h1>Panel Drafts &ldquo;AJAX Dashboard&rdquo;</h1>
		<p class="create"><a href="<?php _p(__VIRTUAL_DIRECTORY__ . __PANEL_DRAFTS__) ?>">Go to Dashboard</a></p>
	</div>

<?php require (__CONFIGURATION__ . '/footer.inc.php'); ?>