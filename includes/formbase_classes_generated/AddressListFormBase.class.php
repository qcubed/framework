<?php
	/**
	 * This is a quick-and-dirty draft QForm object to do the List All functionality
	 * of the Address class.  It uses the code-generated
	 * AddressDataGrid control which has meta-methods to help with
	 * easily creating/defining Address columns.
	 *
	 * Any display customizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 * 
	 * NOTE: This file is overwritten on any code regenerations.  If you want to make
	 * permanent changes, it is STRONGLY RECOMMENDED to move both address_list.php AND
	 * address_list.tpl.php out of this Form Drafts directory.
	 *
	 * @package My QCubed Application
	 * @subpackage FormBaseObjects
	 */
	abstract class AddressListFormBase extends QForm {
		// Local instance of the Meta DataGrid to list Addresses
		/**
		 * @var AddressDataGrid dtgAddresses
		 */
		protected $dtgAddresses;

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
			$this->dtgAddresses = new AddressDataGrid($this);

			// Style the DataGrid (if desired)
			$this->dtgAddresses->CssClass = 'datagrid';
			$this->dtgAddresses->AlternateRowStyle->CssClass = 'alternate';

			// Add Pagination (if desired)
			$this->dtgAddresses->Paginator = new QPaginator($this->dtgAddresses);
			$this->dtgAddresses->ItemsPerPage = __FORM_DRAFTS_FORM_LIST_ITEMS_PER_PAGE__;

			// Use the MetaDataGrid functionality to add Columns for this datagrid

			// Create an Edit Column
			$strEditPageUrl = __VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__ . '/address_edit.php';
			$this->dtgAddresses->MetaAddEditLinkColumn($strEditPageUrl, 'Edit', 'Edit');

			// Create the Other Columns (note that you can use strings for address's properties, or you
			// can traverse down QQN::address() to display fields that are down the hierarchy)
			$this->dtgAddresses->MetaAddColumn('Id');
			$this->dtgAddresses->MetaAddColumn(QQN::Address()->Person);
			$this->dtgAddresses->MetaAddColumn('Street');
			$this->dtgAddresses->MetaAddColumn('City');
		}
	}
?>
