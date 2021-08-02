<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use App\Entity\ListEntry;


class EntryController extends AbstractController
{
  protected const PROP_MAP = [
    'date' => 'EntryDate',
    'name' => 'Name',
    'quantity' => 'Quantity',
    'distance' => 'Distance',
  ];

  protected const PROP_CONV = [
    'EntryDate' => 'convertEntryDate'
  ];
  
  protected static function convertEntryDate(string $value): ?\DateTime 
  {
    try {
      return \DateTime::createFromFormat('Y-m-d', $value);
    }
    catch (\Exception $e) {
      return null;
    }
  }
  
  /**
   * @Route("/data/entries/", name="entries")
   */
  public function entries(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
  {
    $data = [];
    $total = $pages = 0;
    $pagesize = $request->query->get('pagesize') ? $request->query->get('pagesize') : 25;
    $entryRepository = $entityManager->getRepository('\App\Entity\ListEntry');

    try {
      $sortOrder = ['EntryDate' => 'DESC'];
      $customSort = $request->query->get('sort');
      $sortOrder = (!empty($customSort) && self::PROP_MAP[$customSort])
          ?[self::PROP_MAP[$customSort] => 'ASC'] + $sortOrder
          : $sortOrder;

      $criteria = Criteria::create()
        ->orderBy($sortOrder);
      $filter = $request->query->get('filter');

      if (!empty($filter)) {
        $field = self::PROP_MAP[$request->query->get('field')];
        $op = $request->query->get('condition');
        $criteria->andWhere(Criteria::expr()->$op(
          $field,
          isset(self::PROP_CONV[$field])  ? call_user_func('self::' . self::PROP_CONV[$field], $filter) : $filter
        ));
      }

      $total = $entryRepository->matching($criteria)->count();
      $pages = intdiv($total, $pagesize) + (int) (($total % $pagesize) > 0);

      $criteria->setMaxResults($pagesize)
        ->setFirstResult(($request->query->get('page') - 1) * $pagesize);
      $entries = $entryRepository->matching($criteria);

      foreach ($entries as $entry) {
          $data[] = $serializer->normalize($entry, null);
      }
    }
    catch (\Exception $e) {

      return new JsonResponse([
        'query' => $request->query->all(),
        'error' => $e->getMessage()
      ]);
    }
    $response = new JsonResponse([
        'query' => $request->query->all(),
        'data' => ['items' => $data, 'pages' => $pages, 'total' => $total]
    ]);
    $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);

    return $response;
  }
}
