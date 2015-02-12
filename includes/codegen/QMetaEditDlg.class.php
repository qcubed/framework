<?php

require(__QCUBED__ . '/codegen/QCodeGen.class.php');

/**
 * Class QMetaEditDlg
 *
 * A dialog that lets you specify code generation options for a control. These options control how a control
 * is generated, and includes additional parameters that can be specified for a control.
 *
 * This dialog pops up when designer mode is turned on and the user right clicks on a control.
 *
 * The code below will set up the dialog and display options that are generic to all QControls. Individual
 * controls can add parameters to this dialog by implementing the GetMetaParams function.
 *
 * Everything gets saved in the configuration/codegen_options.json file.
 *
 *
 */
class QMetaEditDlg extends QDialog {

	protected $objCurrentControl;
	protected $tabs;

	protected $txtName;
	protected $txtControlId;
	protected $txtControlClass;
	protected $lstFormGen;

	protected $params;
	protected $objMetacontrolOptions;

	protected $generalOptions;
	protected $dtgGeneralOptions;

	protected $categories;
	protected $datagrids;

	public function __construct($objParentObject, $strControlId) {
		parent::__construct ($objParentObject, $strControlId);

		$this->AutoRenderChildren = true;
		$this->Width = 700;

		$this->objMetacontrolOptions = new QMetacontrolOptions();

		$this->tabs = new QTabs ($this);
		$this->tabs->HeightStyle = "auto";

		$this->AddButton ('Save', 'save');
		$this->AddButton ('Save, Regenerate and Reload', 'saveRefresh');
		$this->AddButton ('Cancel', 'cancel');

		$this->AddAction(new QDialog_ButtonEvent(), new QAjaxControlAction($this, 'ButtonClick'));
	}

	/**
	 * Recreate the tabs in the dialog
	 */
	protected function SetupTabs() {
		$strClassNames = $this->CreateClassNameArray();
		$this->tabs->RemoveChildControls(true);
		$this->categories = array();

		$this->dtgGeneralOptions = new QSimpleTable($this->tabs, 'definitionTab');
		$this->dtgGeneralOptions->ShowHeader = false;
		$this->dtgGeneralOptions->Name = "Definition Options";
		$this->dtgGeneralOptions->CreatePropertyColumn('Attribute', 'Name');
		$col = $this->dtgGeneralOptions->AddColumn (new QSimpleTableCallableColumn('Attribute', array ($this, 'dtg_ValueRender'), $this->dtgGeneralOptions));
		$col->HtmlEntities = false;
		$this->dtgGeneralOptions->SetDataBinder('dtgGeneralOptions_Bind', $this);

		$this->generalOptions = array (
			new QMetaParam ('General', 'FormGen',
				'Whether or not to generate this object, just a label for the object, just the control, or both the control and label',
				QMetaParam::SelectionList,
				array (QFormGen::Both=>'Both', QFormGen::None=>'None', QFormGen::ControlOnly=>'Control', QFormGen::LabelOnly=>'Label')),
			new QMetaParam ('General', 'Name', 'Control\'s Name', QType::String),
			new QMetaParam ('General', 'ControlClass', 'Override of the PHP type for the control. If you change this, save the dialog and reopen to reload the tabs to show the control specific options.', QMetaParam::SelectionList, $strClassNames),
			new QMetaParam ('General', 'NoAutoLoad', 'Prevent automatically populating a list type control. Set this if you are doing more complex list loading.', QType::Boolean)
		);

		// load values from settings file
		foreach ($this->generalOptions as $objParam) {
			$objControl = $objParam->GetControl ($this->dtgGeneralOptions);
			$strName = $objControl->Name;

			if (isset($this->params[$strName])) {
				$objControl->Value = $this->params[$strName];
				if ($strName == 'ControlClass') {
					$strControlClass = $this->params[$strName];
				}
			} else {
				$objControl->Value = null;
			}
		}

		if (!isset ($strControlClass)) {
			$strControlClass = get_class ($this->objCurrentControl);
		}
		$metaParams = $strControlClass::GetMetaParams();

		// gather categories
		foreach ($metaParams as $metaParam) {
			$this->categories[$metaParam->Category][] = $metaParam;
		}

		foreach ($this->categories as $tabName=>$metaParams) {
			$panel = new QPanel ($this->tabs);
			$panel->SetCustomStyle('overflow-y', 'scroll');
			$panel->SetCustomStyle('max-height', '200');
			$panel->AutoRenderChildren = true;
			$panel->Name = $tabName;

			$dtg = new QSimpleTable($panel);
			$dtg->ShowHeader = false;
			$dtg->CreatePropertyColumn('Attribute', 'Name');
			$col = $dtg->AddColumn (new QSimpleTableCallableColumn('Attribute', array ($this, 'dtg_ValueRender'), $dtg));
			$col->HtmlEntities = false;
			$dtg->SetDataBinder('dtgControlBind', $this);
			$dtg->Name = $tabName; // holder for category
			$this->datagrids[$tabName] = $dtg;

			// load values from settings file
			foreach ($metaParams as $objParam) {
				$objControl = $objParam->GetControl ($this->datagrids[$tabName]);
				if ($objControl) {
					$strName = $objControl->Name;

					if (isset($this->params['Overrides'][$strName])) {
						$objControl->Value = $this->params['Overrides'][$strName];
					} else {
						$objControl->Value = null;
					}
				}
			}

		}
	}

