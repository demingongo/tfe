<?php

namespace Rgs\CatalogModule\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;

use Rgs\CatalogModule\Entity\Category;

use Doctrine\NestedSetModule\Services\NestedSetManagerCreator;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
	public function publish(array $ids, $publish = Category::PUBLISHED){
		foreach($ids as $id){
			$this->publishOneCategory($id, $publish);
		}
		//exit(__METHOD__);
	}

	public function publishOneCategory($id, $publish = Category::PUBLISHED){
		$qb = $this->createQueryBuilder('c');

		return $qb->update('RgsCatalogModule:Category', 'c')
			->set('c.published', ':published')
			->where('c.id = :id')
			->setParameter('published', $publish)
			->setParameter('id', $id)
			->getQuery()
			->execute();
	}

	public function getRootCategories($public = null)
	{
		$qb = $this->createQueryBuilder('c');
		
		$qb->where('c.root = c.id')
			->orderBy('c.name');

		if($public !== null){
			$qb->andWhere($qb->expr()->eq('c.published', ':published'))
			   ->setParameter('published', (bool)$public);
		}

		return $qb->getQuery()->getResult();
	}

	public function getArrayForSelectField(NestedSetManagerCreator $nsmc, $public = null, $id = null)
	{
		$retour = array();

		$nsm = $nsmc->getManager('RgsCatalogModule:Category');

		$categories = $this->getRootCategories($public);

		$space = '';
		$i = 0;
		for($i; $i < count($categories); $i++){
			$c = $categories[$i];
			if($id != null && $c->getId() == $id)
				continue;

			$node = $nsm->wrapNode($c);
			if($node->isRoot()){
				$space = '';
			}
			
			$retour[$c->getId()] = $space.$c->getName();
			
			if($node->hasChildren()){
				$space = "&nbsp;&nbsp;&nbsp;".$space."|_ ";
				$j = 0;
				$array = array();
				foreach($node->getChildren() as $ch)
				{
					$array[] = $ch->getNode();
				}
				array_splice($categories, $i+1, 0, $array);
			}
			else if(!$node->hasNextSibling()){
				if(!empty($space)){
					$space = substr($space, 18, -3);
				}
			}
		}
		return $retour;
	}

	public function deleteOneById(NestedSetManagerCreator $nsmc, $id)
	{
		$category = $this->findOneById($id);
		if($category !== null){
			$nsm = $nsmc->getManager('RgsCatalogModule:Category');
			$node = $nsm->wrapNode($category);
			return $node->delete();
		}
	}

	public function deleteByIds(NestedSetManagerCreator $nsmc, array $ids)
	{
		foreach($ids as $id){
			$this->deleteOneById($nsmc, $ids);
		}
	}

	public function getCategories($limit = 20, $page = 1, $orderBy = 'c.name', $ascending = 'ASC', $published = null)
	{
		$qb = $this->createQueryBuilder('c');

		if($published !== null){
			$qb->andWhere($qb->expr()->eq('c.published', ':published'))
				->setParameter('published', (bool)$published);
		}
		
		$qb	->orderBy($orderBy, $ascending)
			->setFirstResult(($page-1) * $limit)
			->setMaxResults($limit);

		return new Paginator($qb);
	}

	public function findCategories($limit = 20, $page = 1, $where = array(), $orderBy = array())
	{
		$qb = $this->createQueryBuilder('c');
		
		$i = 1;
		foreach($where as $k => $v){
			$qb->andWhere($qb->expr()->eq($k, '?'.$i))
				->setParameter($i, $v);
			$i++;
		}

		if(empty($orderBy))
			$orderBy = array('c.name' => 'ASC');

		foreach($orderBy as $k => $v){
			$qb	->addOrderBy($k, $v);
		}
		
		$qb ->setFirstResult(($page-1) * $limit)
			->setMaxResults($limit);

		return new Paginator($qb);
	}

	public function countCategories($where = array())
	{
		$qb = $this->createQueryBuilder('c');

		$qb->select('count(c.id)');

		$i = 1;
		foreach($where as $k => $v){
			$qb->andWhere($qb->expr()->eq($k, '?'.$i))
				->setParameter($i, $v);
			$i++;
		}

		return $qb->getQuery()->getSingleScalarResult();
	}

	public function getSQLFrontCategories($rsm){
		if(isset($rsm) && $rsm instanceof \Doctrine\ORM\Query\ResultSetMappingBuilder){
			$rsm->addRootEntityFromClassMetadata($this->getEntityName(), 'category');
		}
		return "SELECT * FROM category as c1 
				WHERE 0 = (SELECT count(*) FROM category as c2 
					WHERE c2.lft < (SELECT lft FROM category as c3 WHERE c3.id = c1.id) 
					AND c2.rgt > (SELECT rgt FROM category as c4 WHERE c4.id = c1.id) 
					AND c2.root = (SELECT root FROM category as c5 WHERE c5.id = c1.id) 
					AND c2.published = 0) 
				AND c1.published = 1";
	}

	public function getFrontCategoriesQB(){
				$c = 'category';
				$c2 = 'cat';
				$c3 = 'categ';
				$c4 = 'catego';
				$c5 = 'categor';

				$qb2 = $this->createQueryBuilder($c2);
				$qb3 = $this->createQueryBuilder($c3);
				$qb4 = $this->createQueryBuilder($c4);
				$qb5 = $this->createQueryBuilder($c5);

				$qb5->select($c5.'.root')
				->andWhere($c5.'.id = '.$c.'.id');
				$qb4->select($c4.'.rgt')
				->andWhere($c4.'.id = '.$c.'.id');
				$qb3->select($c3.'.lft')
				->andWhere($c3.'.id = '.$c.'.id');
				
				$andModule2 = $qb2->expr()->andX();
				$andModule2->add($qb2->expr()->lt($c2.'.lft', "(".$qb3->getDQL().")"));
				$andModule2->add($qb2->expr()->gt($c2.'.rgt', "(". $qb4->getDQL() .")" ));
				$andModule2->add($qb2->expr()->eq($c2.'.root', "(". $qb5->getDQL() .")" ));
				$andModule2->add($qb2->expr()->eq($c2.'.published', ':cnp'));

				$qb2->select('count('.$c2.'.id)')
				->andWhere($andModule2);

				$qb = $this->createQueryBuilder($c);

				$andModule = $qb->expr()->andX();
				$andModule->add($qb2->expr()->eq(0, "(". $qb2->getDQL() . ")"));
				$andModule->add($qb2->expr()->eq($c.'.published', ':cp'));

				$qb->andWhere($andModule)
				->setParameter('cnp', Category::NOT_PUBLISHED)
				->setParameter('cp', Category::PUBLISHED)
				->orderBy($c.'.name','ASC');

				return $qb;
	}

	public function getFrontIdsQB(){
				$c = 'category';
				$c2 = 'cat';
				$c3 = 'categ';
				$c4 = 'catego';
				$c5 = 'categor';

				$qb2 = $this->createQueryBuilder($c2);
				$qb3 = $this->createQueryBuilder($c3);
				$qb4 = $this->createQueryBuilder($c4);
				$qb5 = $this->createQueryBuilder($c5);

				$qb5->select($c5.'.root')
				->andWhere($c5.'.id = '.$c.'.id');
				$qb4->select($c4.'.rgt')
				->andWhere($c4.'.id = '.$c.'.id');
				$qb3->select($c3.'.lft')
				->andWhere($c3.'.id = '.$c.'.id');
				
				$andModule2 = $qb2->expr()->andX();
				$andModule2->add($qb2->expr()->lt($c2.'.lft', "(".$qb3->getDQL().")"));
				$andModule2->add($qb2->expr()->gt($c2.'.rgt', "(". $qb4->getDQL() .")" ));
				$andModule2->add($qb2->expr()->eq($c2.'.root', "(". $qb5->getDQL() .")" ));
				$andModule2->add($qb2->expr()->eq($c2.'.published', ':cnp'));

				$qb2->select('count('.$c2.'.id)')
				->andWhere($andModule2);

				$qb = $this->createQueryBuilder($c);
				$qb->select($c.'.id');

				$andModule = $qb->expr()->andX();
				$andModule->add($qb2->expr()->eq(0, "(". $qb2->getDQL() . ")"));
				$andModule->add($qb2->expr()->eq($c.'.published', ':cp'));

				$qb->andWhere($andModule)
				->setParameter('cnp', Category::NOT_PUBLISHED)
				->setParameter('cp', Category::PUBLISHED)
				->orderBy($c.'.name','ASC');

				return $qb;
	}


}
