<?php

namespace App\Controller;


use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface as EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
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
 * @Route("/author")
 */
class AuthorController extends AbstractController
{

    /**
     * @Route("/{id}", name="author", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function index(int $id, AuthorRepository $authorRepository): Response
    {
        if ($author = $authorRepository->find($id)) {
            return new JsonResponse(['name' => $author->getName(),]);
        }
        throw $this->createNotFoundException("Author $id not found");
    }

    /**
     * @Route("/create", name="author_create", methods={"PUT"})
     */
    public function create(Request $request, AuthorRepository $authorRepository, EntityManagerInterface $entityManager,LoggerInterface $logger): JsonResponse
    {
        if (($json = $request->getContent()) && ($json = json_decode($json)) && (isset($json['name']))) {
            if (!$author = $authorRepository->findOneBy(['Name' => $json['name']])) {
                $author = new Author();
                $author->setName($json['name']);
                $entityManager->persist($author);
                $entityManager->flush();
            }

            return new JsonResponse(['id' => $author->getId(),]);
        }
        $logger->debug($request->getContent());
        throw $this->createNotFoundException("No required data");
    }

}