	/**
	 * Bind the general options
	 */
	public function dtgGeneralOptions_Bind() {
		$this->dtgGeneralOptions->DataSource = $this->generalOptions;
	}

	/**
	 * Binder for the control specific options
	 */
	public function dtgControlBind($dtg) {
		$dtg->DataSource = $this->categories[$dtg->Name];
	}

	/**
	 * Render the value column, which allows the user to specify the value of an option for the control.
	 *
	 * @param QMetaParam $objControlParam
	 * @param QControl $objParent
	 * @return string
	 */
	public function dtg_ValueRender (QMetaParam $objControlParam, QControl $objParent) {
		$objControl = $objControlParam->GetControl ($objParent);
		return $objControl->Render(false);
	}

	/**
	 * Entry point for the dialog. Brings up the dialog and loads all the options so that it can be edited.
	 *
	 * @param QControl $objControl
	 */
	public function EditControl (QControl $objControl) {
		$this->objCurrentControl = $objControl;

		$this->Title = $objControl->Name . ' Edit';

		$this->ReadParams();
		$this->SetupTabs();
		$this->Open();
		$this->tabs->Refresh();
	}

	/**
	 * Dialog button has been clicked. Save the options, or Save, codegen, and then reload.
	 *
	 * @param $strFormId
	 * @param $strControlId
	 * @param $mixParam
	 */
	public function ButtonClick ($strFormId, $strControlId, $mixParam) {
		if ($mixParam == 'save') {
			$this->UpdateControlInfo();
			$this->WriteParams();
		} elseif ($mixParam == 'saveRefresh') {
			$this->UpdateControlInfo();
			$this->WriteParams();
			QCodeGen::Run(__CONFIGURATION__ . '/codegen_settings.xml');
			foreach (QCodeGen::$CodeGenArray as $objCodeGen) {
				$objCodeGen->GenerateAll(); // silently codegen
			}
			QApplication::Redirect($_SERVER['PHP_SELF']);
		}

		$this->Close();
	}

	/**
	 * Puts the values of the dialog into the params array to be saved off into the settings file.
	 */
	protected function UpdateControlInfo() {
		$objParams = $this->generalOptions;
		foreach ($objParams as $objParam) {
			$objControl = $objParam->GetControl ($this->dtgGeneralOptions);
			$strName = $objControl->Name;
			$value = $objControl->Value;

			if (!is_null($value)) {
				$this->params[$strName] = $value;
			} else {
				unset ($this->params[$strName]);
			}
		}

		foreach ($this->categories as $objParams) {
			foreach ($objParams as $objParam) {
				$objControl = $objParam->GetControl ();
				if ($objControl) {
					$strName = $objControl->Name;
					$value = $objControl->Value;

					if (!is_null($value)) {
						$this->params['Overrides'][$strName] = $value;
					} else {
						unset ($this->params['Overrides'][$strName]);
					}
				} else {
					unset ($this->params['Overrides'][$strName]);
				}
			}
		}

		if (empty($this->params['Overrides'])) {
			unset ($this->params['Overrides']);
		}
	}

	/**
	 * Write the current params into the settings file.
	 */
	protected function WriteParams() {
		$node = $this->objCurrentControl->LinkedNode;
		$strClassName = $node->_ParentNode->_ClassName;
		$this->objMetacontrolOptions->SetOptions ($strClassName, $node->_PropertyName, $this->params);
		$this->objMetacontrolOptions->Save();
	}

	/**
	 * Read the params from the settings file.
	 */
	protected function ReadParams() {
		$node = $this->objCurrentControl->LinkedNode;
		if ($node) {
			$strClassName = $node->_ParentNode->_ClassName;
			$this->params = $this->objMetacontrolOptions->GetOptions ($strClassName, $node->_PropertyName);
		}
	}

	/**
	 * Returns an array of class names that can be used to edit the current control's data type.
	 *
	 * @return array
	 */
	protected function CreateClassNameArray() {
		// create the control array
		$controls = array();
		include (__QCUBED_CORE__ . '/control_registry.inc.php');

		if (file_exists(__APP_INCLUDES__ . '/control_registry.inc.php')) {
			include (__APP_INCLUDES__ . '/control_registry.inc.php');
		}

		if (defined ('__PLUGINS__') &&
				is_dir(__PLUGINS__)) {
			$plugins = scandir(__PLUGINS__);
			foreach ($plugins as $dirName) {
				if ($dirName != '.' && $dirName != '..') {
					if (file_exists(__PLUGINS__ . '/' . $dirName . '/control_registry.inc.php')) {
						include (__PLUGINS__ . '/' . $dirName . '/control_registry.inc.php');
					}
				}
			}
		}

		// $controls is now an array indexed by QType, with each entry a QControl type name

		// Figure out what type of control we are looking for
		$node = $this->objCurrentControl->LinkedNode;
		$type = $node->_Type;
		if ($node->_Type == QType::ReverseReference) {
			$type = QType::ArrayType;
		}
		elseif ($node->_TableName) { // indicates a reference to another table
			$type = QType::ArrayType;
		}

		if (isset ($controls[$type])) {
			foreach ($controls[$type] as $strClassName) {
				$a[$strClassName] = $strClassName;	// remove duplicates
			}

			return $a;
		} else {
			return null;
		}

	}

}

