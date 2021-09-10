<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;

/**
 * Class AuthorController
 *
 * @package App\Controller
 */
class AuthorController extends AbstractController
{

    /**
     * @Route("/author/create", name="author_create", methods={"POST"})
     */
    public function create(
      Request $request,
      AuthorRepository $authorRepository,
      EntityManagerInterface $entityManager
    ): JsonResponse {
        if (($json = $request->getContent()) && ($json = json_decode($json)) && property_exists($json, 'name')) {
            if (!$author = $authorRepository->findOneBy(['Name' => $json->name])) {
                $author = new Author();
                $author->setName($json->name);
                $entityManager->persist($author);
                $entityManager->flush();
            }

            return new JsonResponse(['id' => $author->getId(),]);
        }

        return new JsonResponse(['message' => 'No required field'], Response::HTTP_NOT_ACCEPTABLE);
    }

}
