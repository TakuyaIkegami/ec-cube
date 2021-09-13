<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Repository;

use Doctrine\ORM\Query;
use Eccube\Entity\Payment;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

/**
 * PaymentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRepository extends AbstractRepository
{
    /**
     * PaymentRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @return array
     */
    public function findAllArray()
    {
        $query = $this
            ->getEntityManager()
            ->createQuery('SELECT p FROM Eccube\Entity\Payment p INDEX BY p.id');
        $result = $query
            ->getResult(Query::HYDRATE_ARRAY);

        return $result;
    }

    /**
     * 支払方法を取得
     * 条件によってはDoctrineのキャッシュが返されるため、arrayで結果を返すパターンも用意
     *
     * @param $delivery
     * @param $returnType true : Object、false: arrayが戻り値
     *
     * @return array
     */
    public function findPayments($delivery, $returnType = false)
    {
        $query = $this->createQueryBuilder('p')
            ->innerJoin('Eccube\Entity\PaymentOption', 'po', 'WITH', 'po.payment_id = p.id')
            ->where('po.Delivery = (:delivery) AND p.visible = true')
            ->orderBy('p.sort_no', 'DESC')
            ->setParameter('delivery', $delivery)
            ->getQuery();

        $query->expireResultCache(false);

        if ($returnType) {
            $payments = $query->getResult();
        } else {
            $payments = $query->getArrayResult();
        }

        return $payments;
    }

    /**
     * 共通の支払方法を取得
     *
     * @param $deliveries
     *
     * @return array
     */
    public function findAllowedPayments($deliveries, $returnType = false)
    {
        $payments = [];
        $saleTypes = [];
        foreach ($deliveries as $Delivery) {
            $p = $this->findPayments($Delivery, $returnType);
            if ($p == null) {
                continue;
            }
            foreach ($p as $payment) {
                $payments[$payment['id']] = $payment;
                $saleTypes[$Delivery->getSaleType()->getId()][$payment['id']] = true;
            }
        }

        foreach ($payments as $key => $payment) {
            foreach ($saleTypes as $row) {
                if (!isset($row[$payment['id']])) {
                    unset($payments[$key]);
                    continue;
                }
            }
        }

        return $payments;
    }
}
