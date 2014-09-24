	 * @property-read <?php echo $objTable->ClassName  ?> $<?php echo $objTable->ClassName  ?> the actual <?php echo $objTable->ClassName  ?> data class being edited
<?php 
	foreach ($objTable->ColumnArray as $objColumn) {
		if (isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == 'none') continue; 
		if (!isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] != 'label') { ?>
	 * @property <?php echo $objCodeGen->FormControlClassForColumn($objColumn);  ?> $<?php echo $objColumn->PropertyName  ?>Control
<?php 	} ?>
	 * @property-read QLabel $<?php echo $objColumn->PropertyName  ?>Label
<?php } ?>
<?php 
	foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
		if ($objReverseReference->Unique) { 
?>
	 * @property QListBox $<?php echo $objReverseReference->ObjectDescription ?>Control
	 * @property-read QLabel $<?php echo $objReverseReference->ObjectDescription ?>Label
<?php
		} 
	} 
?>
<?php foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) { ?>
	 * @property QListBox $<?php echo $objManyToManyReference->ObjectDescription ?>Control
	 * @property-read QLabel $<?php echo $objManyToManyReference->ObjectDescription ?>Label
<?php } ?>
	 * @property-read string $TitleVerb a verb indicating whether or not this is being edited or created
	 * @property-read boolean $EditMode a boolean indicating whether or not this is being edited or created