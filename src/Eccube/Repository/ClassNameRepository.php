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

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Entity\ClassName;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

/**
 * ClassNameRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClassNameRepository extends AbstractRepository
{
    /**
     * ClassNameRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ClassName::class);
    }

    /**
     * 規格一覧を取得する.
     *
     * @return array 規格の配列
     */
    public function getList()
    {
        $qb = $this->createQueryBuilder('cn')
            ->orderBy('cn.sort_no', 'DESC');
        $ClassNames = $qb->getQuery()
            ->getResult();

        return $ClassNames;
    }

    /**
     * 規格を保存する.
     *
     * @param ClassName $ClassName
     */
    public function save($ClassName)
    {
        if (!$ClassName->getId()) {
            $sortNo = $this->createQueryBuilder('cn')
                ->select('COALESCE(MAX(cn.sort_no), 0)')
                ->getQuery()
                ->getSingleScalarResult();
            $ClassName->setSortNo($sortNo + 1);
        }

        $em = $this->getEntityManager();
        $em->persist($ClassName);
        $em->flush();
    }

    /**
     * 規格を削除する.
     *
     * @param ClassName $ClassName
     *
     * @throws ForeignKeyConstraintViolationException 外部キー制約違反の場合
     * @throws DriverException SQLiteの場合, 外部キー制約違反が発生すると, DriverExceptionをthrowします.
     */
    public function delete($ClassName)
    {
        $sortNo = $ClassName->getSortNo();
        $this->createQueryBuilder('cn')
            ->update()
            ->set('cn.sort_no', 'cn.sort_no - 1')
            ->where('cn.sort_no > :sort_no')
            ->setParameter('sort_no', $sortNo)
            ->getQuery()
            ->execute();

        $em = $this->getEntityManager();
        $em->remove($ClassName);
        $em->flush();
    }
}
