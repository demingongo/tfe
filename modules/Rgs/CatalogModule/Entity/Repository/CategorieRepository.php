<?php

namespace Rgs\CatalogModule\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

use Rgs\CatalogModule\Entity\Categorie;

use Doctrine\NestedSetModule\Services\NestedSetManagerCreator;

/**
 * CategorieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategorieRepository extends EntityRepository
{
	public function publish(array $ids, $publish = Categorie::PUBLISHED){
		foreach($ids as $id){
			//dump($this->publishOneCategorie($id, $publish));
			$this->publishOneCategorie($id, $publish);
		}
		//exit(__METHOD__);
	}

	public function publishOneCategorie($id, $publish = Categorie::PUBLISHED){
		$qb = $this->createQueryBuilder('c');

		return $qb->update('RgsCatalogModule:Categorie', 'c')
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

		$nsm = $nsmc->getManager('RgsCatalogModule:Categorie');

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
			$nsm = $nsmc->getManager('RgsCatalogModule:Categorie');
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
		//->from('RgsCatalogModule:Categorie', 'c');

		$i = 1;
		foreach($where as $k => $v){
			$qb->andWhere($qb->expr()->eq($k, '?'.$i))
				->setParameter($i, $v);
			$i++;
		}

		return $qb->getQuery()->getSingleScalarResult();
	}
}