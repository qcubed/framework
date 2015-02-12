<?php
	/** @var QTable $objTable */
	/** @var QDatabaseCodeGen $objCodeGen */
	global $_TEMPLATE_SETTINGS;
	$_TEMPLATE_SETTINGS = array(
		'OverwriteFlag' => true,
		'DocrootFlag' => false,
		'DirectorySuffix' => '',
		'TargetDirectory' => __FORMBASE_CLASSES__,
		'TargetFileName' => $objTable->ClassName . 'ListFormBase.class.php'
	);
?>
<?php print("<?php\n"); ?>
	/**
	 * This is a quick-and-dirty draft QForm object to do the List All functionality
	 * of the <?= $objTable->ClassName ?> class.  It uses the code-generated
	 * <?= $objTable->ClassName ?>DataGrid control which has meta-methods to help with
	 * easily creating/defining <?= $objTable->ClassName ?> columns.
	 *
	 * Any display customizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 * 
	 * NOTE: This file is overwritten on any code regenerations.  If you want to make
	 * permanent changes, it is STRONGLY RECOMMENDED to move both <?= QConvertNotation::UnderscoreFromCamelCase($objTable->ClassName) ?>_list.php AND
	 * <?= QConvertNotation::UnderscoreFromCamelCase($objTable->ClassName) ?>_list.tpl.php out of this Form Drafts directory.
	 *
	 * @package <?= QCodeGen::$ApplicationName; ?>

	 * @subpackage FormBaseObjects
	 */
	abstract class <?= $objTable->ClassName ?>ListFormBase extends QForm {
		// Local instance of the Meta DataGrid to list <?= $objTable->ClassNamePlural ?>

		/**
		 * @var <?= $objTable->ClassName ?>DataGrid dtg<?= $objTable->ClassNamePlural ?>

		 */
		protected $dtg<?= $objTable->ClassNamePlural ?>;

		// Create QForm Event Handlers as Needed

//		protected function Form_Exit() {}
//		protected function Form_Load() {}
//		protected function Form_PreRender() {}
//		protected function Form_Validate() {}

		protected function Form_Run() {
			parent::Form_Run();
		}

		protected function Form_Create() {
			parent::Form_Create();

			// Instantiate the Meta DataGrid
			$this->dtg<?= $objTable->ClassNamePlural ?> = new <?= $objTable->ClassName ?>DataGrid($this);

			// Style the DataGrid (if desired)
			$this->dtg<?= $objTable->ClassNamePlural ?>->CssClass = 'datagrid';
			$this->dtg<?= $objTable->ClassNamePlural ?>->AlternateRowStyle->CssClass = 'alternate';

			// Add Pagination (if desired)
			$this->dtg<?= $objTable->ClassNamePlural ?>->Paginator = new QPaginator($this->dtg<?= $objTable->ClassNamePlural ?>);
			$this->dtg<?= $objTable->ClassNamePlural ?>->ItemsPerPage = __FORM_DRAFTS_FORM_LIST_ITEMS_PER_PAGE__;

			// Use the MetaDataGrid functionality to add Columns for this datagrid

			// Create an Edit Column
			$strEditPageUrl = __VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__ . '/<?= QConvertNotation::UnderscoreFromCamelCase($objTable->ClassName) ?>_edit.php';
			$this->dtg<?= $objTable->ClassNamePlural ?>->MetaAddEditLinkColumn($strEditPageUrl, 'Edit', 'Edit');

			// Create the Other Columns (note that you can use strings for <?= $objTable->Name ?>'s properties, or you
			// can traverse down QQN::<?= $objTable->Name ?>() to display fields that are down the hierarchy)
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
<?php	if (isset($objColumn->Options['FormGen']) && ($objColumn->Options['FormGen'] == QFormGen::None || $objColumn->Options['FormGen'] == QFormGen::ControlOnly)) continue; ?>
<?php if (!$objColumn->Reference) { ?>
			$this->dtg<?= $objTable->ClassNamePlural ?>->MetaAddColumn('<?= $objColumn->PropertyName ?>');
<?php } ?>
<?php if ($objColumn->Reference && $objColumn->Reference->IsType) { ?>
			$this->dtg<?= $objTable->ClassNamePlural ?>->MetaAddTypeColumn('<?= $objColumn->PropertyName ?>', '<?= $objColumn->Reference->VariableType ?>');
<?php } ?>
<?php if ($objColumn->Reference && !$objColumn->Reference->IsType) { ?>
			$this->dtg<?= $objTable->ClassNamePlural ?>->MetaAddColumn(QQN::<?= $objTable->ClassName ?>()-><?= $objColumn->Reference->PropertyName ?>);
<?php } ?>
<?php } ?><?php foreach ($objTable->ReverseReferenceArray as $objReverseReference) { ?><?php if ($objReverseReference->Unique) { ?>
			$this->dtg<?= $objTable->ClassNamePlural ?>->MetaAddColumn(QQN::<?= $objTable->ClassName; ?>()-><?= $objReverseReference->ObjectDescription ?>);
<?php } ?><?php } ?>
		}
	}
?>
