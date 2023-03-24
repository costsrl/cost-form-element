CostFormElement
=======

**What is CostFormElement?**


Sotto public/js e public/css  copiare i file presenti nella cartella assets

includere nel Layout.phtml i file js e css di select2-bootstrap

e Cost-*.js



  -------------- SelectAutocompleteTableGateway ------------------
  
  1) tablegateway_class = tablegateway chimato dal controller del modulo Cost-form-element
  2) tablegateway_method= metodo chimata
  3) tablegateway_searchfield = campo di filtro
  
  4)repo_target_class =classe doctrine in caso di idratazione
  5)repo_method_class = metodo di chimata per estrarre il dato
  6)collaboratore_id  = proprieta che vine chiamata per estrarre il valore
 /**
     *   private $coach;
     * 
     * @ORM\ManyToOne(targetEntity="Collaboratore\Entity\Collaboratore")
     * @ORM\JoinColumn(name="generalmanager", referencedColumnName="collaboratore_id")
     * @Annotation\Type("\CostFormElement\Form\Element\SelectAutocompleteTableGateway")
     * @Annotation\Options({
     * "label":"Generl Manager:",
     * "empty_option": "Seleziona  Gm",
     * "tablegateway_searchfield":"cognome",
     * "tablegateway_class":"collaboratore",
     * "tablegateway_method": "fetchGM",
     * "is_orm_object":1,
     * "target_class":"Collaboratore\Entity\Collaboratore",
     * "repo_target_class":"Collaboratore\Entity\Collaboratore",
     * "repo_method_class":"getOneGmById",
     * "property": "collaboratore_id"})
     * @Annotation\Attributes({"class":"form-control"})
     */
    
    


	---------------------  SelectAutocomplete ---------------------------
	1) target_class 	= 'classe entity che verra chimata'
	2) property     	= volore combo campo estratto
	3) property_key     = chiave combo campo estratto
	4) searchFields 	= campo di ricerca
	5) orm_tag			= identificatvio dell'oggetto
	
	/**
     * @ORM\ManyToOne(targetEntity="Collaboratore\Entity\Collaboratore")
     * @ORM\JoinColumn(name="segnalatore_id", referencedColumnName="collaboratore_id")
     * @Annotation\Attributes({"id":"segnalatoreid"});
     * @Annotation\Type("\CostFormElement\Form\Element\SelectAutocomplete")
     * @Annotation\Options({
     * "label":"Segnalatore:",
     * "empty_option": "Seleziona Segnalatore",
     * "searchFields":{"codice"},
     * "orm_tag":"segnalatoreid",
     * "class":"Commessa\Entity\Commessa", 
     * "property_key": "collaboratore_id",
     * "property": "codice"})
     
     
     
     ----------------------------  SelectAutocompleteTableGatewayOneWay -----------------
     
     /**
     * @ORM\Column(type="string", nullable=true, name="codicecomunenascita")
     * @Annotation\Type("\CostFormElement\Form\Element\SelectAutocompleteTableGatewayOneWay")
     * @Annotation\Options({
     * "label":"Selezione Comune:",
     * "empty_option": "Seleziona Comune",
     * "tablegateway_searchfield":"codice",
     * "tablegateway_class":"comune",  
     * "tablegateway_method": "fetchComune",
     * "tablegateway_method_backend": "fetchValue",
     * "property": "codice"})
     * @Annotation\Attributes({"options":{}, "id":"codicecomunenascita"})
     * 
     *
     */
    private $codicecomunenascita;
    
    
    class comune extends BasicTableGateway  metodi 
    
    public function fetchComune($term){ 
        $sql= new \Laminas\Db\Sql\Sql($this->getAdapter());
        $select = $sql->select()->columns(array('codice','comune'))->from($this->table);
        if($term)
           $select->where->like(new \Laminas\Db\Sql\Expression('LOWER(comune)'), "$term%");
        
       $select->where->isNotNull('comune')->and->notEqualTo('comune', '');
       $select->order('codice ASC')->limit(20);
       //echo $this->sql->getSqlStringForSqlObject($select);      
       $result = $this->selectWith($select)->toArray();
       $rs=$this->selectWith($select)->toArray();
            if($flag) $return['']= '';
            foreach ($rs as $row) {
                $row = array_values($row);              
                $records[] = array ("id"=> $row[0], "text"=>trim($row[1]));
            }
        
            return $records;
        return $select;
    }
    
    
    public function fetchValue($term){
        $sql= new \Laminas\Db\Sql\Sql($this->getAdapter());
        $select = $sql->select()->columns(array('codice','comune'))->from($this->table);
        if($term)
            $select->where->equalTo(new \Laminas\Db\Sql\Expression('codice'), "$term");
            $result = $this->selectWith($select)->toArray();
            $rs=$this->selectWith($select)->toArray();
            foreach ($rs as $row) {
                $row = array_values($row);
                $records[$row[0]] = $row[1];
            }
           return $records;
    }
    ------------------------------------------------------------------------------------------------