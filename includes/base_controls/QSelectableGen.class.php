<?php	
	/**
	 * Triggered when the selectable is created.
	 * 
	 * 	* event Type: Event 
	 * 	* ui Type: Object 
	 * 
	 * _Note: The ui object is empty but included for consistency with other
	 * events._	 */
	class QSelectable_CreateEvent extends QJqUiEvent {
		const EventName = 'selectablecreate';
	}
	/**
	 * Triggered at the end of the select operation, on each element added to
	 * the selection.
	 * 
	 * 	* event Type: Event 
	 * 
	 * 	* ui Type: Object 
	 * 
	 * 	* selected Type: Element The selectable item that has been selected.
	 * 
	 */
	class QSelectable_SelectedEvent extends QJqUiEvent {
		const EventName = 'selectableselected';
	}
	/**
	 * Triggered during the select operation, on each element added to the
	 * selection.
	 * 
	 * 	* event Type: Event 
	 * 
	 * 	* ui Type: Object 
	 * 
	 * 	* selecting Type: Element The current selectable item being selected.
	 * 
	 */
	class QSelectable_SelectingEvent extends QJqUiEvent {
		const EventName = 'selectableselecting';
	}
	/**
	 * Triggered at the beginning of the select operation.
	 * 
	 * 	* event Type: Event 
	 * 	* ui Type: Object 
	 * 
	 * _Note: The ui object is empty but included for consistency with other
	 * events._	 */
	class QSelectable_StartEvent extends QJqUiEvent {
		const EventName = 'selectablestart';
	}
	/**
	 * Triggered at the end of the select operation.
	 * 
	 * 	* event Type: Event 
	 * 	* ui Type: Object 
	 * 
	 * _Note: The ui object is empty but included for consistency with other
	 * events._	 */
	class QSelectable_StopEvent extends QJqUiEvent {
		const EventName = 'selectablestop';
	}
	/**
	 * Triggered at the end of the select operation, on each element removed
	 * from the selection.
	 * 
	 * 	* event Type: Event 
	 * 
	 * 	* ui Type: Object 
	 * 
	 * 	* unselected Type: Element The selectable item that has been
	 * unselected.
	 * 
	 */
	class QSelectable_UnselectedEvent extends QJqUiEvent {
		const EventName = 'selectableunselected';
	}
	/**
	 * Triggered during the select operation, on each element removed from
	 * the selection.
	 * 
	 * 	* event Type: Event 
	 * 
	 * 	* ui Type: Object 
	 * 
	 * 	* unselecting Type: Element The current selectable item being
	 * unselected.
	 * 
	 */
	class QSelectable_UnselectingEvent extends QJqUiEvent {
		const EventName = 'selectableunselecting';
	}

	/* Custom "property" event classes for this control */

	/**
	 * Generated QSelectableGen class.
	 * 
	 * This is the QSelectableGen class which is automatically generated
	 * by scraping the JQuery UI documentation website. As such, it includes all the options
	 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
	 * the QSelectableBase class for any glue code to make this class more
	 * usable in QCubed.
	 * 
	 * @see QSelectableBase
	 * @package Controls\Base
	 * @property mixed $AppendTo 	 * Which element the selection helper (the lasso) should be appended to.
	 * @property boolean $AutoRefresh 	 * This determines whether to refresh (recalculate) the position and size
	 * of each selectee at the beginning of each select operation. If you
	 * have many items, you may want to set this to false and call the
	 * refresh() method manually.
	 * @property mixed $Cancel 	 * Prevents selecting if you start on elements matching the selector.
	 * @property integer $Delay 	 * Time in milliseconds to define when the selecting should start. This
	 * helps prevent unwanted selections when clicking on an element.
	 * @property boolean $Disabled 	 * Disables the selectable if set to true.
	 * @property integer $Distance 	 * Tolerance, in pixels, for when selecting should start. If specified,
	 * selecting will not start until the mouse has been dragged beyond the
	 * specified distance.
	 * @property mixed $Filter 	 * The matching child elements will be made selectees (able to be
	 * selected).
	 * @property string $Tolerance 	 * Specifies which mode to use for testing whether the lasso should
	 * select an item. Possible values: 
	 * 
	 * 	* "fit": Lasso overlaps the item entirely.
	 * 	* "touch": Lasso overlaps the item by any amount.
	 * 

	 */

	class QSelectableGen extends QPanel	{
		protected $strJavaScripts = __JQUERY_EFFECTS__;
		protected $strStyleSheets = __JQUERY_CSS__;
		/** @var mixed */
		protected $mixAppendTo = null;
		/** @var boolean */
		protected $blnAutoRefresh = null;
		/** @var mixed */
		protected $mixCancel = null;
		/** @var integer */
		protected $intDelay;
		/** @var boolean */
		protected $blnDisabled = null;
		/** @var integer */
		protected $intDistance;
		/** @var mixed */
		protected $mixFilter = null;
		/** @var string */
		protected $strTolerance = null;

		/**
		 * Builds the option array to be sent to the widget consctructor.
		 *
		 * @return array key=>value array of options
		 */
		protected function MakeJqOptions() {
			$jqOptions = null;
			if (!is_null($val = $this->AppendTo)) {$jqOptions['appendTo'] = $val;}
			if (!is_null($val = $this->AutoRefresh)) {$jqOptions['autoRefresh'] = $val;}
			if (!is_null($val = $this->Cancel)) {$jqOptions['cancel'] = $val;}
			if (!is_null($val = $this->Delay)) {$jqOptions['delay'] = $val;}
			if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
			if (!is_null($val = $this->Distance)) {$jqOptions['distance'] = $val;}
			if (!is_null($val = $this->Filter)) {$jqOptions['filter'] = $val;}
			if (!is_null($val = $this->Tolerance)) {$jqOptions['tolerance'] = $val;}
			return $jqOptions;
		}

		public function GetJqSetupFunction() {
			return 'selectable';
		}

		public function GetEndScript() {
			if ($this->getJqControlId() !== $this->ControlId) {
				// If events are not attached to the actual object being drawn, then the old events will not get
				// deleted. We delete the old events here. This code must happen before any other event processing code.
				QApplication::ExecuteControlCommand($this->getJqControlId(), "off", QJsPriority::High);
			}
			$jqOptions = $this->makeJqOptions();
			if (empty($jqOptions)) {
				QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction());
			}
			else {
				QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), $jqOptions);
			}

			return parent::GetEndScript();
		}

		/**
		 * Removes the selectable functionality completely. This will return the
		 * element back to its pre-init state.
		 * 
		 * 	* This method does not accept any arguments.		 */
		public function Destroy() {
			QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", QJsPriority::Low);
		}
		/**
		 * Disables the selectable.
		 * 
		 * 	* This method does not accept any arguments.		 */
		public function Disable() {
			QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", QJsPriority::Low);
		}
		/**
		 * Enables the selectable.
		 * 
		 * 	* This method does not accept any arguments.		 */
		public function Enable() {
			QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", QJsPriority::Low);
		}
		/**
		 * Retrieves the selectables instance object. If the element does not
		 * have an associated instance, undefined is returned. 
		 * 
		 * Unlike other widget methods, instance() is safe to call on any element
		 * after the selectable plugin has loaded.
		 * 
		 * 	* This method does not accept any arguments.		 */
		public function Instance() {
			QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", QJsPriority::Low);
		}
		/**
		 * Gets the value currently associated with the specified optionName. 
		 * 
		 * Note: For options that have objects as their value, you can get the
		 * value of a specific key by using dot notation. For example, "foo.bar"
		 * would get the value of the bar property on the foo option.
		 * 
		 * 	* optionName Type: String The name of the option to get.		 * @param $optionName		 */
		public function Option($optionName) {
			QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, QJsPriority::Low);
		}
		/**
		 * Gets an object containing key/value pairs representing the current
		 * selectable options hash.
		 * 
		 * 	* This signature does not accept any arguments.		 */
		public function Option1() {
			QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", QJsPriority::Low);
		}
		/**
		 * Sets the value of the selectable option associated with the specified
		 * optionName. 
		 * 
		 * Note: For options that have objects as their value, you can set the
		 * value of just one property by using dot notation for optionName. For
		 * example, "foo.bar" would update only the bar property of the foo
		 * option.
		 * 
		 * 	* optionName Type: String The name of the option to set.
		 * 	* value Type: Object A value to set for the option.		 * @param $optionName		 * @param $value		 */
		public function Option2($optionName, $value) {
			QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, QJsPriority::Low);
		}
		/**
		 * Sets one or more options for the selectable.
		 * 
		 * 	* options Type: Object A map of option-value pairs to set.		 * @param $options		 */
		public function Option3($options) {
			QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, QJsPriority::Low);
		}
		/**
		 * Refresh the position and size of each selectee element. This method
		 * can be used to manually recalculate the position and size of each
		 * selectee when the autoRefresh option is set to false.
		 * 
		 * 	* This method does not accept any arguments.		 */
		public function Refresh() {
			QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", QJsPriority::Low);
		}


		public function __get($strName) {
			switch ($strName) {
				case 'AppendTo': return $this->mixAppendTo;
				case 'AutoRefresh': return $this->blnAutoRefresh;
				case 'Cancel': return $this->mixCancel;
				case 'Delay': return $this->intDelay;
				case 'Disabled': return $this->blnDisabled;
				case 'Distance': return $this->intDistance;
				case 'Filter': return $this->mixFilter;
				case 'Tolerance': return $this->strTolerance;
				default: 
					try { 
						return parent::__get($strName); 
					} catch (QCallerException $objExc) { 
						$objExc->IncrementOffset(); 
						throw $objExc; 
					}
			}
		}

		public function __set($strName, $mixValue) {
			switch ($strName) {
				case 'AppendTo':
					$this->mixAppendTo = $mixValue;
					$this->AddAttributeScript($this->getJqSetupFunction(), 'option', 'appendTo', $mixValue);
					break;

				case 'AutoRefresh':
					try {
						$this->blnAutoRefresh = QType::Cast($mixValue, QType::Boolean);
						$this->AddAttributeScript($this->getJqSetupFunction(), 'option', 'autoRefresh', $this->blnAutoRefresh);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Cancel':
					$this->mixCancel = $mixValue;
					$this->AddAttributeScript($this->getJqSetupFunction(), 'option', 'cancel', $mixValue);
					break;

				case 'Delay':
					try {
						$this->intDelay = QType::Cast($mixValue, QType::Integer);
						$this->AddAttributeScript($this->getJqSetupFunction(), 'option', 'delay', $this->intDelay);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Disabled':
					try {
						$this->blnDisabled = QType::Cast($mixValue, QType::Boolean);
						$this->AddAttributeScript($this->getJqSetupFunction(), 'option', 'disabled', $this->blnDisabled);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Distance':
					try {
						$this->intDistance = QType::Cast($mixValue, QType::Integer);
						$this->AddAttributeScript($this->getJqSetupFunction(), 'option', 'distance', $this->intDistance);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Filter':
					$this->mixFilter = $mixValue;
					$this->AddAttributeScript($this->getJqSetupFunction(), 'option', 'filter', $mixValue);
					break;

				case 'Tolerance':
					try {
						$this->strTolerance = QType::Cast($mixValue, QType::String);
						$this->AddAttributeScript($this->getJqSetupFunction(), 'option', 'tolerance', $this->strTolerance);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				case 'Enabled':
					$this->Disabled = !$mixValue;	// Tie in standard QCubed functionality
					parent::__set($strName, $mixValue);
					break;
					
				default:
					try {
						parent::__set($strName, $mixValue);
						break;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}
	}

?>
