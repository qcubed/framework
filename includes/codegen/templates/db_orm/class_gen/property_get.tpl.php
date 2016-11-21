		/**
		 * Override method to perform a property "Get"
		 * This will get the value of $strName
		 *
		 * @param string $strName Name of the property to get
		 * @return mixed
		 */
		public function __get($strName) {
			// Use getter if it exists
			$strMethod = 'get' . $strName;
			if (method_exists($this, $strMethod)) {
				return $this->$strMethod();
			}

			switch ($strName) {
				///////////////////
				// Member Objects
				///////////////////
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
<?php if ($objColumn->Reference) { ?>
<?php if (!$objColumn->Reference->IsType) { ?>
				case '<?= $objColumn->Reference->PropertyName ?>':
					/**
					 * Gets the value for the <?= $objColumn->Reference->VariableType ?> object referenced by <?= $objColumn->VariableName ?> <?php if ($objColumn->Identity) print '(Read-Only PK)'; else if ($objColumn->PrimaryKey) print '(PK)'; else if ($objColumn->Unique) print '(Unique)'; else if ($objColumn->NotNull) print '(Not Null)'; ?>

					 * @return <?= $objColumn->Reference->VariableType ?>

					 */
					try {
						if ((!$this-><?= $objColumn->Reference->VariableName ?>) && (!is_null($this-><?= $objColumn->VariableName ?>)))
							$this-><?= $objColumn->Reference->VariableName ?> = <?= $objColumn->Reference->VariableType ?>::Load($this-><?= $objColumn->VariableName ?>);
						return $this-><?= $objColumn->Reference->VariableName ?>;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
<?php } else { // type ?>
				case '<?= $objColumn->Reference->PropertyName ?>':
					/**
					 * Gets the value for the <?= $objColumn->Reference->VariableType ?> type referenced by <?= $objColumn->VariableName ?>
					 * @return string
					 */
					if ($this-><?= $objColumn->VariableName ?>) {
						return <?= $objColumn->Reference->VariableType ?>::ToString($this-><?= $objColumn->VariableName ?>);
					} else {
						return '';
					}

<?php } ?>
<?php } ?>
<?php } ?>
<?php foreach ($objTable->ReverseReferenceArray as $objReverseReference) { ?>
<?php if ($objReverseReference->Unique) { ?>
<?php $objReverseReferenceTable = $objCodeGen->TableArray[strtolower($objReverseReference->Table)]; ?>
<?php $objReverseReferenceColumn = $objReverseReferenceTable->ColumnArray[strtolower($objReverseReference->Column)]; ?>
				case '<?= $objReverseReference->ObjectPropertyName ?>':
					/**
					 * Gets the value for the <?= $objReverseReference->VariableType ?> object that uniquely references this <?= $objTable->ClassName ?>

					 * by <?= $objReverseReference->ObjectMemberVariable ?> (Unique)
					 * @return <?= $objReverseReference->VariableType ?>

					 */
					try {
						if (!$this->__blnRestored ||
								$this-><?= $objReverseReference->ObjectMemberVariable ?> === false)
							// Either this is a new object, or we've attempted early binding -- and the reverse reference object does not exist
							return null;
						if (!$this-><?= $objReverseReference->ObjectMemberVariable ?>)
							$this-><?= $objReverseReference->ObjectMemberVariable ?> = <?= $objReverseReference->VariableType ?>::LoadBy<?= $objReverseReferenceColumn->PropertyName ?>(<?= $objCodeGen->ImplodeObjectArray(', ', '$this->', '', 'VariableName', $objTable->PrimaryKeyColumnArray) ?>);
						return $this-><?= $objReverseReference->ObjectMemberVariable ?>;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

<?php } ?>
<?php } ?>

				////////////////////////////
				// Virtual Object References (Many to Many and Reverse References)
				// (If restored via a "Many-to" expansion)
				////////////////////////////

<?php foreach ($objTable->ManyToManyReferenceArray as $objReference) { ?>
<?php 
	$objAssociatedTable = $objCodeGen->GetTable($objReference->AssociatedTable);
	$varPrefix = (is_a($objAssociatedTable, 'QTypeTable') ? '_int' : '_obj');
	$varType = (is_a($objAssociatedTable, 'QTypeTable') ? 'integer' : $objReference->VariableType);
?>
				case '<?= $objReference->ObjectDescription ?>':
				case '_<?= $objReference->ObjectDescription ?>': // for backwards compatibility
					/**
					 * Gets the value for the protected <?= $varPrefix . $objReference->ObjectDescription ?> (Read-Only)
					 * if set due to an expansion on the <?= $objReference->Table ?> association table
					 * @return <?= $varType ?>

					 */
					return $this-><?= $varPrefix . $objReference->ObjectDescription ?>;

				case '<?= $objReference->ObjectDescription ?>Array':
				case '_<?= $objReference->ObjectDescription ?>Array': // for backwards compatibility
					/**
					 * Gets the value for the protected <?= $varPrefix . $objReference->ObjectDescription ?>Array (Read-Only)
					 * if set due to an ExpandAsArray on the <?= $objReference->Table ?> association table
					 * @return <?= $varType ?>[]
					 */
					return $this-><?= $varPrefix . $objReference->ObjectDescription ?>Array;


<?php } ?><?php foreach ($objTable->ReverseReferenceArray as $objReference) { ?><?php if (!$objReference->Unique) { ?>
				case '<?= $objReference->ObjectDescription ?>':
				case '_<?= $objReference->ObjectDescription ?>':
					/**
					 * Gets the value for the protected _obj<?= $objReference->ObjectDescription ?> (Read-Only)
					 * if set due to an expansion on the <?= $objReference->Table ?>.<?= $objReference->Column ?> reverse relationship
					 * @return <?= $objReference->VariableType ?>

					 */
					return $this->_obj<?= $objReference->ObjectDescription ?>;

				case '<?= $objReference->ObjectDescription ?>Array':
				case '_<?= $objReference->ObjectDescription ?>Array':
					/**
					 * Gets the value for the protected _obj<?= $objReference->ObjectDescription ?>Array (Read-Only)
					 * if set due to an ExpandAsArray on the <?= $objReference->Table ?>.<?= $objReference->Column ?> reverse relationship
					 * @return <?= $objReference->VariableType ?>[]
					 */
					return $this->_obj<?= $objReference->ObjectDescription ?>Array;

<?php } ?><?php } ?>

				case '__Restored':
					return $this->__blnRestored;

				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}