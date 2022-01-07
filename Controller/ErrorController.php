<?php
/*
 * This file is part of the Kazetenn Articles Bundle
 *
 * (c) Gwilherm-Alan Turpin (elvandar.ysalys@protonmail.com) 2022.
 *
 * For more informations about the license and copyright, please view the LICENSE file at the root of the project.
 */

namespace Kazetenn\Articles\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route("/404", name="not_found", methods={"GET"}, priority="1")
     */
    public function notFound(): Response
    {
        return $this->render('@KazetennArticles/not_found.html.twig');
    }
}
