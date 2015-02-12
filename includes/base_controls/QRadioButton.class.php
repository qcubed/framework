<?php
	/**
	 * This file contains the QRadioButton class.
	 *
	 * @package Controls
	 */

	/**
	 * This class will render an HTML Radio button.
	 *
	 * Based on a QCheckbox, which is very similar to a checkbox.
	 *
	 * @package Controls
	 *
	 * @property string $Text is used to display text that is displayed next to the radio. The text is rendered as an html "Label For" the radio
	 * @property string $TextAlign specifies if "Text" should be displayed to the left or to the right of the radio.
	 * @property string $GroupName assigns the radio button into a radio button group (optional) so that no more than one radio in that group may be selected at a time.
	 * @property boolean $HtmlEntities
	 * @property boolean $Checked specifices whether or not the radio is selected
	 */
	class QRadioButton extends QCheckBox {
		/**
		 * Group to which this radio button belongs
		 * Groups determine the 'radio' behavior wherein you can select only one option out of all buttons in that group
		 * @var null|string Name of the group
		 */
		protected $strGroupName = null;

		/**
		 * Parse the data posted
		 */
		public function ParsePostData() {
			if (QApplication::$RequestMode == QRequestMode::Ajax) {
				$this->blnChecked = QType::Cast ($_POST[$this->strControlId], QType::Boolean);
			}
			elseif ($this->objForm->IsCheckableControlRendered($this->strControlId)) {
				if ($this->strGroupName)
					$strName = $this->strGroupName;
				else
					$strName = $this->strControlId;

				if (array_key_exists($strName, $_POST)) {
					if ($_POST[$strName] == $this->strControlId)
						$this->blnChecked = true;
					else
						$this->blnChecked = false;
				} else {
					$this->blnChecked = false;
				}
			}
		}

		/**
		 * Returns the HTML code for the control which can be sent to the client.
		 *
		 * Note, previous version wrapped this in a div and made the control a block level control unnecessarily. To
		 * achieve a block control, set blnUseWrapper and blnIsBlockElement.
		 *
		 * @return string THe HTML for the control
		 */
		protected function GetControlHtml() {
			if ($this->strGroupName)
				$strGroupName = $this->strGroupName;
			else
				$strGroupName = $this->strControlId;

			$attrOverride = array('type'=>'radio', 'name'=>$strGroupName, 'value'=>$this->strControlId);
			return $this->RenderButton($attrOverride);
		}

		/////////////////////////
		// Public Properties: GET
		/////////////////////////
		/**
		 * PHP __get magic method implementation for the QRadioButton class
		 * @param string $strName Name of the property
		 *
		 * @return array|bool|int|mixed|null|QControl|QForm|string
		 * @throws Exception|QCallerException
		 */
		public function __get($strName) {
			switch ($strName) {
				// APPEARANCE
				case "GroupName": return $this->strGroupName;

				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		/////////////////////////
		// Public Properties: SET
		/////////////////////////
		/**
		 * PHP __set magic method implementation
		 *
		 * @param string $strName  Name of the property
		 * @param string $mixValue Value of the property
		 *
		 * @return mixed
		 * @throws Exception|QCallerException|QInvalidCastException
		 */
		public function __set($strName, $mixValue) {
			switch ($strName) {
				case "GroupName":
					try {
						$strGroupName = QType::Cast($mixValue, QType::String);
						if ($this->strGroupName != $strGroupName) {
							$this->strGroupName = $strGroupName;
							$this->blnModified = true;
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				default:
					try {
						parent::__set($strName, $mixValue);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					break;
			}
		}
	}
?>