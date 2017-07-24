<?php

namespace Acme\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Eccube\EntityExtension("Eccube\Entity\BaseInfo")
 */
trait BaseInfoTrait
{
    /**
     * @ORM\Column(name="company_name_vn", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="にゅうりょくしてくださいね！！！")
     * @Eccube\FormAppend(
     *     auto_render=false,
     *     form_theme="Form/company_name_vn.twig",
     *     type="\Symfony\Component\Form\Extension\Core\Type\TextType",
     *     options={
     *          "required": true,
     *          "label": "会社名(VN)"
     *     })
     */
    public $company_name_vn;

    /**
     * @ORM\ManyToOne(targetEntity="\Eccube\Entity\Master\Db")
     * @ORM\JoinColumn(name="database_id", referencedColumnName="id")
     * @Eccube\FormAppend()
     */
    public $DataBase;
}
