<?php
namespace CostFormElement;
use CostFormElement\Controller\Factory\SearchControllerFactory;
use CostFormElement\Controller\SearchController;

return array(
    'form_elements' => array(
        'factories' => array(
            'CostFormElement\Form\Element\ObjectAutocomplete'                      => 'CostFormElement\Form\Element\ObjectAutocomplete',
            'CostFormElement\Form\Element\SelectAutocomplete'                      => 'CostFormElement\Form\Element\SelectAutocomplete',
            'CostFormElement\Form\Element\SelectAutocompleteTableGateway'          => 'CostFormElement\Form\Element\SelectAutocompleteTableGateway',
            'CostFormElement\Form\Element\SelectAutocompleteTableGatewayOneWay'    => 'CostFormElement\Form\Element\SelectAutocompleteTableGatewayOneWay',
        ),
    ),
    'router' => array(
        'routes' => array(
            'cost-doctrine-autocomplete' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cost-doctrine-autocomplete[/:element][/:tag][/:property][/]',
                    'defaults' => array(
                        'action' => 'searchautocomplete',
                        'controller' => SearchController::class,
                        'tag'      => '',
                        'property' => ''
                    )
                )
            ),
            'cost-doctrine-select-autocomplete' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cost-doctrine-select-autocomplete[/:element][/:tag][/:property]',
                    'defaults' => array(
                        'action' => 'searchselect',
                        'controller' => SearchController::class,
                        'tag'      => '',
                        'property' => ''
                    )
                )
            ),
            'cost-tablegateway-select-autocomplete' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cost-tablegateway-select-autocomplete[/:element][/:tag][/:property]',
                    'defaults' => array(
                        'action' => 'searchtablegatewayselect',
                        'controller' => SearchController::class,
                        'tag'      => '',
                        'property' => ''
                    )
                )
            ),
            'cost-tablegateway-oneway-select-autocomplete' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cost-tablegateway-oneway-select-autocomplete[/:element][/:tag][/:property]',
                    'defaults' => array(
                        'action' => 'searchtablegatewayonewayselect',
                        'controller' => SearchController::class,
                        'tag'      => '',
                        'property' => ''
                    )
                )
            ),
        )
    ),
    'controllers' => array(
        'invokables' => [
            //'SearchAutoComplete' =>'CostFormElement\Controller\SearchController'
        ],
        'factories'=>[
            SearchController::class =>SearchControllerFactory::class
        ]
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy'
        )
    ),
);
