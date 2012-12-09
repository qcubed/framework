<?php
	/**
	 * The abstract QDialogGen class defined here is
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
	 * update QCubed state variables. See the QDialogBase 
	 * file, which contains code to interface between this generated file and QCubed.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the QDialog class file.
	 *
	 */

	/* Custom event classes for this control */
	
	
	/**
	 * This event is triggered when dialog is created.
	 */
	class QDialog_CreateEvent extends QJqUiEvent {
		const EventName = 'dialogcreate';
	}
	/**
	 * This event is triggered when a dialog attempts to close. If the beforeClose
	 * 		event handler (callback function) returns false, the close will be
	 * 		prevented.
	 */
	class QDialog_BeforeCloseEvent extends QJqUiEvent {
		const EventName = 'dialogbeforeclose';
	}
	/**
	 * This event is triggered when dialog is opened.
	 */
	class QDialog_OpenEvent extends QJqUiEvent {
		const EventName = 'dialogopen';
	}
	/**
	 * This event is triggered when the dialog gains focus.
	 */
	class QDialog_FocusEvent extends QJqUiEvent {
		const EventName = 'dialogfocus';
	}
	/**
	 * This event is triggered at the beginning of the dialog being dragged.
	 */
	class QDialog_DragStartEvent extends QJqUiEvent {
		const EventName = 'dialogdragstart';
	}
	/**
	 * This event is triggered when the dialog is dragged.
	 */
	class QDialog_DragEvent extends QJqUiEvent {
		const EventName = 'dialogdrag';
	}
	/**
	 * This event is triggered after the dialog has been dragged.
	 */
	class QDialog_DragStopEvent extends QJqUiEvent {
		const EventName = 'dialogdragstop';
	}
	/**
	 * This event is triggered at the beginning of the dialog being resized.
	 */
	class QDialog_ResizeStartEvent extends QJqUiEvent {
		const EventName = 'dialogresizestart';
	}
	/**
	 * This event is triggered when the dialog is resized. demo
	 */
	class QDialog_ResizeEvent extends QJqUiEvent {
		const EventName = 'dialogresize';
	}
	/**
	 * This event is triggered after the dialog has been resized.
	 */
	class QDialog_ResizeStopEvent extends QJqUiEvent {
		const EventName = 'dialogresizestop';
	}
	/**
	 * This event is triggered when the dialog is closed.
	 */
	class QDialog_CloseEvent extends QJqUiEvent {
		const EventName = 'dialogclose';
	}

	/* Custom "property" event classes for this control */

	/**
	 * @property boolean $Disabled Disables (true) or enables (false) the dialog. Can be set when initialising
	 * 		(first creating) the dialog.
	 * @property boolean $AutoOpen When autoOpen is true the dialog will open automatically when dialog is
	 * 		called. If false it will stay hidden until .dialog("open") is called on it.
	 * @property mixed $Buttons Specifies which buttons should be displayed on the dialog. The property key
	 * 		is the text of the button. The value is the callback function for when the
	 * 		button is clicked.  The context of the callback is the dialog element; if
	 * 		you need access to the button, it is available as the target of the event
	 * 		object.
	 * @property array $Buttons1 Specifies which buttons should be displayed on the dialog. Each element of
	 * 		the array must be an Object defining the properties to set on the button.
	 * @property boolean $CloseOnEscape Specifies whether the dialog should close when it has focus and the user
	 * 		presses the esacpe (ESC) key.
	 * @property string $CloseText Specifies the text for the close button. Note that the close text is
	 * 		visibly hidden when using a standard theme.
	 * @property string $DialogClass The specified class name(s) will be added to the dialog, for additional
	 * 		theming.
	 * @property boolean $Draggable If set to true, the dialog will be draggable will be draggable by the
	 * 		titlebar.
	 * @property integer $Height The height of the dialog, in pixels. Specifying 'auto' is also supported to
	 * 		make the dialog adjust based on its content.
	 * @property string $Hide The effect to be used when the dialog is closed.
	 * @property mixed $Hide1 The effect to be used when the dialog is closed.
	 * @property integer $MaxHeight The maximum height to which the dialog can be resized, in pixels.
	 * @property integer $MaxWidth The maximum width to which the dialog can be resized, in pixels.
	 * @property integer $MinHeight The minimum height to which the dialog can be resized, in pixels.
	 * @property integer $MinWidth The minimum width to which the dialog can be resized, in pixels.
	 * @property boolean $Modal If set to true, the dialog will have modal behavior; other items on the
	 * 		page will be disabled (i.e. cannot be interacted with). Modal dialogs
	 * 		create an overlay below the dialog but above other page elements.
	 * @property mixed $Position Specifies where the dialog should be displayed. Possible values: 1) a
	 * 		single string representing position within viewport: 'center', 'left',
	 * 		'right', 'top', 'bottom'. 2) an array containing an x,y coordinate pair in
	 * 		pixel offset from left, top corner of viewport (e.g. [350,100]) 3) an array
	 * 		containing x,y position string values (e.g. ['right','top'] for top right
	 * 		corner).
	 * @property boolean $Resizable If set to true, the dialog will be resizable.
	 * @property string $Show The effect to be used when the dialog is opened.
	 * @property mixed $Show1 The effect to be used when the dialog is opened.
	 * @property boolean $Stack Specifies whether the dialog will stack on top of other dialogs. This will
	 * 		cause the dialog to move to the front of other dialogs when it gains focus.
	 * @property string $Title Specifies the title of the dialog. Any valid HTML may be set as the title.
	 * 		The title can also be specified by the title attribute on the dialog source
	 * 		element.
	 * @property integer $Width The width of the dialog, in pixels.
	 * @property integer $ZIndex The starting z-index for the dialog.
	 */

	class QDialogGen extends QPanel	{
		protected $strJavaScripts = __JQUERY_EFFECTS__;
		protected $strStyleSheets = __JQUERY_CSS__;
		/** @var boolean */
		protected $blnDisabled = null;
		/** @var boolean */
		protected $blnAutoOpen = null;
		/** @var mixed */
		protected $mixButtons = null;
		/** @var array */
		protected $arrButtons1 = null;
		/** @var boolean */
		protected $blnCloseOnEscape = null;
		/** @var string */
		protected $strCloseText = null;
		/** @var string */
		protected $strDialogClass = null;
		/** @var boolean */
		protected $blnDraggable = null;
		/** @var integer */
		protected $intHeight = null;
		/** @var string */
		protected $strHide = null;
		/** @var mixed */
		protected $mixHide1 = null;
		/** @var integer */
		protected $intMaxHeight = null;
		/** @var integer */
		protected $intMaxWidth = null;
		/** @var integer */
		protected $intMinHeight = null;
		/** @var integer */
		protected $intMinWidth = null;
		/** @var boolean */
		protected $blnModal = null;
		/** @var mixed */
		protected $mixPosition = null;
		/** @var boolean */
		protected $blnResizable = null;
		/** @var string */
		protected $strShow = null;
		/** @var mixed */
		protected $mixShow1 = null;
		/** @var boolean */
		protected $blnStack = null;
		/** @var string */
		protected $strTitle = null;
		/** @var integer */
		protected $intWidth = null;
		/** @var integer */
		protected $intZIndex = null;
		
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
			$strJqOptions .= $this->makeJsProperty('AutoOpen', 'autoOpen');
			$strJqOptions .= $this->makeJsProperty('Buttons', 'buttons');
			$strJqOptions .= $this->makeJsProperty('Buttons1', 'buttons');
			$strJqOptions .= $this->makeJsProperty('CloseOnEscape', 'closeOnEscape');
			$strJqOptions .= $this->makeJsProperty('CloseText', 'closeText');
			$strJqOptions .= $this->makeJsProperty('DialogClass', 'dialogClass');
			$strJqOptions .= $this->makeJsProperty('Draggable', 'draggable');
			$strJqOptions .= $this->makeJsProperty('Height', 'height');
			$strJqOptions .= $this->makeJsProperty('Hide', 'hide');
			$strJqOptions .= $this->makeJsProperty('Hide1', 'hide');
			$strJqOptions .= $this->makeJsProperty('MaxHeight', 'maxHeight');
			$strJqOptions .= $this->makeJsProperty('MaxWidth', 'maxWidth');
			$strJqOptions .= $this->makeJsProperty('MinHeight', 'minHeight');
			$strJqOptions .= $this->makeJsProperty('MinWidth', 'minWidth');
			$strJqOptions .= $this->makeJsProperty('Modal', 'modal');
			$strJqOptions .= $this->makeJsProperty('Position', 'position');
			$strJqOptions .= $this->makeJsProperty('Resizable', 'resizable');
			$strJqOptions .= $this->makeJsProperty('Show', 'show');
			$strJqOptions .= $this->makeJsProperty('Show1', 'show');
			$strJqOptions .= $this->makeJsProperty('Stack', 'stack');
			$strJqOptions .= $this->makeJsProperty('Title', 'title');
			$strJqOptions .= $this->makeJsProperty('Width', 'width');
			$strJqOptions .= $this->makeJsProperty('ZIndex', 'zIndex');
			if ($strJqOptions) $strJqOptions = substr($strJqOptions, 0, -2);
			return $strJqOptions;
		}

		public function getJqSetupFunction() {
			return 'dialog';
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
			return $str . $this->GetControlJavaScript() . '; ' . parent::GetEndScript();
		}
		
		/**
		 * Call a JQuery UI Method on the object. Takes variable number of arguments.
		 * 
		 * @param string $strMethodName the method name to call
		 * @internal param $mixed [optional] $mixParam1
		 * @internal param $mixed [optional] $mixParam2
		 */
		protected function CallJqUiMethod($strMethodName /*, ... */) {
			$args = func_get_args();

			$strArgs = JavaScriptHelper::toJsObject($args);
			$strJs = sprintf('jQuery("#%s").%s(%s)',
				$this->getJqControlId(),
				$this->getJqSetupFunction(),
				substr($strArgs, 1, strlen($strArgs)-2));	// params without brackets
			QApplication::ExecuteJavaScript($strJs);
		}


		/**
		 * Remove the dialog functionality completely. This will return the element
		 * back to its pre-init state.
		 */
		public function Destroy() {
			$this->CallJqUiMethod("destroy");
		}
		/**
		 * Disable the dialog.
		 */
		public function Disable() {
			$this->CallJqUiMethod("disable");
		}
		/**
		 * Enable the dialog.
		 */
		public function Enable() {
			$this->CallJqUiMethod("enable");
		}
		/**
		 * Get or set any dialog option. If no value is specified, will act as a
		 * getter.
		 * @param $optionName
		 * @param $value
		 */
		public function Option($optionName, $value = null) {
			$this->CallJqUiMethod("option", $optionName, $value);
		}
		/**
		 * Set multiple dialog options at once by providing an options object.
		 * @param $options
		 */
		public function Option1($options) {
			$this->CallJqUiMethod("option", $options);
		}
		/**
		 * Close the dialog.
		 */
		public function Close() {
			$this->CallJqUiMethod("close");
		}
		/**
		 * Returns true if the dialog is currently open.
		 */
		public function IsOpen() {
			$this->CallJqUiMethod("isOpen");
		}
		/**
		 * Move the dialog to the top of the dialogs stack.
		 */
		public function MoveToTop() {
			$this->CallJqUiMethod("moveToTop");
		}
		/**
		 * Open the dialog.
		 */
		public function Open() {
			$this->CallJqUiMethod("open");
		}


		public function __get($strName) {
			switch ($strName) {
				case 'Disabled': return $this->blnDisabled;
				case 'AutoOpen': return $this->blnAutoOpen;
				case 'Buttons': return $this->mixButtons;
				case 'Buttons1': return $this->arrButtons1;
				case 'CloseOnEscape': return $this->blnCloseOnEscape;
				case 'CloseText': return $this->strCloseText;
				case 'DialogClass': return $this->strDialogClass;
				case 'Draggable': return $this->blnDraggable;
				case 'Height': return $this->intHeight;
				case 'Hide': return $this->strHide;
				case 'Hide1': return $this->mixHide1;
				case 'MaxHeight': return $this->intMaxHeight;
				case 'MaxWidth': return $this->intMaxWidth;
				case 'MinHeight': return $this->intMinHeight;
				case 'MinWidth': return $this->intMinWidth;
				case 'Modal': return $this->blnModal;
				case 'Position': return $this->mixPosition;
				case 'Resizable': return $this->blnResizable;
				case 'Show': return $this->strShow;
				case 'Show1': return $this->mixShow1;
				case 'Stack': return $this->blnStack;
				case 'Title': return $this->strTitle;
				case 'Width': return $this->intWidth;
				case 'ZIndex': return $this->intZIndex;
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
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'disabled', $this->blnDisabled);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'AutoOpen':
					try {
						$this->blnAutoOpen = QType::Cast($mixValue, QType::Boolean);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'autoOpen', $this->blnAutoOpen);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Buttons':
					$this->mixButtons = $mixValue;
				
					if ($this->Rendered) {
						$this->CallJqUiMethod('option', 'buttons', $mixValue);
					}
					break;

				case 'Buttons1':
					try {
						$this->arrButtons1 = QType::Cast($mixValue, QType::ArrayType);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'buttons', $this->arrButtons1);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'CloseOnEscape':
					try {
						$this->blnCloseOnEscape = QType::Cast($mixValue, QType::Boolean);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'closeOnEscape', $this->blnCloseOnEscape);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'CloseText':
					try {
						$this->strCloseText = QType::Cast($mixValue, QType::String);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'closeText', $this->strCloseText);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'DialogClass':
					try {
						$this->strDialogClass = QType::Cast($mixValue, QType::String);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'dialogClass', $this->strDialogClass);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Draggable':
					try {
						$this->blnDraggable = QType::Cast($mixValue, QType::Boolean);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'draggable', $this->blnDraggable);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Height':
					try {
						$this->intHeight = QType::Cast($mixValue, QType::Integer);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'height', $this->intHeight);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Hide':
					try {
						$this->strHide = QType::Cast($mixValue, QType::String);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'hide', $this->strHide);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Hide1':
					$this->mixHide1 = $mixValue;
				
					if ($this->Rendered) {
						$this->CallJqUiMethod('option', 'hide', $mixValue);
					}
					break;

				case 'MaxHeight':
					try {
						$this->intMaxHeight = QType::Cast($mixValue, QType::Integer);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'maxHeight', $this->intMaxHeight);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'MaxWidth':
					try {
						$this->intMaxWidth = QType::Cast($mixValue, QType::Integer);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'maxWidth', $this->intMaxWidth);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'MinHeight':
					try {
						$this->intMinHeight = QType::Cast($mixValue, QType::Integer);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'minHeight', $this->intMinHeight);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'MinWidth':
					try {
						$this->intMinWidth = QType::Cast($mixValue, QType::Integer);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'minWidth', $this->intMinWidth);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Modal':
					try {
						$this->blnModal = QType::Cast($mixValue, QType::Boolean);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'modal', $this->blnModal);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Position':
					$this->mixPosition = $mixValue;
				
					if ($this->Rendered) {
						$this->CallJqUiMethod('option', 'position', $mixValue);
					}
					break;

				case 'Resizable':
					try {
						$this->blnResizable = QType::Cast($mixValue, QType::Boolean);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'resizable', $this->blnResizable);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Show':
					try {
						$this->strShow = QType::Cast($mixValue, QType::String);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'show', $this->strShow);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Show1':
					$this->mixShow1 = $mixValue;
				
					if ($this->Rendered) {
						$this->CallJqUiMethod('option', 'show', $mixValue);
					}
					break;

				case 'Stack':
					try {
						$this->blnStack = QType::Cast($mixValue, QType::Boolean);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'stack', $this->blnStack);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Title':
					try {
						$this->strTitle = QType::Cast($mixValue, QType::String);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'title', $this->strTitle);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Width':
					try {
						$this->intWidth = QType::Cast($mixValue, QType::Integer);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'width', $this->intWidth);
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'ZIndex':
					try {
						$this->intZIndex = QType::Cast($mixValue, QType::Integer);
						if ($this->Rendered) {
							$this->CallJqUiMethod('option', 'zIndex', $this->intZIndex);
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
	}

?>
