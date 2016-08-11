<?php
/*
 * This file is part of the pixSortableBehaviorBundle.
 *
 * (c) Nicolas Ricci <nicolas.ricci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aliocza\SortableUiBundle\Services;

use Doctrine\ORM\EntityManagerInterface;

class PositionORMHandler extends PositionHandler
{

    /**
     *
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }


    
    public function setPosition($entity, $setter, $dataPositionList)
    {
        
        foreach ($dataPositionList as $position){
            
           $editId = $position['id'];
           $position = $position['position'];
            
            $qb = $this->em->createQueryBuilder();
            $q = $qb->update($entity, 'u')
                    ->set('u.position', $qb->expr()->literal($position))
                    ->where('u.id = ?1')
                    ->setParameter(1, $editId)
                    ->getQuery();
           
            
            try {
                  $p = $q->execute();
                }
            catch(\Doctrine\ORM\NoResultException $e) {
                return false;
            }
            
        }
        

        return true;
    }


}
