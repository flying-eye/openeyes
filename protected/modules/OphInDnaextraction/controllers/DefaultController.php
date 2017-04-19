<?php

class DefaultController extends BaseEventTypeController
{
    var $box_id;
    var $letter;
    var $number;
    
    protected static $action_types = array(
        'addTransaction' => self::ACTION_TYPE_FORM,
        'GetNewStorageFields' => self::ACTION_TYPE_FORM,
        'getAvailableLetterNumberToBox' => self::ACTION_TYPE_FORM,
        'saveNewStorage' => self::ACTION_TYPE_FORM,
        'refreshStorageSelect' => self::ACTION_TYPE_FORM,
    );

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('Create', 'Update', 'View', 'Print', 'AddTransaction','GetNewStorageFields','getAvailableLetterNumberToBox','saveNewStorage', 'refreshStorageSelect'),
                'roles' => array('OprnEditDNAExtraction'),
            ),
            array('allow',
                'actions' => array('View', 'Print'),
                'roles' => array('OprnViewDNAExtraction'),
            ),
        );
    }

    public function volumeRemaining($event_id)
    {
        $api = Yii::app()->moduleAPI->get('OphInDnaextraction');

        return $api->volumeRemaining($event_id);
    }

    public function actionPrint($id)
    {
        parent::actionPrint($id);
    }

    public function actionAddTransaction()
    {
        if (!isset($_GET['i'])) {
            throw new Exception('Row number not set');
        }

        $transaction = new OphInDnaextraction_DnaTests_Transaction();
        $transaction->setDefaultOptions();
        $this->renderPartial('_dna_test', array(
            'i' => $_GET['i'],
            'transaction' => $transaction,
            'disabled' => false,
        ));
    }

    public function getFormTransactions()
    {
        $transactions = array();

        if (!empty($_POST['date'])) {
            foreach ($_POST['date'] as $i => $date) {
                if ($_POST['transactionID'][$i]) {
                    $_transaction = OphInDnaextraction_DnaTests_Transaction::model()->findByPk($_POST['transactionID'][$i]);
                } else {
                    $_transaction = new OphInDnaextraction_DnaTests_Transaction();
                }
                $_transaction->date = date('Y-m-d', strtotime($date));
                $_transaction->study_id = $_POST['study_id'][$i];
                $_transaction->volume = $_POST['volume'][$i];
                $_transaction->comments = $_POST['comments'][$i];
                $transactions[] = $_transaction;
            }
        }

        return $transactions;
    }

    /*
     * Validate element related models
     */

    protected function setAndValidateElementsFromData($data)
    {
        $errors = parent::setAndValidateElementsFromData($data);

        if (!empty($data['date'])) {
            foreach ($this->getFormTransactions() as $transaction) {
                if (!$transaction->validate()) {
                    foreach ($transaction->getErrors() as $errormsgs) {
                        foreach ($errormsgs as $error) {
                            $errors['Tests'][] = $error;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    protected function saveComplexAttributes_Element_OphInDnaextraction_DnaTests($element, $data, $index)
    {
        $item_ids = array();

        foreach ($this->getFormTransactions() as $transaction) {
            $transaction->element_id = $element->id;

            if (!$transaction->save()) {
                throw new Exception('Unable to save transaction: '.print_r($transaction->getErrors(), true));
            }

            $item_ids[] = $transaction->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('element_id = :element_id');
        $criteria->addNotInCondition('id', $item_ids);
        $criteria->params[':element_id'] = $element->id;

        foreach (OphInDnaextraction_DnaTests_Transaction::model()->findAll($criteria) as $transaction) {
            if (!$transaction->delete()) {
                throw new Exception('Unable to delete transaction: '.print_r($transaction->getErrors(), true));
            }
        }
    }

    public function isRequiredInUI(BaseEventTypeElement $element)
    {
        return true;
    }
    
    public function actionGetNewStorageFields()
    {
        $element = new Element_OphInDnaextraction_DnaExtraction();
        $this->renderPartial('newStoragePopup', array('element'=> $element), false, true);
    }
    
    public function actionGetAvailableLetterNumberToBox( )
    {
        $result = array();
        if((int)$_POST['box_id'] > '0'){
            $storage = new OphInDnaextraction_DnaExtraction_Storage();
            $boxModel = new OphInDnaextraction_DnaExtraction_Box();
            $boxRanges = $boxModel->boxMaxValues($_POST['box_id']);  

            $letterArray = $storage->generateLetterArrays($_POST['box_id'], $boxRanges['maxletter'] , $boxRanges['maxnumber']);       
            $usedBoxRows = $storage->getAllLetterNumberToBox( $_POST['box_id'] );


            $arrayDiff = array_filter($letterArray, function ($element) use ($usedBoxRows) {
                return !in_array($element, $usedBoxRows);
            });

            foreach($arrayDiff as $key => $val){
                if($val['letter'] == "0"){
                    $result['letter'] = "You have not specified a maximum letter value.";
                    $result['number'] = "You have not specified a maximum number value.";
                } else {
                    $result['letter'] = $val['letter'];
                    $result['number'] = $val['number'];
                }

                break;
            }
        }
 
        echo json_encode($result);
    }

    public function actionSaveNewStorage()
    {
        $storage = new OphInDnaextraction_DnaExtraction_Storage();
        
        $storage->box_id = Yii::app()->request->getPost('dnaextraction_box_id');
        $storage->letter = Yii::app()->request->getPost('dnaextraction_letter');
        $storage->number = Yii::app()->request->getPost('dnaextraction_number');
        $storage->letter = $storage->letter ? strtoupper($storage->letter) : $storage->letter;

        if( $storage->save() ){
            $result = array('s' => 1);
        } else {
            $errors = '';
            foreach( $storage->getErrors() as $attribute => $error ){
                $errors .= $errors == '' ? '' : "<br>";
                $errors .= "$attribute: " . ( implode($error) );
            }
            $result = array(
                's' => 0,
                'msg' => $errors
            );
        }

        echo json_encode($result);
    }
    
    public function actionRefreshStorageSelect()
    {
        $element = new Element_OphInDnaextraction_DnaExtraction();
        $this->renderPartial('_boxSelectRefresh', array('element'=> $element), false, true);
    }

    private function _registerDnaTestFormJs()
    {
        $assetPath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$this->getModule()->name.'.assets'));
        Yii::app()->clientScript->registerScriptFile($assetPath.'/js/dna_tests_form.js');
    }

    public function actionView($id)
    {
        $this->_registerDnaTestFormJs();
        parent::actionView($id);
    }

    public function actionUpdate($id)
    {
        $this->_registerDnaTestFormJs();
        parent::actionUpdate($id);
    }

    public function actionUpdateDnaTests($id)
    {
        $errors = $this->setAndValidateElementsFromData($_POST);

        if (empty($errors)) {
            $transaction = Yii::app()->db->beginTransaction();

            try {
                //TODO: should all the auditing be moved into the saving of the event
                $success = $this->saveEvent($_POST);

                if ($success) {
                    //TODO: should not be pasing event?
                    $this->afterUpdateElements($this->event);
                    $this->logActivity('updated event');

                    $this->event->audit('event', 'update');

                    $this->event->user = Yii::app()->user->id;

                    if (!$this->event->save()) {
                        throw new SystemException('Unable to update event: '.print_r($this->event->getErrors(), true));
                    }

                    OELog::log("Updated event {$this->event->id}");
                    $transaction->commit();
                    if ($this->event->parent_id) {
                        $this->redirect(Yii::app()->createUrl('/'.$this->event->parent->eventType->class_name.'/default/view/'.$this->event->parent_id));
                    } else {
                        $this->redirect(array('default/view/'.$this->event->id));
                    }
                } else {
                    throw new Exception('Unable to save edits to event');
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }

        echo json_encode(array('success'=>true));
        exit;
    }

}
