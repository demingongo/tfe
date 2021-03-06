<?php

namespace Rgs\AdminModule\Util;

class ArticlesManager extends ContentManager {

    private $utils;

    public function __construct($container){
        parent::__construct($container);
        $this->utils = new AdminModuleUtils();
    }

    public function getTitle(){
        return 'Articles';
    }

    public function getDefaultOrder(){
      return  'a.name ASC';
    }

    public function getOrderOptions(){
        return array( 
				"a.name ASC" => "Title ascending",
				"a.name DESC" => "Title descending",
				"a.id ASC" => "Id ascending",
				"a.id DESC" => "Id descending",
			);
    }

    public function getVisibilityKey(){
        return 'a.published';
    }

    public function getEntityName(){
        return 'RgsCatalogModule:Article';
    }

    public function getAlias(){
        return 'a';
    }

    public function getAddRouteId(){
        return 'rgs_admin_articles_add';
    }

    public function getEditRouteId(){
        return 'rgs_admin_articles_edit_2';
    }

    public function getColumns(){
        return $this->utils->getArticlesColumns();
    }

    public function getCustomFields(){
        $customFields = parent::getCustomFields();

        $formFieldExtension =  new \Novice\Form\Extension\Entity\EntityExtension($this->container->get('managers'), array(
		'class' => 'RgsCatalogModule:Category',
		'choice_label' => function($cat){return $cat->getName();},
		'query_builder' => function ($er) {
				return $er->createQueryBuilder('c')
					->orderBy('c.name', 'ASC');
		},
        'name' => 'category',
		'feedback' => false,
		'attributes' => array(
			'style' => 'width: 99%',
			'data-placeholder' => 'All Categories',
			'data-allow-clear' => 'true',
			'data-minimum-results-for-search' => 15,
			'class' => 'select2',
			'onchange' => 'this.form.submit()',
			),
		));

		$customFields = ['categories' => $formFieldExtension->createField()] + $customFields;
        return $customFields;
    }

    public function processCustomFields($request, array $where, $customFields){
        $where = parent::processCustomFields($request, $where, $customFields);
        $byCategory = null;
        if($request->request->has('category')){
			$req_byCategory = $request->request->get('category');
			if(!empty($req_byCategory)){
				$byCategory = $req_byCategory;
				$where['a.category'] = $byCategory;
			}
		}
        $customFields['categories']->setValue($byCategory);
        return $where;
    }
}