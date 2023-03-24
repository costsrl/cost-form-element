<?php

/**
 * @author fabio <paiva.fabiofelipe@gmail.com> 
 */

namespace CostFormElement\Controller;

use CostAdmin\Controller\BasicController;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\Form\Factory;
use Doctrine\Common\Annotations\AnnotationReader;
use Zf2DoctrineAutocomplete\Form\Element\ObjectAutocomplete;

class SearchController extends BasicController {

    private $proxy;
    private $objects;
    private $om;
    private $options;
    protected $tableService;
    
    
    public function searchselectAction() {
        $className      = str_replace('-', '\\',$this->params()->fromRoute('element'));  
        $classTag       = $this->params()->fromRoute('tag');
        $propertyName   = $this->params()->fromRoute('property');
        $term           = $this->params()->fromQuery('q', '');
        $sm             =  $this->getServiceLocator();
        $em             =  $sm->get('doctrine.entitymanager.orm_default');
        
        
        
        $element = new \CostFormElement\Form\Element\SelectAutocomplete();
        $element->setOption('sm', $this->getServiceLocator());
        
        /** call factory form **/
        if($sm->has($className)){
            if($sm->get($className) instanceof  \Laminas\Form\Form){
                // retrive proprty from form
                if($sm->get($className)->has($classTag)){
                    $className = $sm->get($className)->get($classTag);
                    $optionAutocomplete = $className->getOptions();
                }
            }
        }
        else{ // from annotation
            $entity         = $className;
            $elementName    = $classTag;
            $docReader      = new AnnotationReader();
            $reflect        = new \ReflectionClass($entity);
            $fields         = array($propertyName => $propertyName);
            foreach ($fields as $key => $val)
            {
                if (!$reflect->hasProperty($key)) {
                    var_dump('the entity does not have a such property');
                    continue;
                }
                $docInfos = $docReader->getPropertyAnnotations($reflect->getProperty($key));
                foreach ($docInfos as $docInfo){
                    if($docInfo instanceof \Laminas\Form\Annotation\Options){
                        $optionAutocomplete = $docInfo->getOptions();
                    }
                }
            }
        }
        $targetClass = $optionAutocomplete['target_class'];
        
        
        // to do define adapter (doctrine , Laminas/sql Laminas/tablegateway
        $qb = $em->getRepository($targetClass)->createQueryBuilder('q');
        
        $driver = '';
        if (class_exists("\Doctrine\ORM\QueryBuilder") && $qb instanceof \Doctrine\ORM\QueryBuilder) {
            /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb->setMaxResults(20);
            $driver = 'orm';
        } elseif (class_exists("\Doctrine\ODM\MongoDB\Query\Builder") && $qb instanceof \Doctrine\ODM\MongoDB\Query\Builder) {
            /* @var $qb \Doctrine\ODM\MongoDB\Query\Builder */
            $qb->limit(20);
            $driver = 'odm';
        } else {
            throw new \Exception('Can\'t find ORM or ODM doctrine driver');
        }
        
        if(key_exists('property_key', $optionAutocomplete)){
            $qb->select("q.{$optionAutocomplete['property_key']}");
        }
        
        if(key_exists('property', $optionAutocomplete)){
            $qb->addSelect("q.{$optionAutocomplete['property']}");
        }
        
        foreach ($optionAutocomplete['searchFields'] as $field) {
            if ($driver == 'orm') {
                $qb->orWhere($qb->expr()->like('q.' . $field, $qb->expr()->literal("%{$term}%")));
                 
            } elseif ($driver == 'odm') {
                $qb->addOr($qb->expr()->field($field)->equals(new \MongoRegex("/{$term}/i")));
            }
        }
        if ($optionAutocomplete['orderBy']) {
            if ($driver == 'orm') {
                $qb->orderBy('q.' . $optionAutocomplete['orderBy'][0], $optionAutocomplete['orderBy'][1]);
            } elseif ($driver == 'odm') {
                $qb->sort($optionAutocomplete['orderBy'][0], $optionAutocomplete['orderBy'][1]);
            }
        }
        
        //echo $qb->getQuery()->getSql();
        
        
        foreach ($qb->getQuery()->getArrayResult() as $key => $row){
            //var_dump($row);
            $records[] = array ("id"=> $row[$optionAutocomplete['property_key']], "text"=>(string) $row[$optionAutocomplete['property']]);
        }
        
         
        $view = new JsonModel($records);
        return $view;
        
        
    }
    
    
    public function searchtablegatewayselectAction() {
        $tableGateway       = str_replace('-', '\\',$this->params()->fromRoute('element'));
        $classMethod        = $this->params()->fromRoute('tag');
        $propertyName       = $this->params()->fromRoute('property');   
        $term               = $this->params()->fromQuery('q', '');
        $sm                 =  $this->getServiceLocator();
        $em                 = $sm->get('doctrine.entitymanager.orm_default');
        $element = new \CostFormElement\Form\Element\SelectAutocompleteTableGateway();
        $records            = array();
        
        $this->tableService = $this->getServiceLocator()->get('table-gateway');
        $oTableGateway      = $this->tableService->get($tableGateway);
        
        $where = $term;
        if($oTableGateway instanceof \Laminas\Db\TableGateway\AbstractTableGateway){
            if(method_exists($oTableGateway, $classMethod)){
                $records  = $oTableGateway->$classMethod($where);
            }
            else{
                throw new \Exception('Class TableGateway must implements $classMethod method');
            }
        }
        else{
            throw new \Exception('You must supply class tahet ineherit tablegateway');
        }
        
        $view = new JsonModel($records);
        return $view;
    }
    
    
    public function searchtablegatewayonewayselectAction() {

        $tableGateway       = str_replace('-', '\\',$this->params()->fromRoute('element'));
        $classMethod        = $this->params()->fromRoute('tag');
        $propertyName       = $this->params()->fromRoute('property');
        $term               = $this->params()->fromQuery('q', '');
        $sm                 = $this->getServiceLocator();

        $element = new \CostFormElement\Form\Element\SelectAutocompleteTableGatewayOneWay();
        $records            = array();
    
        $this->tableService = $this->getServiceLocator()->get('table-gateway');
        $oTableGateway      = $this->tableService->get($tableGateway);

        $where = $term;
        if($oTableGateway instanceof \Laminas\Db\TableGateway\AbstractTableGateway){
            if(method_exists($oTableGateway, $classMethod)){
                $records  = $oTableGateway->$classMethod($where);
            }
            else{
                throw new \Exception('Class TableGateway must implements $classMethod method');
            }
        }
        else{
            throw new \Exception('You must supply class tahet ineherit tablegateway');
        }
    
        $view = new JsonModel($records);
        return $view;
    }


