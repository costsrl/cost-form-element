<?php
namespace CostFormElement\Form\Element;

use RuntimeException;
use Laminas\Form\Element\Text;
use DoctrineModule\Form\Element\Proxy;
use Laminas\Form\Element\Select;
use DoctrineModule\Form\Element\ObjectSelect;

class SelectAutocomplete extends ObjectSelect
{

    protected $proxy;

    private $initialized = false;

    protected function getValidator()
    {
        if (null === $this->validator && ! $this->disableInArrayValidator()) {
            if ($this->isMultiple()) {
                $validator = new ExplodeValidator([
                    'validator' => $validator,
                    'valueDelimiter' => null
                ] // skip explode if only one value
);
            }
            
            $this->validator = $validator;
        }
        return $this->validator;
    }

    /**
     *
     * @param mixed $value            
     * @return void
     * @throws \RuntimeException
     */
    public function setValue($value)
    {
        $multiple = $this->getAttribute('multiple');
         
        
        if(!is_object($value)){
            if(is_array($value) && array_key_exists('id', $value)){
                $this->setAttribute('data-zf2doctrineacid', $value['id']);
                return parent::setValue($value[$this->getProxy()->getProperty()]);
            }
            else{
            $this->setAttribute('data-zf2doctrineacid', $value);
            return parent::setValue($value);
            }
        }
        $id = $this->getProxy()->getValue($value);
        
        
        
        $this->setAttribute('data-zf2doctrineacid', $id);
        $metadata   = $this->getProxy()->getObjectManager()
                ->getClassMetadata($this->getProxy()->getTargetClass());
        $identifier = $metadata->getIdentifierFieldNames();
        $object = $this->getProxy()->getObjectManager()
                        ->getRepository($this->getProxy()->getTargetClass())->find($id);
        
                        
        //var_dump($object);
        $identifier = $metadata->getSingleIdentifierFieldName();
        //var_dump($identifier);
        
                      
        if (
                is_callable($this->getOption('label_generator')) 
                && null !== ($generatedLabel = call_user_func($this->getOption('label_generator'), $object))
                ) {
            $label = $generatedLabel;
        } elseif ($property = $this->getProxy()->getProperty()) {
            if ($this->getProxy()->getIsMethod() == false && !$metadata->hasField($property)) {
                        throw new RuntimeException(
                            sprintf(
                                'Property "%s" could not be found in object "%s"',
                                $property,
                                $targetClass
                            )
                        );
                    }
            $getter     = 'get' . ucfirst($property);
            $getterId   = 'get' . ucfirst($identifier);
            //var_dump(get_class($object));
            //var_dump(get_class_methods($object));
            if (!is_callable(array($object, $getter))) {
                        throw new RuntimeException(
                            sprintf('Method "%s::%s" is not callable', $this->getProxy()->getTargetClass(), $getter)
                        );
                    }
            $label = $object->{$getter}();
            $id    = $object->{$getterId}();
        } else {
            if (!is_callable(array($object, '__toString'))) {
                throw new RuntimeException(
                sprintf(
                        '%s must have a "__toString()" method defined if you have not set a property'
                        . ' or method to use.', $this->getProxy()->getTargetClass()
                )
                );
            }
            $label = (string) $object;
        }
       
        // overwite 
        $this->setValueOptions([$id => $label]);
        
        $multiple = $this->getAttribute('multiple');

        if (true === $multiple || 'multiple' === $multiple) {
            if ($value instanceof \Traversable) {
                $value = ArrayUtils::iteratorToArray($value);
            } elseif ($value == null) {
                return parent::setValue(array());
            } elseif (!is_array($value)) {
                $value = (array) $value;
            }

            return parent::setValue(array_map(array($this->getProxy(), 'getValue'), $value));
        }

        return parent::setValue($id);
         
        
    }

    /**
     *
     * @return Proxy
     */
    public function getProxy()
    {
        if (null === $this->proxy) {
            $this->proxy = new Proxy();
        }
        return $this->proxy;
    }

    /**
     *
     * @param array|\Traversable $options            
     * @return ObjectSelect
     */
    public function setOptions($options)
    {
        if (! $this->initialized) {
            $this->setAttribute('data-Costdoctrineacclass', urlencode(str_replace('\\', '-', $options['class'])));
            $this->setAttribute('data-Costdoctrineacproperty', $options['property']);
            $this->setAttribute('data-zf2tagclass', $options['orm_tag']);
            $this->setAttribute('data-Costdoctrineacselectwarningmessage', $options['select_warning_message']);
            $this->setAttribute('data-Costdoctrineacinit', 'Cost-doctrine-combo-autocomplete');
            if (isset($options['allow_persist_new']) && $options['allow_persist_new']) {
                $this->setAttribute('data-Costdoctrineacallowpersist', 'true');
            }
            $this->initialized = true;
        }
        $this->getProxy()->setOptions($options);
        //$this->setValueOptions(array("1"=>"VISA", "2"=>"MASTER CARD", "3"=>"AMERICAN EXPRESS"));
        
        
         
        return parent::setOptions($options);
    }

    public function setOption($key, $value)
    {}
}

?>