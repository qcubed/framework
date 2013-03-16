<?php
	/**
	 * This is a quick-and-dirty draft QPanel object to do Create, Edit, and Delete functionality
	 * of the Project class.  It uses the code-generated
	 * ProjectMetaControl class, which has meta-methods to help with
	 * easily creating/defining controls to modify the fields of a Project columns.
	 *
	 * Any display customizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 *
	 * NOTE: This file is overwritten on any code regenerations.  If you want to make
	 * permanent changes, it is STRONGLY RECOMMENDED to move both project_edit.php AND
	 * project_edit.tpl.php out of this Form Drafts directory.
	 *
	 * @package My QCubed Application
	 * @subpackage Drafts
	 */
	class ProjectEditPanel extends QPanel {
		// Local instance of the ProjectMetaControl
		/**
		 * @var ProjectMetaControl
		 */
		protected $mctProject;

		// Controls for Project's Data Fields
		/** @var QLabel  */
		public $lblId;
		/** @var QListBox  */
		public $lstProjectStatusType;
		/** @var QListBox  */
		public $lstManagerPerson;
		/** @var QTextBox  */
		public $txtName;
		/** @var QTextBox  */
		public $txtDescription;
		/** @var QDateTime  */
		public $calStartDate;
		/** @var QDateTime  */
		public $calEndDate;
		/** @var QFloatTextBox  */
		public $txtBudget;
		/** @var QFloatTextBox  */
		public $txtSpent;

		// Other ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References
		public $dtgProjectsAsRelated;
		public $dtgParentProjectsAsRelated;
		public $dtgPeopleAsTeamMember;

		// Other Controls
		/**
		 * @var QButton Save
		 */
		public $btnSave;
		/**
		 * @var QButton Delete
		 */
		public $btnDelete;
		/**
		 * @var QButton Cancel
		 */
		public $btnCancel;

		// Callback
		protected $strClosePanelMethod;

		public function __construct($objParentObject, $strClosePanelMethod, $intId = null, $strControlId = null) {
			// Call the Parent
			try {
				parent::__construct($objParentObject, $strControlId);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Setup Callback and Template
			$this->strTemplate = __DOCROOT__ . __PANEL_DRAFTS__ . '/ProjectEditPanel.tpl.php';
			$this->strClosePanelMethod = $strClosePanelMethod;

			// Construct the ProjectMetaControl
			// MAKE SURE we specify "$this" as the MetaControl's (and thus all subsequent controls') parent
			$this->mctProject = ProjectMetaControl::Create($this, $intId);

			// Call MetaControl's methods to create qcontrols based on Project's data fields
			$this->lblId = $this->mctProject->lblId_Create();
			$this->lstProjectStatusType = $this->mctProject->lstProjectStatusType_Create();
			$this->lstManagerPerson = $this->mctProject->lstManagerPerson_Create();
			$this->txtName = $this->mctProject->txtName_Create();
			$this->txtDescription = $this->mctProject->txtDescription_Create();
			$this->calStartDate = $this->mctProject->calStartDate_Create();
			$this->calEndDate = $this->mctProject->calEndDate_Create();
			$this->txtBudget = $this->mctProject->txtBudget_Create();
			$this->txtSpent = $this->mctProject->txtSpent_Create();
			$this->dtgProjectsAsRelated = $this->mctProject->dtgProjectsAsRelated_Create();
			$this->dtgParentProjectsAsRelated = $this->mctProject->dtgParentProjectsAsRelated_Create();
			$this->dtgPeopleAsTeamMember = $this->mctProject->dtgPeopleAsTeamMember_Create();

			// Create Buttons and Actions on this Form
			$this->btnSave = new QButton($this);
			$this->btnSave->Text = QApplication::Translate('Save');
			$this->btnSave->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnSave_Click'));
			$this->btnSave->CausesValidation = $this;

			$this->btnCancel = new QButton($this);
			$this->btnCancel->Text = QApplication::Translate('Cancel');
			$this->btnCancel->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnCancel_Click'));

			$this->btnDelete = new QButton($this);
			$this->btnDelete->Text = QApplication::Translate('Delete');
			$this->btnDelete->AddAction(new QClickEvent(), new QConfirmAction(sprintf(QApplication::Translate('Are you SURE you want to DELETE this %s?'),  QApplication::Translate('Project'))));
			$this->btnDelete->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnDelete_Click'));
			$this->btnDelete->Visible = $this->mctProject->EditMode;
		}

		// Control AjaxAction Event Handlers
		public function btnSave_Click($strFormId, $strControlId, $strParameter) {
			// Delegate "Save" processing to the ProjectMetaControl
			$this->mctProject->SaveProject();
			$this->CloseSelf(true);
		}

		public function btnDelete_Click($strFormId, $strControlId, $strParameter) {
			// Delegate "Delete" processing to the ProjectMetaControl
			$this->mctProject->DeleteProject();
			$this->CloseSelf(true);
		}

		public function btnCancel_Click($strFormId, $strControlId, $strParameter) {
			$this->CloseSelf(false);
		}

		// Close Myself and Call ClosePanelMethod Callback
		protected function CloseSelf($blnChangesMade) {
			$strMethod = $this->strClosePanelMethod;
			$this->objForm->$strMethod($blnChangesMade);
		}

		
	}
?>