    public function searchautocompleteAction() {
        $elementName = $this->params()->fromRoute('element');
        $elementName = str_replace('-', '\\', $elementName);
        $em   =  $sm->get('doctrine.entitymanager.orm_default');

        $term = $this->params()->fromQuery('term', '');

        $factory = new Factory();
        $element = $factory->createElement(array(
            'type' => $elementName,
            'options' => array(
                'sm' => $this->getServiceLocator()
            )
        ));
        $options = $element->getOptions();
        $this->setOm($em);
        $proxy = $element->getProxy();
        $this->setProxy($proxy);
        $this->setOptions($options);

        $qb = $proxy->getObjectManager()->getRepository($proxy->getTargetClass())
            ->createQueryBuilder('q');
        $driver = '';
        if (class_exists("\Doctrine\ORM\QueryBuilder") && $qb instanceof \Doctrine\ORM\QueryBuilder) {
            /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb->setMaxResults(20);
            $driver = 'orm';
        } elseif (class_exists("\Doctrine\ODM\MongoDB\Query\Builder") && $qb instanceof \Doctrine\ODM\MongoDB\Query\Builder) {
            /* @var $qb \Doctrine\ODM\MongoDB\Query\Builder */
            $qb->limit(20);
            $driver = 'odm';
        } else {
            throw new \Exception('Can\'t find ORM or ODM doctrine driver');
        }

        foreach ($options['searchFields'] as $field) {
            if ($driver == 'orm') {
                $qb->orWhere($qb->expr()->like('q.' . $field, $qb->expr()->literal("%{$term}%")));
            } elseif ($driver == 'odm') {
                $qb->addOr($qb->expr()->field($field)->equals(new \MongoRegex("/{$term}/i")));
            }
        }
        if ($options['orderBy']) {
            if ($driver == 'orm') {
                $qb->orderBy('q.' . $options['orderBy'][0], $options['orderBy'][1]);
            } elseif ($driver == 'odm') {
                $qb->sort($options['orderBy'][0], $options['orderBy'][1]);
            }
        }
        $this->setObjects($qb->getQuery()->execute());
        $valueOptions = $this->getValueOptions();

        $view = new JsonModel($valueOptions);
        return $view;
    }



