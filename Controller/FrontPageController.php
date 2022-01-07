<?php
/*
 * This file is part of the Kazetenn Articles Bundle
 *
 * (c) Gwilherm-Alan Turpin (elvandar.ysalys@protonmail.com) 2022.
 *
 * For more informations about the license and copyright, please view the LICENSE file at the root of the project.
 */

namespace Kazetenn\Articles\Controller;

use Exception;
use Kazetenn\Articles\Entity\Article;
use Kazetenn\Articles\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontPageController extends AbstractController
{
    /**
     * @Route("/{page_path_1}/{page_path_2}", name="front_index", methods={"GET"})
     * @throws Exception
     */
    public function index(ArticleRepository $pageRepository, string $page_path_1 = null, string $page_path_2 = null): Response
    {
        /** @var Article|null $page */
        $page = $pageRepository->findPage($page_path_1, $page_path_2);

        if (null === $page) {
            return $this->redirectToRoute('not_found');
        }

        return $this->render('@KazetennArticles/display_page.html.twig', [
            'article' => $page,
        ]);
    }
}
