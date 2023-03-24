<?php
namespace CostFormElement\Form\Element;

use RuntimeException;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Select;
use DoctrineModule\Form\Element\Proxy;
use DoctrineModule\Form\Element\ObjectSelect;

class SelectAutocompleteTableGateway extends Select
{
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
    
    public function setValue($value)
    {
       if($this->getAttribute('data-is_orm_object')){
            $getter     = 'get' . ucfirst($this->getAttribute('data-property'));
            $method     = $this->getAttribute('data-repo-method-class');
            if(is_object($value)){
                    $propertyId =  $value->$getter();
                    $em = $this->getProxy()->getObjectManager();
                    $result = $em->getRepository( $this->getAttribute('data-repo-target-class'))->$method($propertyId);
                    $this->setValueOptions($result);
            }
        }
            
        return parent::setValue($value);
        
    }
    
    /**
     *
     * @param array|\Traversable $options
     * @return ObjectSelect
     */
    public function setOptions($options)
    {
        if (! $this->initialized) {
            $this->setAttribute('data-cost-tablegatwayacclass', urlencode(str_replace('\\', '-', $options['tablegateway_class'])));
            $this->setAttribute('data-cost-tablegatwayacproperty', $options['tablegateway_method']);
            $this->setAttribute('data-zf2tagclass', $options['tablegateway_searchfield']);
            $this->setAttribute('data-cost-tablegatwayacselectwarningmessage', $options['select_warning_message']);
            $this->setAttribute('data-cost-tablegatwayacinit', 'cost-tablegatway-combo-autocomplete');
            
            
            $this->setAttribute('data-is_orm_object', $options['is_orm_object']);
            $this->setAttribute('data-target-class',  $options['target_class']);
            $this->setAttribute('data-property',  $options['property']);
            $this->setAttribute('data-repo-target-class',  $options['repo_target_class']);
            $this->setAttribute('data-repo-method-class',  $options['repo_method_class']);
            
            
            
            if (isset($options['allow_persist_new']) && $options['allow_persist_new']) {
                $this->setAttribute('data-cost-tablegatwayacallowpersist', 'true');
            }
            $this->initialized = true;
        }
        //$this->getProxy()->setOptions($options);
        //$this->setValueOptions(array("1"=>"VISA", "2"=>"MASTER CARD", "3"=>"AMERICAN EXPRESS"));
    
    
         
        return parent::setOptions($options);
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
    
    
    public function setOption($key, $value)
    {}
}

?>