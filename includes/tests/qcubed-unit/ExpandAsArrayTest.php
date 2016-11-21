<?php
/**
 * Tests for the ExpandAsArray functionality in QQuery
 * 
 * @package Tests
 */
// If the test is being run in php cli mode, the autoloader does not work.
// Check to see if the models you need exist and if not, include them here.
if(!class_exists('Person')){
    require_once __INCLUDES__ .'/model/Person.class.php';
}
if(!class_exists('Project')){
    require_once __INCLUDES__ .'/model/Project.class.php';
}
if(!class_exists('Login')){
    require_once __INCLUDES__ .'/model/Login.class.php';
}
if(!class_exists('Milestone')){
    require_once __INCLUDES__ .'/model/Milestone.class.php';
}
if(!class_exists('Address')){
    require_once __INCLUDES__ .'/model/Address.class.php';
}
if(!class_exists('PersonType')){
    require_once __INCLUDES__ .'/model/PersonType.class.php';
}
if(!class_exists('TwoKey')){
    require_once __INCLUDES__ .'/model/TwoKey.class.php';
}
if(!class_exists('ProjectStatusType')){
    require_once __INCLUDES__ .'/model/ProjectStatusType.class.php';
}
if(!class_exists('Login')){
    require_once __INCLUDES__ .'/model/Login.class.php';
}

class ExpandAsArrayTests extends QUnitTestCaseBase {

	public function testMultiLevel() {
		$arrPeople = Person::LoadAll(
			self::getTestClauses()
		);
				
		$this->assertEquals(12, sizeof($arrPeople), "12 Person objects found");
		$targetPerson = $this->verifyObjectPropertyHelper($arrPeople, 'LastName', 'Wolfe');
		
		$this->helperVerifyKarenWolfe($targetPerson);
		
		$objProjectArray = $targetPerson->_ProjectAsManagerArray;
		$this->assertEquals(2, sizeof($objProjectArray), "2 projects found");
		
		foreach ($objProjectArray as $objProject) {
			$objMilestoneArray = $objProject->_MilestoneArray;
			
			switch ($objProject->Id) {
				case 1:
					$this->assertEquals(3, sizeof($objMilestoneArray), "3 milestones found");
					break;
					
				case 4:
					$this->assertEquals(4, sizeof($objMilestoneArray), "4 milestones found");
					break;
					
				default:
					$this->assertTrue(false, 'Unexpected project found, id: ' . $objProject->Id);
					break;
			}
		}
		
		// Now test a multilevel expansion where first level does not expand by array. Should get duplicate entries at that level.
		$clauses = QQ::Clause(
			QQ::ExpandAsArray(QQN::Person()->Address),
			QQ::Expand(QQN::Person()->ProjectAsManager),
			QQ::ExpandAsArray(QQN::Person()->ProjectAsManager->Milestone)
		);

		$arrPeople = Person::LoadAll(
			$clauses
		);

		// Karen Wolfe should duplicate, since she is managing two projects
		$this->assertEquals(13, sizeof($arrPeople), "13 Person objects found");
		$targetPerson = $this->verifyObjectPropertyHelper($arrPeople, 'LastName', 'Wolfe');

		$objProjectArray = $targetPerson->_ProjectAsManagerArray;
		$this->assertNull($objProjectArray, "No project array found");

		$objProject = $targetPerson->_ProjectAsManager;
		$this->assertNotNull($objProject, "Project found");
		
		$objMilestoneArray = $objProject->_MilestoneArray;
		// since we didn't specify the order, not sure which one we will get, so check for either
		switch ($objProject->Id) {
			case 1:
				$this->assertEquals(3, sizeof($objMilestoneArray), "3 milestones found");
				break;
				
			case 4:
				$this->assertEquals(4, sizeof($objMilestoneArray), "4 milestones found");
				break;
				
			default:
				$this->assertTrue(false, 'Unexpected project found, id: ' . $objProject->Id);
				break;
		}
	}
	
	public function testQuerySingle() {
		$targetPerson = Person::QuerySingle(
			QQ::Equal(QQN::Person()->Id, 7),
			self::getTestClauses()
		);
		
		$this->helperVerifyKarenWolfe($targetPerson);
		
		$objTwoKey = TwoKey::QuerySingle(
			QQ::AndCondition (
				QQ::Equal(QQN::TwoKey()->Server, 'google.com'),
				QQ::Equal(QQN::TwoKey()->Directory, 'mail')
			),
			QQ::Clause(
				QQ::ExpandAsArray(QQN::TwoKey()->Project->PersonAsTeamMember)
			)
		);
		
		$this->assertEquals (count($objTwoKey->Project->_PersonAsTeamMemberArray), 6, '6 team members found.');
	}
	
