<?php

class Application_Model_Option_MultipleChoiceMapper
{
    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }

        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }

        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Option_MultipleChoice');
        }

        return $this->_dbTable;
    }

    public function save(Application_Model_Option_MultipleChoice $oType)
    {
        $aData = array(
            'question' => $oType->getQuestion(),
            'created' => $oType->getCreated()
        );

        if (null === ($iId = $oType->getId())) {
            unset($aData['question_id']);
            $aData['date_input']  = date('Y-m-d H:i:s');
            $oType->setId($this->getDbTable()->insert($aData));
        } else {
            $this->getDbTable()->update($aData, array('question_id = ?' => $iId));
        }
    }

    public function find($iId, Application_Model_Option_Type $oOption)
    {
        $oResult = $this->getDbTable()->find($iId);
        if (0 == count($oResult)) {
            return;
        }

        $oRow = $oResult->current();
        self::mapObjectToModel($oRow, $oOption);
    }

    public function fetchAll()
    {
        $oResultSet = $this->getDbTable()->fetchAll();
        return self::mapEntriesToModels($oResultSet);
    }

    public function fetchOptions($iOptionId)
    {
        $oWhere = $this->getDbTable()->select()->where('option_id = ?', $iOptionId);
        $oResultSet = $this->getDbTable()->fetchAll($oWhere);
        return self::mapEntriesToModels($oResultSet);
    }

    public static function mapEntriesToModels(Zend_Db_Table_Rowset_Abstract $oRows)
    {
        $aEntries = array();
        foreach ($oRows as $oRow) {
            $aEntries[] = self::mapObjectToModel($oRow);
        }
        return $aEntries;
    }

    public static function mapObjectToModel(Zend_Db_Table_Row_Abstract $oRow, Application_Model_Option_MultipleChoice $oMc = null)
    {
        if (null === $oMc) {
            $oMc = new Application_Model_Option_MultipleChoice();
        }
        $oMc->setOptionId($oRow->option_id);
        $oMc->setOptionMcId($oRow->option_mc_id);
        $oMc->setText($oRow->text);

        return $oMc;
    }
}