    public function searchAction() {

        $className    = str_replace('-', '\\',$this->params()->fromRoute('element'));  
        $classTag    = $this->params()->fromRoute('tag');
        $propertyName   = $this->params()->fromRoute('property');
        $term = $this->params()->fromQuery('term', '');
        $sm   =  $this->getServiceLocator();
        $em   =  $sm->get('doctrine.entitymanager.orm_default');


       
        
        $element = new \CostFormElement\Form\Element\ObjectAutocomplete();
        $element->setOption('sm', $this->getServiceLocator());
        
        $options = $element->getOptions();
        $options['object_manager']      =  $em;
        $proxy = $element->getProxy();
        
        $this->setOm( $em );
        $this->setProxy($proxy); 
        $this->setOptions($options);
        
        //echo $className.'-'.$classTag.'-'.$propertyName;
        //die();
        
        /** call factory form **/
        if($sm->has($className)){
            if($sm->get($className) instanceof  \Laminas\Form\Form){

                if($sm->get($className)->has($classTag)){
                    $elementName = $sm->get($className)->get($classTag);
                    $optionAutocomplete = $elementName->getOptions();
                }
            }
        }
        else{ // from annotation 
            $entity         = $className;
            $elementName    = $classTag;
            $docReader      = new AnnotationReader();
            $reflect        = new \ReflectionClass($entity);
            $fields         = array($propertyName => $propertyName);

            foreach ($fields as $key => $val)
            {
                if (!$reflect->hasProperty($key)) {
                    var_dump('the entity does not have a such property');
                    continue;
                }
                $docInfos = $docReader->getPropertyAnnotations($reflect->getProperty($key));
                foreach ($docInfos as $docInfo){
                    if($docInfo instanceof \Laminas\Form\Annotation\Options){
                        $optionAutocomplete = $docInfo->getOptions();
                    }
                 }
            }
        }
        
        $targetClass = $optionAutocomplete['target_class'];
        
        
        // to do define adapter (doctrine , Laminas/sql Laminas/tablegateway
        $qb = $em->getRepository($targetClass)->createQueryBuilder('q');
        
        
        $driver = '';
        if (class_exists("\Doctrine\ORM\QueryBuilder") && $qb instanceof \Doctrine\ORM\QueryBuilder) {
            /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb->setMaxResults(20);
            $driver = 'orm';
        } elseif (class_exists("\Doctrine\ODM\MongoDB\Query\Builder") && $qb instanceof \Doctrine\ODM\MongoDB\Query\Builder) {
            /* @var $qb \Doctrine\ODM\MongoDB\Query\Builder */
            $qb->limit(20);
            $driver = 'odm';
        } else {
            throw new \Exception('Can\'t find ORM or ODM doctrine driver');
        }
        
        foreach ($optionAutocomplete['searchFields'] as $field) {
                    $qb->select("q.$field");
        }

        foreach ($optionAutocomplete['searchFields'] as $field) {
            if ($driver == 'orm') {
                $qb->orWhere($qb->expr()->like('q.' . $field, $qb->expr()->literal("%{$term}%")));
               
            } elseif ($driver == 'odm') {
                $qb->addOr($qb->expr()->field($field)->equals(new \MongoRegex("/{$term}/i")));
            }
        }
        if ($optionAutocomplete['orderBy']) {
            if ($driver == 'orm') {
                $qb->orderBy('q.' . $optionAutocomplete['orderBy'][0], $optionAutocomplete['orderBy'][1]);
            } elseif ($driver == 'odm') {
                $qb->sort($optionAutocomplete['orderBy'][0], $optionAutocomplete['orderBy'][1]);
            }
        }
        
        foreach ($qb->getQuery()->getArrayResult() as $key => $row){
            $records[] = (string) $row[$field];
        }
        
       
        $view = new JsonModel($records);
        return $view;
    }

    private function getValueOptions() {
        $proxy = $this->getProxy();
        $targetClass = $proxy->getTargetClass();
        $metadata = $this->getOm()->getClassMetadata($targetClass);
        $identifier = $metadata->getIdentifierFieldNames();
        $objects = $this->getObjects();
        $options = array();

        if ($proxy->getDisplayEmptyItem() || empty($objects)) {
            $options[] = array('value' => null, 'label' => $proxy->getEmptyItemLabel());
        }

        if (!empty($objects)) {
            $entityOptions = $this->getOptions();
            foreach ($objects as $key => $object) {
                if (isset($entityOptions['label_generator']) && is_callable($entityOptions['label_generator']) && null !== ($generatedLabel = call_user_func($entityOptions['label_generator'], $object))) {
                    $label = $generatedLabel;
                } elseif ($property = $proxy->getProperty()) {
                    if ($proxy->getIsMethod() == false && !$metadata->hasField($property)) {
                        throw new RuntimeException(
                        sprintf(
                                'Property "%s" could not be found in object "%s"', $property, $targetClass
                        )
                        );
                    }

                    $getter = 'get' . ucfirst($property);
                    if (!is_callable(array($object, $getter))) {
                        throw new RuntimeException(
                        sprintf('Method "%s::%s" is not callable', $proxy->getTargetClass(), $getter)
                        );
                    }

                    $label = $object->{$getter}();
                } else {
                    if (!is_callable(array($object, '__toString'))) {
                        throw new RuntimeException(
                        sprintf(
                                '%s must have a "__toString()" method defined if you have not set a property'
                                . ' or method to use.', $targetClass
                        )
                        );
                    }

                    $label = (string) $object;
                }

                if (count($identifier) > 1) {
                    $value = $key;
                } else {
                    $value = current($metadata->getIdentifierValues($object));
                }

                $options[] = array('label' => $label, 'value' => $value);
            }
        }

        return $options;
    }

    public function getProxy() {
        return $this->proxy;
    }

    public function getObjects() {
        return $this->objects;
    }

    public function setProxy($proxy) {
        $this->proxy = $proxy;
        return $this;
    }

    public function setObjects($objects) {
        $this->objects = $objects;
        return $this;
    }

    public function getOm() {
        return $this->om;
    }

    public function setOm($om) {
        $this->om = $om;
        return $this;
    }

    public function getOptions() {
        return $this->options;
    }

    public function setOptions($options) {
        $this->options = $options;
        return $this;
    }

}