	public function testEmptyArray() {
		$arrPeople = Person::QuerySingle(
			QQ::Equal(QQN::Person()->Id, 2),
			self::getTestClauses()
			);
			
		$this->assertTrue(is_array($arrPeople->_ProjectAsManagerArray), "_ProjectAsManagerArray is an array");
		$this->assertEquals(0, count($arrPeople->_ProjectAsManagerArray), "_ProjectAsManagerArray has no Project objects");
	}

	public function testNullArray() {
		$arrPeople = Person::QuerySingle(
			QQ::Equal(QQN::Person()->Id, 2)
			);
		
		$this->assertTrue(is_null($arrPeople->_ProjectAsManagerArray), "_ProjectAsManagerArray is null");
	}
	
	public function testTypeExpansion() {		
		$clauses = QQ::Clause(
			QQ::ExpandAsArray (QQN::Person()->PersonType)
		);
		
		$objPerson = 
			Person::QuerySingle(
				QQ::Equal (QQN::Person()->Id, 7),
				$clauses
			);
		
		$intPersonTypeArray = $objPerson->_PersonTypeArray;
		$this->assertEquals(array(
			PersonType::Manager,
			PersonType::CompanyCar)
			, $intPersonTypeArray
			, "PersonType expansion is correct");
	}

	private static function getTestClauses() {
		return QQ::Clause(
			QQ::ExpandAsArray(QQN::Person()->Address),
			QQ::ExpandAsArray(QQN::Person()->ProjectAsManager),
			QQ::ExpandAsArray(QQN::Person()->ProjectAsManager->Milestone)
		);
	}
	
	private function helperVerifyKarenWolfe(Person $targetPerson) {		
		$this->assertEquals(2, sizeof($targetPerson->_ProjectAsManagerArray), "2 projects found");
		$targetProject = $this->verifyObjectPropertyHelper($targetPerson->_ProjectAsManagerArray, 'Name', 'ACME Payment System');
		
		$this->assertEquals(4, sizeof($targetProject->_MilestoneArray), "4 milestones found");
		$this->verifyObjectPropertyHelper($targetProject->_MilestoneArray, 'Name', 'Milestone H');
	}

	public function testSelectSubsetInExpand() {
		$objPersonArray = Person::QueryArray(
			QQ::OrCondition(
				QQ::Like(QQN::Person()->ProjectAsManager->Name, '%ACME%'),
				QQ::Like(QQN::Person()->ProjectAsManager->Name, '%HR%')
			),
			// Let's expand on the Project, itself
			QQ::Clause(
				QQ::Select(QQN::Person()->LastName),
				QQ::Expand(QQN::Person()->ProjectAsManager, null, QQ::Select(QQN::Person()->ProjectAsManager->Spent)),
				QQ::OrderBy(QQN::Person()->LastName, QQN::Person()->FirstName)
			)
		);

		foreach ($objPersonArray as $objPerson) {
			$this->setExpectedException('QCallerException');
			$objPerson->FirstName; // FirstName should throw exception, since it was not selected
			$this->setExpectedException(null);

			$this->assertNotNull($objPerson->Id, "Id should not be null since it's always added to the select list");
			$this->assertNotNull($objPerson->_ProjectAsManager->Id, "ProjectAsManager->Id should not be null since id's are always added to the select list");

			$this->setExpectedException('QCallerException');
			$objPerson->_ProjectAsManager->Name; // not selected
			$this->setExpectedException(null);
		}
	}

