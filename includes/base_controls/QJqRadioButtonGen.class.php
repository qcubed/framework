<?php
	/**
	 * QJqRadioButtonGen File
	 * 
	 * The abstract QJqRadioButtonGen class defined here is
	 * code-generated and contains options, events and methods scraped from the
	 * JQuery UI documentation Web site. It is not generated by the typical
	 * codegen process, but rather is generated periodically by the core QCubed
	 * team and checked in. However, the code to generate this file is
	 * in the assets/_core/php/_devetools/jquery_ui_gen/jq_control_gen.php file
	 * and you can regenerate the files if you need to.
	 *
	 * The comments in this file are taken from the JQuery UI site, so they do
	 * not always make sense with regard to QCubed. They are simply provided
	 * as reference. Note that this is very low-level code, and does not always
	 * update QCubed state variables. See the QJqRadioButtonBase 
	 * file, which contains code to interface between this generated file and QCubed.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the QJqRadioButton class file.
	 *
	 */

	/* Custom event classes for this control */
	
	
	/**
	 * Triggered when the button is created.<ul><li><strong>event</strong> Type:
	 * 		<a>Event</a> </li> <li><strong>ui</strong> Type: <a>Object</a>
	 * 		</li></ul><p><em>Note: The <code>ui</code> object is empty but included for
	 * 		consistency with other events.</em></p>
	 */
	class QJqRadioButton_CreateEvent extends QJqUiEvent {
		const EventName = 'buttoncreate';
	}

	/* Custom "property" event classes for this control */

	/**
	 * Generated QJqRadioButtonGen class.
	 * 
	 * This is the QJqRadioButtonGen class which is automatically generated
	 * by scraping the JQuery UI documentation website. As such, it includes all the options
	 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
	 * the QJqRadioButtonBase class for any glue code to make this class more
	 * usable in QCubed.
	 * 
	 * @see QJqRadioButtonBase
	 * @package Controls\Base
	 * @property boolean $Disabled Disables the button if set to <code>true</code>.
	 * @property mixed $Icons <p>Icons to display, with or without text (see <a><code>text</code></a>
	 * 		option). By default, the primary icon is displayed on the left of the label
	 * 		text and the secondary is displayed on the right. The positioning can be
	 * 		controlled via CSS.</p>  				<p>The value for the <code>primary</code> and
	 * 		<code>secondary</code> properties must match <a>an icon class name</a>,
	 * 		e.g., <code>&quot;ui-icon-gear&quot;</code>. For using only one icon:
	 * 		<code>icons: { primary: &quot;ui-icon-locked&quot; }</code>. For using two
	 * 		icons: <code>icons: { primary: &quot;ui-icon-gear&quot;, secondary:
	 * 		&quot;ui-icon-triangle-1-s&quot; }</code>.</p>
	 * @property string $Label Text to show in the button. When not specified (<code>null</code>), the
	 * 		element&apos;s HTML content is used, or its <code>value</code> attribute if
	 * 		the element is an input element of type submit or reset, or the HTML
	 * 		content of the associated label element if the element is an input of type
	 * 		radio or checkbox.
	 * @property boolean $JqText Whether to show the label. When set to <code>false</code> no text will be
	 * 		displayed, but the <a><code>icons</code></a> option must be enabled,
	 * 		otherwise the <code>text</code> option will be ignored.
	 */

	class QJqRadioButtonGen extends QRadioButton	{
		protected $strJavaScripts = __JQUERY_EFFECTS__;
		protected $strStyleSheets = __JQUERY_CSS__;
		/** @var boolean */
		protected $blnDisabled = null;
		/** @var mixed */
		protected $mixIcons = null;
		/** @var string */
		protected $strLabel = null;
		/** @var boolean */
		protected $blnJqText = null;
		
		protected function makeJsProperty($strProp, $strKey) {
			$objValue = $this->$strProp;
			if (null === $objValue) {
				return '';
			}

			return $strKey . ': ' . JavaScriptHelper::toJsObject($objValue) . ', ';
		}

		protected function makeJqOptions() {
			$strJqOptions = '';
			$strJqOptions .= $this->makeJsProperty('Disabled', 'disabled');
			$strJqOptions .= $this->makeJsProperty('Icons', 'icons');
			$strJqOptions .= $this->makeJsProperty('Label', 'label');
			$strJqOptions .= $this->makeJsProperty('JqText', 'text');
			if ($strJqOptions) $strJqOptions = substr($strJqOptions, 0, -2);
			return $strJqOptions;
		}

		public function getJqSetupFunction() {
			return 'button';
		}

		public function GetControlJavaScript() {
			return sprintf('jQuery("#%s").%s({%s})', $this->getJqControlId(), $this->getJqSetupFunction(), $this->makeJqOptions());
		}

		public function GetEndScript() {
			$str = '';
			if ($this->getJqControlId() !== $this->ControlId) {
				// #845: if the element receiving the jQuery UI events is different than this control
				// we need to clean-up the previously attached event handlers, so that they are not duplicated 
				// during the next ajax update which replaces this control.
				$str = sprintf('jQuery("#%s").off(); ', $this->getJqControlId());
			}
			$str .= $this->GetControlJavaScript();
			if ($strParentScript = parent::GetEndScript()) {
				$str .= '; ' . $strParentScript;
			}
			return $str;
		}
		
		/**
		 * Call a JQuery UI Method on the object. 
		 * 
		 * A helper function to call a jQuery UI Method. Takes variable number of arguments.
		 *
		 * @param boolean $blnAttribute true if the method is modifying an option, false if executing a command
		 * @param string $strMethodName the method name to call
		 * @internal param $mixed [optional] $mixParam1
		 * @internal param $mixed [optional] $mixParam2
		 */
		protected function CallJqUiMethod($blnAttribute, $strMethodName /*, ... */) {
			$args = func_get_args();
			array_shift ($args);

			$strArgs = JavaScriptHelper::toJsObject($args);
			$strJs = sprintf('jQuery("#%s").%s(%s)',
				$this->getJqControlId(),
				$this->getJqSetupFunction(),
				substr($strArgs, 1, strlen($strArgs)-2));	// params without brackets
			if ($blnAttribute) {
				$this->AddAttributeScript($strJs);
			} else {
				QApplication::ExecuteJavaScript($strJs);
			}
		}


		/**
		 * Removes the button functionality completely. This will return the element
		 * back to its pre-init state.<ul><li>This method does not accept any
		 * arguments.</li></ul>
		 */
		public function Destroy() {
			$this->CallJqUiMethod(false, "destroy");
		}
		/**
		 * Disables the button.<ul><li>This method does not accept any
		 * arguments.</li></ul>
		 */
		public function Disable() {
			$this->CallJqUiMethod(false, "disable");
		}
		/**
		 * Enables the button.<ul><li>This method does not accept any
		 * arguments.</li></ul>
		 */
		public function Enable() {
			$this->CallJqUiMethod(false, "enable");
		}
		/**
		 * <p>Retrieves the button&apos;s instance object. If the element does not
		 * have an associated instance, <code>undefined</code> is returned.</p> 
		 * 		<p>Unlike other widget methods, <code>instance()</code> is safe to call
		 * on any element after the button plugin has loaded.</p><ul><li>This method
		 * does not accept any arguments.</li></ul>
		 */
		public function Instance() {
			$this->CallJqUiMethod(false, "instance");
		}
		/**
		 * <p>Gets the value currently associated with the specified
		 * <code>optionName</code>.</p> 			<p><strong>Note:</strong> For options that
		 * have objects as their value, you can get the value of a specific key by
		 * using dot notation. For example, <code>&quot;foo.bar&quot;</code> would get
		 * the value of the <code>bar</code> property on the <code>foo</code>
		 * option.</p><ul><li><strong>optionName</strong> Type: <a>String</a> The name
		 * of the option to get.</li></ul>
		 * @param $optionName
		 */
		public function Option($optionName) {
			$this->CallJqUiMethod(false, "option", $optionName);
		}
		/**
		 * Gets an object containing key/value pairs representing the current button
		 * options hash.<ul><li>This signature does not accept any
		 * arguments.</li></ul>
		 */
		public function Option1() {
			$this->CallJqUiMethod(false, "option");
		}
		/**
		 * <p>Sets the value of the button option associated with the specified
		 * <code>optionName</code>.</p> 			<p><strong>Note:</strong> For options that
		 * have objects as their value, you can set the value of just one property by
		 * using dot notation for <code>optionName</code>. For example,
		 * <code>&quot;foo.bar&quot;</code> would update only the <code>bar</code>
		 * property of the <code>foo</code>
		 * option.</p><ul><li><strong>optionName</strong> Type: <a>String</a> The name
		 * of the option to set.</li> <li><strong>value</strong> Type: <a>Object</a> A
		 * value to set for the option.</li></ul>
		 * @param $optionName
		 * @param $value
		 */
		public function Option2($optionName, $value) {
			$this->CallJqUiMethod(false, "option", $optionName, $value);
		}
		/**
		 * Sets one or more options for the button.<ul><li><strong>options</strong>
		 * Type: <a>Object</a> A map of option-value pairs to set.</li></ul>
		 * @param $options
		 */
		public function Option3($options) {
			$this->CallJqUiMethod(false, "option", $options);
		}
		/**
		 * Refreshes the visual state of the button. Useful for updating button state
		 * after the native element&apos;s checked or disabled state is changed
		 * programmatically.<ul><li>This method does not accept any
		 * arguments.</li></ul>
		 */
		public function Refresh() {
			$this->CallJqUiMethod(false, "refresh");
		}


		public function __get($strName) {
			switch ($strName) {
				case 'Disabled': return $this->blnDisabled;
				case 'Icons': return $this->mixIcons;
				case 'Label': return $this->strLabel;
				case 'JqText': return $this->blnJqText;
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
				case 'Disabled':
					try {
						$this->blnDisabled = QType::Cast($mixValue, QType::Boolean);
						if ($this->OnPage) {
							$this->CallJqUiMethod(true, 'option', 'disabled', $this->blnDisabled);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Icons':
					$this->mixIcons = $mixValue;
				
					if ($this->OnPage) {
						$this->CallJqUiMethod(true, 'option', 'icons', $mixValue);
					}
					break;

				case 'Label':
					try {
						$this->strLabel = QType::Cast($mixValue, QType::String);
						if ($this->OnPage) {
							$this->CallJqUiMethod(true, 'option', 'label', $this->strLabel);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'JqText':
					try {
						$this->blnJqText = QType::Cast($mixValue, QType::Boolean);
						if ($this->OnPage) {
							$this->CallJqUiMethod(true, 'option', 'text', $this->blnJqText);
						}
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

		/**
		* If this control is attachable to a codegenerated control in a metacontrol, this function will be
		* used by the metacontrol designer dialog to display a list of options for the control.
		* @return QMetaParam[]
		**/
		public static function GetMetaParams() {
			return array_merge(parent::GetMetaParams(), array(
				new QMetaParam (get_called_class(), 'Disabled', 'Disables the button if set to <code>true</code>.', QType::Boolean),
				new QMetaParam (get_called_class(), 'Label', 'Text to show in the button. When not specified (<code>null</code>), the element&apos;s HTML content is used, or its <code>value</code> attribute if the element is an input element of type submit or reset, or the HTML content of the associated label element if the element is an input of type radio or checkbox.', QType::String),
				new QMetaParam (get_called_class(), 'JqText', 'Whether to show the label. When set to <code>false</code> no text will be displayed, but the <a><code>icons</code></a> option must be enabled, otherwise the <code>text</code> option will be ignored.', QType::Boolean),
			));
		}
	}

?>
