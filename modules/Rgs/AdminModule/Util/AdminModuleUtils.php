<?php

namespace Rgs\AdminModule\Util;

use Novice\Module\SmartyBootstrapModule\Util\ItemProperty;

class AdminModuleUtils {

    const ASSET_OK = '/img/pictos/Ok-16.png';

    const ASSET_CANCEL = '/img/pictos/Cancel_2-16.png';

    private function getPublishedColumn(){
        return [
				'property' => 'published',
				'label' => 'Status',
				'filter' => function($propertyValue, $entity, $i, $smarty){
					if($entity->isPublished()){
						$publishValue='unpublish';
						$src = $smarty->getAssets()->getUrl(self::ASSET_OK, null);
					}
					else{
						$publishValue='publish';
						$src = $smarty->getAssets()->getUrl(self::ASSET_CANCEL, null);
					}

					$result = '';
					$result .= '<input type="image"';
					$result .= ' src="'.$src.'"';
					$result .= ' class="btn btn-outline btn-default" name="submit[]"';
					$result .= ' data-check-id="cb'.$i.'"';
					$result .= ' value="'.$publishValue.'" />';

					return $result;
				}
			];
    }

    public function getArticlesColumns(){
        return array(
			$this->getPublishedColumn(),
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_articles_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			],
			[
				'property' => 'category.name',
				'label' => 'Category',
				'class' => 'hidden-xs',
				'route' => [
					'id' => 'rgs_admin_categories_edit',
					'params' =>[
						'id' => new ItemProperty('category.id'), 
						'slug' => new ItemProperty('category.slug')
					],
					'absolute' => true
				]
			],
			[
				'property' => 'state.name',
				'label' => 'State',
				'class' => 'hidden-xs hidden-sm',
				'route' => [
					'id' => 'rgs_admin_states_edit',
					'params' =>[
						'id' => new ItemProperty('state.id'), 
						'slug' => new ItemProperty('state.slug')
					],
					'absolute' => true
				]
			],
			[
				'property' => 'brand.name',
				'fallbackView' => '-',
				'label' => 'Brand',
				'class' => 'hidden-xs hidden-sm hidden-md',
				'route' => [
					'id' => 'rgs_admin_brands_edit',
					'params' =>[
						'id' => new ItemProperty('brand.id'), 
						'slug' => new ItemProperty('brand.slug')
					],
					'absolute' => true
				]
			]
		);
    }

	public function getAdvertisementsColumns(){
        return array(
			$this->getPublishedColumn(),
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_advertisements_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			]
		);
    }

    public function getCategoriesColumns(){
        return array(
			$this->getPublishedColumn(),
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_categories_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			]
		);
    }

    public function getStatesColumns(){
        return array(
			$this->getPublishedColumn(),
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_states_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			]
		);
    }

    public function getBrandsColumns(){
        return array(
			$this->getPublishedColumn(),
			[
				'property' => 'name',
				'label' => 'Title',
				'route' => [
					'id' => 'rgs_admin_brands_edit',
					'params' =>[
						'id' => new ItemProperty('id'), 
						'slug' => new ItemProperty('slug')
					],
					'absolute' => true
				]
			]
		);
    }

}