	public function testSelectSubsetInExpandAsArray() {
		$objPersonArray = Person::LoadAll(
			QQ::Clause(
				QQ::Select(QQN::Person()->FirstName),
				QQ::ExpandAsArray(QQN::Person()->Address, QQ::Select(QQN::Person()->Address->Street, QQN::Person()->Address->City)),
				QQ::ExpandAsArray(QQN::Person()->ProjectAsManager, QQ::Select(QQN::Person()->ProjectAsManager->StartDate)),
				QQ::ExpandAsArray(QQN::Person()->ProjectAsManager->Milestone, QQ::Select(QQN::Person()->ProjectAsManager->Milestone->Name))
			)
		);

		foreach ($objPersonArray as $objPerson) {
			$this->setExpectedException('QCallerException');
			$objPerson->LastName; // Should throw exception, since it was not selected
			$this->setExpectedException(null);

			$this->assertNotNull($objPerson->Id, "Id should not be null since it's always added to the select list");
			if (sizeof($objPerson->_AddressArray) > 0) {
				foreach ($objPerson->_AddressArray as $objAddress) {
					$this->assertNotNull($objAddress->Id, "Address->Id should not be null since it's always added to the select list");

					$this->setExpectedException('QCallerException');
					$objAddress->PersonId; // Should throw exception, since it was not selected
					$this->setExpectedException(null);
				}
			}
			if (sizeof($objPerson->_ProjectAsManagerArray) > 0) {
				foreach($objPerson->_ProjectAsManagerArray as $objProject) {
					$this->assertNotNull($objProject->Id, "Project->Id should not be null since it's always added to the select list");

					$this->setExpectedException('QCallerException');
					$objProject->Name; // Should throw exception, since it was not selected
					$this->setExpectedException(null);

					if (sizeof($objProject->_MilestoneArray) > 0) {
						foreach ($objProject->_MilestoneArray as $objMilestone) {
							$this->assertNotNull($objMilestone->Id, "Milestone->Id should not be null since it's always added to the select list");

							$this->setExpectedException('QCallerException');
							$objMilestone->ProjectId; // Should throw exception, since it was not selected
							$this->setExpectedException(null);
						}
					}
				}
			}
		}
	}
	
	public function testMultiLeafExpansion() {
		$objMilestone = Milestone::QuerySingle(
			QQ::Equal (QQN::Milestone()->Id, 1),
			QQ::Clause(
				QQ::ExpandAsArray(QQN::Milestone()->Project->ManagerPerson->ProjectAsTeamMember),
				QQ::ExpandAsArray(QQN::Milestone()->Project->PersonAsTeamMember)
			)
		);
		
		$objProjectArray = $objMilestone->Project->ManagerPerson->_ProjectAsTeamMemberArray;
		$objPeopleArray = $objMilestone->Project->_PersonAsTeamMemberArray;
		
		$this->assertTrue(is_array($objProjectArray), "_ProjectAsTeamMemberArray is an array");
		$this->assertEquals(2, count($objProjectArray), "_ProjectAsTeamMemberArray has 2 Project objects");
		
		$this->assertTrue(is_array($objPeopleArray), "_PersonAsTeamMemberArray is an array");
		$this->assertEquals(5, count($objPeopleArray), "_PersonAsTeamMemberArray has 5 People objects");
		
		// try through a unique relationship
		$objLogin = Login::QuerySingle(
			QQ::Equal (QQN::Login()->PersonId, 7),
			QQ::Clause(
				QQ::ExpandAsArray(QQN::Login()->Person->ProjectAsTeamMember),
				QQ::ExpandAsArray(QQN::Login()->Person->ProjectAsManager)
			)
		);
		
		$objProjectArray = $objLogin->Person->_ProjectAsTeamMemberArray;
		
		$this->assertTrue(is_array($objProjectArray), "_ProjectAsTeamMemberArray is an array");
		$this->assertEquals(2, count($objProjectArray), "_ProjectAsTeamMemberArray has 2 Project objects");
		
		$objProjectArray = $objLogin->Person->_ProjectAsManagerArray;
		
		$this->assertTrue(is_array($objProjectArray), "_ProjectAsManagerArray is an array");
		$this->assertEquals(2, count($objProjectArray), "_ProjectAsManagerArray has 2 Project objects");
				
	}

	public function testConditionalExpansion() {
		$clauses = QQ::Clause(
			QQ::ExpandAsArray(QQN::Person()->Address),
			QQ::Expand(QQN::Person()->ProjectAsManager, QQ::Equal (QQN::Person()->ProjectAsManager->ProjectStatusTypeId, ProjectStatusType::Open)),
			QQ::ExpandAsArray(QQN::Person()->ProjectAsManager->Milestone),
			QQ::OrderBy(QQN::Person()->Id)
		);
		
		$targetPersonArray = Person::LoadAll (
			$clauses
		);
		
		$targetPerson = reset($targetPersonArray);
		
		$this->assertEquals ($targetPerson->Id, 1, "Person 1 found.");
		$this->assertNotNull ($targetPerson->_ProjectAsManager, "Person 1 has a project.");

		$targetPerson = end($targetPersonArray);
		
		$this->assertEquals ($targetPerson->Id, 12, "Person 12 found.");
		$this->assertNull ($targetPerson->_ProjectAsManager, "Person 12 does not have a project.");

	}

