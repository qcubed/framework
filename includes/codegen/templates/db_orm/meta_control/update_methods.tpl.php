<?php
 foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) {
	 if (isset($objManyToManyReference->Options['FormGen']) && $objManyToManyReference->Options['FormGen'] == QFormGen::None) continue;

	 $strControlType = $objCodeGen->MetaControlControlClass($objManyToManyReference);

	 $objReflection = new ReflectionClass ($strControlType);
	 $blnHasMethod = $objReflection->hasMethod ('Codegen_MetaRefresh');
	 if ($blnHasMethod) {
		 echo $strControlType::Codegen_MetaUpdateMethod($objCodeGen, $objTable, $objManyToManyReference);
	 } else {
		 throw new QCallerException ('Can\'t find Codegen_MetaUpdate for ' . $strControlType);
	 }
}
?>