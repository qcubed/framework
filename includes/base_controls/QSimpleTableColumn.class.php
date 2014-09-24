<?php

	/**
	 * Represents a column for a SimpleTable. Different subclasses (see below) allow accessing and fetching the data
	 * for each cells in a variety of ways
	 *
	 * @property string $Name name of the column
	 * @property string $CssClass css class of the column
	 * @property string $HeaderCssClass css class of the column when it's rendered in a table header
	 * @property boolean $HtmlEntities if true, cell values will be converted using htmlentities()
	 * @property boolean $RenderAsHeader if true, all cells in the column will be rendered with a <<th>> tag instead of <<td>>
	 * @property integer $Id HTML id attribute to put in the col tag
	 * @property integer $Span HTML span attribute to put in the col tag
	 * @property-read QSimpleTableBase $ParentTable parent table of the column
	 * @property boolean $Visible Whether the column will be drawn. Defaults to true.
	 */
	abstract class QAbstractSimpleTableColumn extends QBaseClass {
		/** @var string */
		protected $strName;
		/** @var string */
		protected $strCssClass = null;
		/** @var string */
		protected $strHeaderCssClass = null;
		/** @var boolean */
		protected $blnHtmlEntities = true;
		/** @var boolean */
		protected $blnRenderAsHeader = false;
		/** @var QSimpleTableBase */
		protected $objParentTable = null;
		/** @var integer */
		protected $intSpan = 1;
		/** @var string optional id for column tag rendering and datatables*/
		protected $strId = null;
		/** @var bool Easy way to hide a column without removing the column. */
		protected $blnVisible = true;
		
		/**
		 * @param string $strName Name of the column
		 */
		public function __construct($strName) {
			$this->strName = $strName;
		}

		/**
		 * 
		 * Render the header cell including opening and closing tags. 
		 * 
		 * This will be called by the data table if ShowHeader is on, and will only
		 * be called for the top line item.
		 * 
		 */
		public function RenderHeaderCell() {
			if (!$this->blnVisible) return '';

			$cellValue = $this->FetchHeaderCellValue();
			if ($this->blnHtmlEntities)
				$cellValue = QApplication::HtmlEntities($cellValue);
			if ($cellValue == '' && QApplication::IsBrowser(QBrowserType::InternetExplorer)) {
				$cellValue = '&nbsp;';
			}
			
			$strToReturn = '<th';
			$aParams = $this->GetHeaderCellParams();
			foreach ($aParams as $key=>$str) {
				$strToReturn .= ' ' . $key . '="' . $str . '"';
			}
			$strToReturn .= '>' . $cellValue . '</th>';
			return $strToReturn;
		}
		
		/**
		 * Returns the text to print in the header cell, if one is to be drawn. Override if you want
		 * something other than the default.
		 */
		public function FetchHeaderCellValue() {
			return $this->strName;
		}

		/**
		 * Returns an array of key/value pairs to insert as parameters in the header cell. Override and add
		 * more if you need them.
		 * @return array
		 */
		public function GetHeaderCellParams () {
			$aParams = array();
			if ($this->strHeaderCssClass) {
				$aParams['class'] = $this->strHeaderCssClass;
			}
			return $aParams;		
		}
		
		/**
		 * Render a cell. 
		 * 
		 * Called by data table for each cell. Override and call with $blnHeader = true if you want
		 * this individual cell to render with <<th>> tags instead of <<td>>.
		 * 
		 * @param mixed $item
		 * @param boolean $blnAsHeader
		 */
		public function RenderCell($item, $blnAsHeader = false) {
			if (!$this->blnVisible) return '';

			$cellValue = $this->FetchCellValue($item);
			if ($this->blnHtmlEntities)
				$cellValue = QApplication::HtmlEntities($cellValue);
			if ($cellValue == '' && QApplication::IsBrowser(QBrowserType::InternetExplorer)) {
				$cellValue = '&nbsp;';
			}
	
			if ($blnAsHeader || $this->blnRenderAsHeader) {
				$tag = 'th';
			} else {
				$tag = 'td';
			}
			
			$strToReturn = '<' . $tag;
			
			$aParams = $this->GetCellParams($item);
			foreach ($aParams as $key=>$str) {
				$strToReturn .= ' ' . $key . '="' . $str . '"';
			}
			$strToReturn .= '>' . $cellValue . '</' . $tag . '>';
			return $strToReturn;
		}

		/**
		 * Return a key/val array of items to insert inside the cell tag. 
		 * 
		 * Handles class, style, and id already. Override to add additional items, like an onclick handler.
		 * No checking is done on these params, the raw strings are output
		 * 
		 * @param mixed $item
		 */
		protected function GetCellParams ($item) {
			$aParams = array();
			if ($strClass = $this->GetCellClass ($item)) {
				$aParams['class'] = $strClass;
			}
			
			if ($strId = $this->GetCellId ($item)) {
				$aParams['id'] = addslashes($strId);
			}
			
			if ($strStyle = $this->GetCellStyle ($item)) {
				$aParams['style'] = $strStyle;
			}
			return $aParams;		
		}
		
		/**
		 * Return the class of the cell.
		 * @param mixed $item
		 */
		protected function GetCellClass ($item) {
			if ($this->strCssClass) {
				return $this->strCssClass;
			}
			return '';
		}
		
		/**
		 * Return the id of the cell.
		 * @param mixed $item
		 */
		protected function GetCellId ($item) {
			return '';
		}
		
		/**
		 * Return the style string for the cell.
		 * @param unknown_type $item
		 */
		protected function GetCellStyle ($item) {
			return '';
		}
		
		/**
		 * Return the raw string that represents the cell value. 
		 * 
		 * @param mixed $item
		 */
		abstract public function FetchCellValue($item);
		
		public function RenderColTag() {
			$strToReturn = '<col ';
			
			$aParams = $this->GetColParams();
			foreach ($aParams as $key=>$str) {
				$strToReturn .= $key . '="' . $str . '" ';
			}
			$strToReturn .= '/>';
			return $strToReturn;
		}

		/**
		 * Return a key/value array of parameters to put in the col tag.
		 * Override to add parameters.
		 */
		protected function GetColParams () {
			$aParams = array();
			if ($this->intSpan > 1) {
				$aParams['span'] = $this->intSpan;
			}
			if ($this->strId) {
				$aParams['id'] = addslashes($this->strId);
			}
			if ($this->strCssClass) {
				$aParams['class'] = addslashes($this->strCssClass);
			}

			return $aParams;		
		}
		
		public function __get($strName) {
			switch ($strName) {
				case 'Name':
					return $this->strName;
				case 'CssClass':
					return $this->strCssClass;
				case 'HeaderCssClass':
					return $this->strHeaderCssClass;
				case 'HtmlEntities':
					return $this->blnHtmlEntities;
				case 'RenderAsHeader':
					return $this->blnRenderAsHeader;
				case 'ParentTable':
					return $this->objParentTable;
				case 'Span':
					return $this->intSpan;
				case 'Id':
					return $this->strId;
				case 'Visible':
					return $this->blnVisible;
					
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
				case "Name":
					try {
						$this->strName = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "CssClass":
					try {
						$this->strCssClass = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "HeaderCssClass":
					try {
						$this->strHeaderCssClass = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "HtmlEntities":
					try {
						$this->blnHtmlEntities = QType::Cast($mixValue, QType::Boolean);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					
				case "RenderAsHeader":
					try {
						$this->blnRenderAsHeader = QType::Cast($mixValue, QType::Boolean);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					
				case "Span":
					try {
						$this->intSpan = QType::Cast($mixValue, QType::Integer);
						if ($this->intSpan < 1) {
							throw new Exception("Span must be 1 or greater.");
						}
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "Id":
					try {
						$this->strId = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "Visible":
					try {
						$this->blnVisible = QType::Cast($mixValue, QType::Boolean);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "_ParentTable":
					try {
						$this->objParentTable = QType::Cast($mixValue, 'QSimpleTableBase');
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

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
	
	/**
	 * An abstract column designed to work with QDataTables and other tables that require more than basic columns.
	 * Supports post processing of cell contents for further formatting, and orderby clauses. 
	 *
	 * @property QQOrderBy $OrderByClause order by clause for sorting the column in ascending order. Used by QDataTables plugin.
	 * @property QQOrderBy $ReverseOrderByClause order by clause for sorting the column in descending order. Used by QDataTables plugin.
	 * @property string $Format the default format to use for FetchCellValueFormatted(). Used by QDataTables plugin.
	 *    For date columns it should be a format accepted by QDateTime::qFormat()
	 * @property-write string $PostMethod after the cell object is retrieved, call this method on the obtained object
	 * @property-write callback $PostCallback after the cell object is retrieved, call this callback on the obtained object.
	 *    If $PostMethod is also set, this will be called after that method call.
	 */
	 
	abstract class QAbstractSimpleTableDataColumn extends QAbstractSimpleTableColumn {
		/** @var QQOrderBy */
		protected $objOrderByClause = null;
		/** @var QQOrderBy */
		protected $objReverseOrderByClause = null;
		/** @var string */
		protected $strFormat = null;
		/** @var string */
		protected $strPostMethod = null;
		/** @var callback */
		protected $objPostCallback = null;
		
		/**
		 * Return the raw string that represents the cell value. 
		 * 
		 * This version uses a combination of post processing strategies so that you can set 
		 * column options to format the raw data. If no
		 * options are set, then $item will just pass through, or __toString() will be called
		 * if its an object. If none of these work for you, just override FetchCellObject and
		 * return your formatted string from there.
		 * 
		 * @param mixed $item
		 */
		public function FetchCellValue($item) {
			$cellValue = $this->FetchCellObject($item);
						
			if ($cellValue !== null && $this->strPostMethod) {
				$strPostMethod = $this->strPostMethod;
				$cellValue = $cellValue->$strPostMethod();
			}
			if ($this->objPostCallback) {
				$cellValue = call_user_func($this->objPostCallback, $cellValue);
			}
			if (!$cellValue)
				return '';

			if ($cellValue instanceof QDateTime) {
				return $cellValue->qFormat($this->strFormat);
			}
			if (is_object($cellValue)) {
				$cellValue = $cellValue->__toString();
			}
			if ($this->strFormat)
				return sprintf($this->strFormat, $cellValue);
			return $cellValue;
		}

		/**
		 * Return the value of the cell. FetchCellValue will process this more if needed.
		 * Default returns an entire data row and relies on FetchCellValue to extract the needed data.
		 * 
		 * @param mixed $item
		 */
		abstract public function FetchCellObject($item);

		public function __get($strName) {
			switch ($strName) {
				case "OrderByClause":
					return $this->objOrderByClause;
				case "ReverseOrderByClause":
					return $this->objReverseOrderByClause;
				case "Format":
					return $this->strFormat;

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
				case "OrderByClause":
					try {
						$this->objOrderByClause = QType::Cast($mixValue, 'QQOrderBy');
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "ReverseOrderByClause":
					try {
						$this->objReverseOrderByClause = QType::Cast($mixValue, 'QQOrderBy');
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "Format":
					try {
						$this->strFormat = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "PostMethod":
					try {
						$this->strPostMethod = QType::Cast($mixValue, QType::String);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "PostCallback":
					$this->objPostCallback = $mixValue;
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

	/**
	 * Displays a  property of an object, as in $object->Property 
	 * 
	 * User this to if your DataSource contains objects to display a particular property of that object in this
	 * column. Can search with depth to, as in $obj->Prop1->Prop2.
	 *
	 * @property string $Property the property to use when accessing the objects in the DataSource array. Can be a s
	 *  series of properties separated with '->', i.e. 'Prop1->Prop2->Prop3' will find the Prop3 item inside the Prop2 object,
	 *  inside the Prop1 object, inside the current object.
	 * @property boolean $NullSafe if true the value fetcher will check for nulls before accessing the properties
	 *
	 */
	class QSimpleTablePropertyColumn extends QAbstractSimpleTableDataColumn {
		protected $strProperty;
		protected $strPropertiesArray;
		protected $blnNullSafe = true;

		/**
		 * @param string $strName name of the column
		 * @param string $strProperty the property name to use when accessing the DataSource row object
		 * @param QQBaseNode $objBaseNode if not null the OrderBy and ReverseOrderBy clauses will be created using the property path and the given database node
		 */
		public function __construct($strName, $strProperty, $objBaseNode = null) {
			parent::__construct($strName);
			$this->Property = $strProperty;

			if ($objBaseNode != null) {
				foreach ($this->strPropertiesArray as $strProperty) {
					$objBaseNode = $objBaseNode->$strProperty;
				}

				$this->OrderByClause = QQ::OrderBy($objBaseNode);
				$this->ReverseOrderByClause = QQ::OrderBy($objBaseNode, 'desc');
			}
		}

		public function FetchCellObject($item) {
			if ($this->blnNullSafe && $item == null)
				return null;
			foreach ($this->strPropertiesArray as $strProperty) {
				$item = $item->$strProperty;
				if ($this->blnNullSafe && $item == null)
					break;
			}
			return $item;
		}

		public function __get($strName) {
			switch ($strName) {
				case 'Property':
					return $this->strProperty;
				case 'NullSafe':
					return $this->blnNullSafe;
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
				case "Property":
					try {
						$this->strProperty = QType::Cast($mixValue, QType::String);
						$this->strPropertiesArray = $this->strProperty ? explode('->', $this->strProperty) : array();
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case "NullSafe":
					try {
						$this->blnNullSafe = QType::Cast($mixValue, QType::Boolean);
						break;
					} catch (QInvalidCastException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

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

	/**
	 * A type of column that should be used when the DataSource items are arrays
	 *
	 * @property int|string $Index the index or key to use when accessing the arrays in the DataSource array
	 *
	 */
	class QSimpleTableIndexedColumn extends QAbstractSimpleTableDataColumn {
		protected $mixIndex;

		/**
		 * @param string $strName name of the column
		 * @param int|string $mixIndex the index or key to use when accessing the DataSource row array
		 */
		public function __construct($strName, $mixIndex) {
			parent::__construct($strName);
			$this->mixIndex = $mixIndex;
		}

		public function FetchCellObject($item) {
			if (isset ($item[$this->mixIndex])) {
				return $item[$this->mixIndex];
			} else {
				return '';
			}
		}

		public function __get($strName) {
			switch ($strName) {
				case 'Index':
					return $this->mixIndex;
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
				case "Index":
					$this->mixIndex = $mixValue;
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

	/**
	 * A type of column based on a user specified function (Closure) that can be used when a complex logic is required
	 * to fetch the cell data from the DataSource items
	 *
	 * @property int|string $Index the index or key to use when accessing the arrays in the DataSource array
	 *
	 */
	class QSimpleTableClosureColumn extends QAbstractSimpleTableDataColumn implements Serializable {
		/** @var callback */
		protected $objClosure;
		/** @var array extra parameters passed to closure */
		protected $mixParams;

		/**
		 * @param string $strName name of the column
		 * @param callback $objClosure a callable object (e.g. Closure). It should take a single argument, and it
		 * @param mixed $mixParams extra parameters to pass to the closure callback.
		 * will be called with the row of the DataSource as that single argument.
		 *
		 * @throws InvalidArgumentException
		 */
		public function __construct($strName, $objClosure, $mixParams = null) {
			parent::__construct($strName);
			if (!is_callable($objClosure)) {
				throw new InvalidArgumentException();
			}
			$this->objClosure = $objClosure;
			$this->mixParams = $mixParams;
		}

		public function FetchCellObject($item) {
			if ($this->mixParams) {
				return call_user_func($this->objClosure, $item, $this->mixParams);
			} else {
				return call_user_func($this->objClosure, $item);
			}
		}

		/**
		 * (PHP 5 &gt;= 5.1.0)<br/>
		 * String representation of object
		 * @link http://php.net/manual/en/serializable.serialize.php
		 * @return string the string representation of the object or &null;
		 */
		public function serialize() {
			$vars = array(
				$this->strName,
				$this->strCssClass,
				$this->strHeaderCssClass,
				$this->blnHtmlEntities,
				$this->objOrderByClause,
				$this->objReverseOrderByClause);
			// Closure is a feature of PHP 5.3
			// unfortunately, as of PHP 5.3.6 Closure is not serializable
			// this code can be removed when Closures become serializable in PHP
			if (version_compare(PHP_VERSION, '5.3.0', '<') || (!$this->objClosure instanceof Closure)) {
				$vars[] = $this->objClosure;
				$vars[] = $this->mixParams;
			}
			return serialize($vars);
		}

		/**
		 * (PHP 5 &gt;= 5.1.0)<br/>
		 * Constructs the object
		 * @link http://php.net/manual/en/serializable.unserialize.php
		 * @param string $serialized <p>
		 * The string representation of the object.
		 * </p>
		 * @throws RuntimeException
		 * @return mixed the original value unserialized.
		 */
		public function unserialize($serialized) {
			$vars = unserialize($serialized);
			$cnt = count($vars);
			if ($cnt == 6) {
				list($this->strName,
						$this->strCssClass,
						$this->strHeaderCssClass,
						$this->blnHtmlEntities,
						$this->objOrderByClause,
						$this->objReverseOrderByClause
						) = $vars;
			} else if ($cnt == 7) {
				list($this->strName,
						$this->strCssClass,
						$this->strHeaderCssClass,
						$this->blnHtmlEntities,
						$this->objOrderByClause,
						$this->objReverseOrderByClause,
						$this->objClosure
						) = $vars;
			} else if ($cnt == 8) {
				list($this->strName,
					$this->strCssClass,
					$this->strHeaderCssClass,
					$this->blnHtmlEntities,
					$this->objOrderByClause,
					$this->objReverseOrderByClause,
					$this->objClosure,
					$this->mixParams
					) = $vars;
			} else {
				throw new RuntimeException("wrong number of variables when unserializing QSimpleTableClosureColumn");
			}
		}

		public function __get($strName) {
			switch ($strName) {
				case 'Closure':
					return $this->objClosure;
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
				case "Closure":
					if (!is_callable($mixValue)) {
						throw new QInvalidCastException("Closure must be a callable object");
					}
					$this->objClosure = $mixValue;
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
	
	/**
	 * 
	 * A column to display a virtual attribute from a database record.
	 *
	 * @property string Attribute 
	 */
	class QVirtualAttributeColumn extends QAbstractSimpleTableDataColumn {
		protected $strAttribute;
		
		public function __construct($strName, $strAttribute = null) {
			parent::__construct($strName);
			if ($strAttribute) {
				$this->strAttribute = $strAttribute;
			}
		}
		
		public function FetchCellObject($item) {
			return $item->GetVirtualAttribute ($this->strAttribute);
		}
		
		public function __get($strName) {
			switch ($strName) {
				case 'Attribute':
					return $this->strAttribute;
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
				case "Attribute":
					$this->strAttribute = QType::Cast ($mixValue, QType::String);
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
	
	/**
	 * 
	 * A column of checkboxes. 
	 * 
	 * Prints checkboxes in a column, including the header. Override this class and implement whatever hooks you need. In
	 * particular implement the CheckId hooks, and IsChecked hooks. 
	 *
	 */
	class QSimpleTableCheckBoxColumn extends QAbstractSimpleTableDataColumn {
		protected $blnHtmlEntities = false;
		protected $checkParamCallback = null;
		
		public function FetchHeaderCellValue() {
			$strToReturn = '<input type="checkbox"';
			$aParams = $this->GetHeaderCheckboxParams();
			foreach ($aParams as $key=>$str) {
				$strToReturn .= ' ' . $key . '="' . $str . '"';
			}
			
			$strToReturn .= ' />';
			return $strToReturn;
		}

		public function GetHeaderCheckboxParams () {
			$aParams = array();
			
			if ($strId = $this->GetHeaderCheckId ()) {
				$aParams['id'] = addslashes($strId);
			}
			
			if ($strCheck = $this->IsHeaderChecked ()) {
				$aParams['checked'] = 'checked';
			}
			return $aParams;		
		}
		
		function GetHeaderCheckId () {
			return null;	
		}
		
		function IsHeaderChecked () {
			return false;
		}
		
		public function FetchCellObject($item) {

			$strToReturn = '<input type="checkbox"';

			$aParams = $this->GetCheckboxParams($item);
			foreach ($aParams as $key=>$str) {
				$strToReturn .= ' ' . $key . '="' . $str . '"';
			}

			$strToReturn .= ' />';
			return $strToReturn;
		}
		
		public function GetCheckboxParams ($item) {
			$aParams = array();
			
			if ($this->checkParamCallback) {
				$aParams = call_user_func($this->checkParamCallback, $item, 'id');
				$aParams['id'] = addslashes($aParams['id']);
				return $aParams;
			}
			
			
			if ($strId = $this->GetCheckId ($item)) {
				$aParams['id'] = addslashes($strId);
			}
			
			if ($strCheck = $this->IsChecked ($item)) {
				$aParams['checked'] = 'checked';
			}
			return $aParams;		
		}
		
		public function SetCheckParamCallback ($closure) {
			$this->checkParamCallback = $closure;
		}
		
		/**
		 * Returns the id for the checkbox itself. This is used together with the check action to send the item
		 * id to the action. This system currently supports only one checkbox column. 
		 * @param unknown_type $item
		 */
		function GetCheckId ($item) {
			return null;
		}
		
		function GetItemId ($item){}
		
		function IsChecked ($item) {
			return false;
		}
	}
	

?>
