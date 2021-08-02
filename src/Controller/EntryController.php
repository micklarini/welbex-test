<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ListEntry;


class EntryController extends AbstractController
{
  protected const PROP_MAP = [
    'name' => 'Name',
    'quantity' => 'Quantity',
    'distance' => 'Distance',
  ];
  
  /**
   * @Route("/data/entries/", name="entries")
   */
  public function entries(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
  {
    $query = $request->query->all();
    $entryRepository = $entityManager->getRepository('\App\Entity\ListEntry');

    $sortOrder = ['EntryDate' => 'DESC'];
    $customSort = $request->query->get('sort');
    $sortOrder = (!empty($customSort) && self::PROP_MAP[$customSort]) 
        ?[self::PROP_MAP[$customSort] => 'ASC'] + $sortOrder
        : $sortOrder;

    $entries = $entryRepository->findBy([], $sortOrder);
    
    $data = [];
    foreach ($entries as $entry) {
        $data[] = $serializer->normalize($entry, null);
    }

    $response = new JsonResponse(['query' => $query, 'data' => $data]);
    $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

    return $response;
  }
}
