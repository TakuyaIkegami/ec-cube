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

use Eccube\Entity\BaseInfo;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

/**
 * BaseInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BaseInfoRepository extends AbstractRepository
{
    /**
     * BaseInfoRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BaseInfo::class);
    }

    /**
     * @param int $id
     *
     * @return BaseInfo
     */
    public function get($id = 1)
    {
        $BaseInfo = $this->find($id);

        if (null === $BaseInfo) {
            throw new \Exception('BaseInfo not found. id = '.$id);
        }

        return $this->find($id);
    }
}
