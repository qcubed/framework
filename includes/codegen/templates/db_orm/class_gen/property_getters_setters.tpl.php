//////////////////////////////////////////////////////////////
//															//
//				GETTERS and SETTERS  						//
//															//
//////////////////////////////////////////////////////////////

<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
   /**
	* Gets the value for <?= $objColumn->VariableName ?> <?php if ($objColumn->Identity) print '(Read-Only PK)'; else if ($objColumn->PrimaryKey) print '(PK)'; else if ($objColumn->Timestamp) print '(Read-Only Timestamp)'; else if ($objColumn->Unique) print '(Unique)'; else if ($objColumn->NotNull) print '(Not Null)'; ?>

	* @throws QCallerException
	* @return <?= $objColumn->VariableType ?>

	*/
	public function get<?= $objColumn->PropertyName ?>() {
<?php if (!$objColumn->Identity) { ?>
		if (empty($this->__blnValid[self::<?= strtoupper($objColumn->Name) ?>_FIELD])) {
			throw new QCallerException("<?= $objColumn->PropertyName ?> has not been set nor was selected in the most recent query and is not valid.");
		}
<?php } ?>
		return $this-><?= $objColumn->VariableName ?>;
	}
<?php if ($objColumn->Reference && $objColumn->Reference->IsType) { ?>

   /**
	* Gets the value for <?= $objColumn->VariableName ?> as a type name.
	* @return string
	*/
	public function get<?= $objColumn->Reference->PropertyName ?>() {
		return <?= $objColumn->Reference->PropertyName ?>::toString($this-><?= $objColumn->VariableName ?>);
	}
<?php } ?>

<?php 	if ((!$objColumn->Identity) && (!$objColumn->Timestamp)) { ?>

   /**
	* Sets the value for <?= $objColumn->VariableName ?> <?php if ($objColumn->PrimaryKey) print '(PK)'; else if ($objColumn->Unique) print '(Unique)'; else if ($objColumn->NotNull) print '(Not Null)'; ?>

	* Returns $this to allow chaining of setters.
	* @param <?= $objColumn->VariableType ?> $<?= $objColumn->VariableName ?>

	* @return <?= $objTable->ClassName ?>

	*/
	public function set<?= $objColumn->PropertyName ?>($<?= $objColumn->VariableName ?>) {
		$<?= $objColumn->VariableName ?> = QType::Cast($<?= $objColumn->VariableName ?>, <?= $objColumn->VariableTypeAsConstant ?>);

		if ($this-><?= $objColumn->VariableName ?> !== $<?= $objColumn->VariableName ?>) {
<?php if (($objColumn->Reference) && (!$objColumn->Reference->IsType)) { ?>
			$this-><?= $objColumn->Reference->VariableName ?> = null; // remove the associated object
<?php } ?>
			$this-><?= $objColumn->VariableName ?> = $<?= $objColumn->VariableName ?>;
			$this->__blnDirty[self::<?= strtoupper($objColumn->Name) ?>_FIELD] = true;
		}
		$this->__blnValid[self::<?= strtoupper($objColumn->Name) ?>_FIELD] = true;
		return $this; // allows chaining
	}

<?php 	} ?>

<?php } ?>