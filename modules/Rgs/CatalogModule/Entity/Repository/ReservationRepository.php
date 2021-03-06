<?php

namespace Rgs\CatalogModule\Entity\Repository;

/**
 * ReservationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReservationRepository extends \Doctrine\ORM\EntityRepository
{
	public function getFindReservationsQB($limit = 20, $page = 1, $where = array(), $orderBy = array(), $groupBy = array())
	{
		$qb = $this->createQueryBuilder('r');

		$qb->join ('r.user ','u');
		
		$i = 1;
		foreach($where as $k => $v){
			$qb->andWhere($qb->expr()->eq($k, '?'.$i))
				->setParameter($i, $v);
			$i++;
		}

		if(empty($orderBy))
			$orderBy = array('r.id' => 'DESC');

		foreach($orderBy as $k => $v){
			$qb	->addOrderBy($k, $v);
		}
		
		foreach($groupBy as $v){
			$qb	->addGroupBy($v);
		}
		
		return $qb ->setFirstResult(($page-1) * $limit)
			->setMaxResults($limit);
	}
	
	public function getCountReservationsQB($where = array())
	{
		$qb = $this->createQueryBuilder('r');

		$qb->join ('r.user ','u');

		$qb->select('count(r.id)');

		$i = 1;
		foreach($where as $k => $v){
			$qb->andWhere($qb->expr()->eq($k, '?'.$i))
				->setParameter($i, $v);
			$i++;
		}

		return $qb;
	}
	
	public function deleteOneById($id)
	{
		$qb = $this->createQueryBuilder('r');

		return $qb->delete('RgsCatalogModule:Reservation', 'r')
			->where('r.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->execute();
	}

	public function deleteByIds(array $ids)
	{
		foreach($ids as $id){
			$this->deleteOneById($ids);
		}
	}
	
	public function cancelOneById($id)
	{
		//get the reservation
		$reservation = $this->findOneById($id);
		
		if(!$reservation){
			return;
		}
		
		// get the articles reserved, in which quantity
		$reservation_articles = $reservation->getReservationArticles();
		
		// put the quantity of each article reserved back to the article's stock
		foreach($reservation_articles as $ra){
			$this->getEntityManager()->getRepository('RgsCatalogModule:ReservationArticle')->restockAndDelete($ra);
		}
		
		//delete the whole reservation
		$this->deleteOneById($id);
	}

	public function cancelByIds(array $ids)
	{
		foreach($ids as $id){
			$this->cancelOneById($id);
		}
	}
	
	public function cancelExpired()
	{
		$this->cancelThoseWithExpiration();
	}

	public function cancelNonExpired()
	{
		$this->cancelThoseWithExpiration(false);
	}

	private function cancelThoseWithExpiration($boolean = true){
		$sign = ">";
		if($boolean){
			$sign = "<=";
		}
		$dt = new \Datetime("now");
		$result = $this->createQueryBuilder('r')->andWhere('r.expiresAt '.$sign.' :expiresAt')
			->setParameter('expiresAt', $dt->format('Y-m-d H:i:s'))
			->getQuery()
			->getResult();
			
		return $this->cancelByIds(array_map(function($r){
			return $r->getId();
		}, $result));
	}
}
