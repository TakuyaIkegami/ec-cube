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

use Doctrine\ORM\NoResultException;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Page;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PageRepository extends AbstractRepository
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var string
     * @path %eccube_theme_user_data_dir% (app/template/user_data)
     */
    protected $userDataRealDir;

    /**
     * @var string
     * @path %eccube_theme_app_dir% (app/template)
     */
    protected $templateRealDir;

    /**
     * @var string
     * @path %eccube_theme_src_dir% (src/Eccube/Resource/template)
     */
    protected $templateDefaultRealDir;

    /**
     * PageRepository constructor.
     *
     * @param RegistryInterface $registry
     * @param EccubeConfig $eccubeConfig
     * @param ContainerInterface $container
     */
    public function __construct(RegistryInterface $registry, EccubeConfig $eccubeConfig, ContainerInterface $container)
    {
        parent::__construct($registry, Page::class);
        $this->eccubeConfig = $eccubeConfig;
        $this->userDataRealDir = $container->getParameter('eccube_theme_user_data_dir');
        $this->templateRealDir = $container->getParameter('eccube_theme_app_dir');
        $this->templateDefaultRealDir = $container->getParameter('eccube_theme_src_dir');
    }

    /**
     * @param $route
     *
     * @return Page
     */
    public function getPageByRoute($route)
    {
        $qb = $this->createQueryBuilder('p');

        try {
            $Page = $qb
                ->select(['p', 'pl', 'l'])
                ->leftJoin('p.PageLayouts', 'pl')
                ->leftJoin('pl.Layout', 'l')
                ->where('p.url = :url')
                ->setParameter('url', $route)
                ->getQuery()
                ->useResultCache(true, $this->getCacheLifetime())
                ->getSingleResult();
        } catch (\Exception $e) {
            return $this->newPage();
        }

        return $Page;
    }

    /**
     * @param string $url
     *
     * @return Page
     *
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByUrl($url)
    {
        $qb = $this->createQueryBuilder('p');
        $Page = $qb->select('p')
            ->where('p.url = :route')
            ->setParameter('route', $url)
            ->getQuery()
            ->useResultCache(true, $this->getCacheLifetime())
            ->getSingleResult();

        return $Page;
    }

    /**
     * @return Page
     */
    public function newPage()
    {
        $Page = new \Eccube\Entity\Page();
        $Page->setEditType(Page::EDIT_TYPE_USER);

        return $Page;
    }

    /**
     * ページの属性を取得する.
     *
     * この関数は, dtb_Page の情報を検索する.
     *
     * @param  string                            $where 追加の検索条件
     * @param  string[]                          $parameters 追加の検索パラメーター
     *
     * @return array                             ページ属性の配列
     */
    public function getPageList($where = null, $parameters = [])
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.id <> 0')
            ->andWhere('(p.MasterPage is null OR p.edit_type = :edit_type)')
            ->orderBy('p.id', 'ASC')
            ->setParameter('edit_type', Page::EDIT_TYPE_DEFAULT_CONFIRM);
        if (!is_null($where)) {
            $qb->andWhere($where);
            foreach ($parameters as $key => $val) {
                $qb->setParameter($key, $val);
            }
        }

        $Pages = $qb
            ->getQuery()
            ->getResult();

        return $Pages;
    }
}
