# SortableUibundle
It's totally rework for a better gui.

The original 'bundle' provided by : https://github.com/pix-digital/pixSortableBehaviorBundle

The original 'cookbook' proived by : https://github.com/sonata-project/SonataAdminBundle/blob/master/Resources/doc/cookbook/recipe_sortable_listing.rst


In the future features :
- multi level drag and drop
- improve performance code
- comment the code

-
 Configuration
-

```
# app/config/config.yml
aliocza_sortable_ui:
    db_driver: orm # default value : orm (orm is only supported)
    position_field:
        default: sort #default value : position
        entities:
            AppBundle/Entity/Foobar: order
            AppBundle/Entity/Baz: rang
```

# Cookbook for Sonata Admin

Pre-requisites
--------------
- you already have SonataAdmin and DoctrineORM up and running
- you already have an Entity class for which you want to implement a sortable feature. For the purpose of the example we are going to call it ``Client``.
- you already have an Admin set up, in this example we will call it ``ClientAdmin``

-
 Bundles
-
- install ``gedmo/doctrine-extensions`` bundle in your project (check ``stof/doctrine-extensions-bundle`` for easier integration in your project) and enable the sortable feature in your config. 
For how to install bundle : http://symfony.com/doc/current/bundles/StofDoctrineExtensionsBundle/index.html
- install ``aliocza/sortable-ui-bundle`` in your project

The recipe
----------

First of all we are going to add a position field in our ``Client`` entity.
```
    use Gedmo\Mapping\Annotation as Gedmo;
    // ...
    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;
```

Then we need to inject the Sortable listener. If you only have the Gedmo bundle enabled, you only have to add the listener to your config.yml and skip this step.

```
services:
    gedmo.listener.sortable:
        class: Gedmo\Sortable\SortableListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
        calls:
            - [ setAnnotationReader, [ "@annotation_reader" ] ]
```

In our ``ClientAdmin`` we are going to add a custom action in the ``configureListFields`` method
and use the default twig template provided in the ``alioczaSortableUiBundle``

```
	$listMapper
	    ->add('_action', 'actions', array(
            'actions' => array(
                'drag' => array(
                            'template' => 'AlioczaSortableUiBundle:Default:drag.html.twig'
                ),
            )
        )
    );
```

In order to add new routes for these actions we are also adding the following method and we override the template for add button

```
    <?php
    // src/AppBundle/Admin/ClientAdmin.php

    namespace AppBundle/Admin;

    use Sonata\AdminBundle\Route\RouteCollection;
    // ...
    private $positionService;
    
    protected function configureRoutes(RouteCollection $collection) {
    // ...
        $collection->add('drag', 'drag');
    }
    
    public function configure() {
        $this->setTemplate('list', 'AlioczaSortableUiBundle:CRUD:base_list.html.twig');
    }

    public function setPositionService(\Aliocza\SortableUiBundle\Services\PositionHandler $positionHandler) {
        $this->positionService = $positionHandler;
    }
    
```

Now you can update your ``services.yml`` to use the handler provider by the ``alioczaSortableUiBundle``

```
	services:
	    app.admin.client:
	        class: AppBundle\Admin\ClientAdmin
	        tags:
	            - { name: sonata.admin, manager_type: orm, label: "Clients" }
	        arguments:
	            - ~
	            - AppBundle\Entity\Client
	            - 'AlioczaSortableUiBundle:SortableUiAdmin' # define the new controller via the third argument
	        calls:
	            - [ setTranslationDomain, [AppBundle]]
	            - [ setPositionService, ['@aliocza_sortable_ui.position']]
```