	public function testConditionalExpansion2() {
		$clauses = QQ::Clause(
			QQ::Expand(QQN::Login()->Person->ProjectAsManager, QQ::Equal (QQN::Login()->Person->ProjectAsManager->ProjectStatusTypeId, ProjectStatusType::Open)),
			QQ::ExpandAsArray(QQN::Login()->Person->ProjectAsManager->Milestone),
			QQ::ExpandAsArray(QQN::Login()->Person->Address),
			QQ::OrderBy(QQN::Login()->Person->Id)
		);

		$cond = QQ::In (QQN::Login()->PersonId, [1,3,7]);
		$targetLoginArray = Login::QueryArray (
			$cond,
			$clauses
		);

		$targetLogin = reset($targetLoginArray);
		$this->assertEquals ($targetLogin->Person->Id, 1, "Person 1 found.");
		$this->assertNotNull ($targetLogin->Person->_ProjectAsManager, "Person 1 has an open project.");

		$targetLogin = next($targetLoginArray);
		$this->assertEquals ($targetLogin->Person->Id, 3, "Person 3 found.");
		$this->assertNull ($targetLogin->Person->_ProjectAsManager, "Person 3 does not have an open project.");

		$targetLogin = next($targetLoginArray);
		$this->assertEquals ($targetLogin->Person->Id, 7, "Person 7 found.");
		$this->assertNull ($targetLogin->Person->_ProjectAsManager, "Person 7 does have an open project.");

	}


	public function testConditionalExpansion3() {

		// A complex join with conditions. Find all team members of completed projects which have an open child project.
		$clauses = QQ::Clause(
			QQ::Expand(QQN::Person()->ProjectAsTeamMember->Project, QQ::Equal(QQN::Person()->ProjectAsTeamMember->Project->ProjectStatusTypeId, ProjectStatusType::Completed)),
			QQ::Expand(QQN::Person()->ProjectAsTeamMember->Project->ProjectAsRelated->Project, QQ::Equal(QQN::Person()->ProjectAsTeamMember->Project->ProjectAsRelated->Project->ProjectStatusTypeId, ProjectStatusType::Open))
		);

		$cond = QQ::IsNotNull(QQN::Person()->ProjectAsTeamMember->Project->ProjectAsRelated->Project->Id); // Filter out unsuccessful joins

		$targetPersonArray = Person::QueryArray (
			$cond,
			$clauses
		);

		$targetPerson = reset($targetPersonArray);

		$this->assertEquals(ProjectStatusType::Completed, $targetPerson->ProjectAsTeamMember->ProjectStatusTypeId, "Found completed parent project");
		$this->assertEquals(ProjectStatusType::Open, $targetPerson->ProjectAsTeamMember->ProjectAsRelated->ProjectStatusTypeId, "Found open child project");

	}

	public function testConditionalExpansionReverse() {
		// Get all people, and projects they are managing if the projects are open.
		$a = Person::QueryArray(
			QQ::All(),
			[
				QQ::ExpandAsArray(QQN::Person()->ProjectAsManager, QQ::Equal(QQN::Person()->ProjectAsManager->ProjectStatusTypeId, ProjectStatusType::Open)),
				QQ::OrderBy(QQN::Person()->Id)
			]
		);

		$this->assertEquals(3, $a[0]->_ProjectAsManagerArray[0]->Id);
	}

	public function testConditionalExpansionAssociation() {
		// Conditional expansion on association nodes really can only work with the PK of the join.

		// Get all projects, and also expand on related projects if the id is 1
		$a = Project::QueryArray(
			QQ::All(),
			[
				QQ::ExpandAsArray(QQN::Project()->ParentProjectAsRelated, QQ::Equal(QQN::Project()->ParentProjectAsRelated->ProjectId, 1)),
				QQ::ExpandAsArray(QQN::Project()->ProjectAsRelated, QQ::Equal(QQN::Project()->ProjectAsRelated->Project->Id, 1)),
				QQ::OrderBy(QQN::Project()->Id)
			]
		);

		$this->assertEquals(1, $a[2]->_ParentProjectAsRelatedArray[0]->Id);
	}



	public function testDataGridHtml() {
		$objMilestone = Milestone::QuerySingle(
			QQ::Equal (QQN::Milestone()->Id, 1),
			QQ::Clause(
				QQ::Expand(QQN::Milestone()->Project->ManagerPerson)
			)
		);

		$_ITEM =$objMilestone;
		$node = QQN::Milestone()->Project->ManagerPerson;

		$html = $node->GetDataGridHtml();
		$val = eval(sprintf('return %s;', $html));
		$this->assertEquals ($val, "Person Object 7");
	}
	
}
