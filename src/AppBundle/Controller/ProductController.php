<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ProductController extends Controller
{
    /**
     * @Route("/product/create")
     * @Method("POST")
     */
    public function createAction(Request $request) 
    {
        $params = array();

        // For processing JSON
        $content = $request->getContent();

        if (empty($content)) {
           return new JsonResponse('Invalid request parameters');
        }

        $params = json_decode($content, true);

        $product = new Product;

        $name = isset($params['name']) ? $params['name'] : '';
        $price = isset($params['price']) ? $params['price'] : 0;
        $description = isset($params['description']) ? $params['description'] : '';

        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        
        $em = $this->getDoctrine()->getManager();
        
        // Tells Doctrine you want to save the Product (no queries yet)
        $em->persist($product);

        // Executes query
        $em->flush();

        return new JsonResponse('Created a new product with id: ' . $product->getId());
    }

    /**
     * @Route("/product/search")
     * @Method("GET")
     */
    public function searchAction(Request $request) 
    {
        $key = $request->query->get('key');
        $response = [];

        // Doctrine Query Language
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT p  FROM AppBundle:Product p
            WHERE p.name like :name
            ORDER BY p.price ASC 
            "
        )->setParameter('name', '%' . $key . '%');

        $products = $query->getResult();

        if (!$products) {
            return new JsonResponse('Product(s) not found');
        }

        foreach ( $products as $product) {
            $response[] = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice()
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/product/quick-search")
     * @Method("GET")
     */
    public function quickSearchAction(Request $request)
    {
        $key = $request->query->get('key');
        $response = [];

        $products = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->searchByName($key);

        foreach ($products as $product) {
            $response[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription()
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/product/advanced-search")
     * @Method("GET")
     */
    public function advancedSearchAction(Request $request) 
    {
        $key = $request->query->get('key');
        $order = $request->query->get('order', 'ASC');
        $limit = $request->query->get('limit');
        $response = [];

        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');

        /**  Alternatively, you can also use this **/
        // $repository = $this->getDoctrine()->getRepository(Product::class);
        
        // Querying for Objects Using Doctrine's Query Builder
        $query = $repository->createQueryBuilder('p')
            ->where("p.name LIKE :key")
            ->orderBy("p.price", $order)
            ->setParameters([
                "key" => "%$key%"
            ]);

        // Set limit if there is limit    
        if (isset($limit))
            $query = $query->setMaxResults($limit);

        $products = $query->getQuery()->getResult();

        foreach ($products as $product) {
            $response[] = [
                "id" => $product->getId(),
                "name" => $product->getName(),
                "price" => $product->getPrice(),
                "description" => $product->getDescription()
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/product/{id}")
     * @Method("GET")
     */
    public function showAction($id)
    {
        $repository = $this->getDoctrine()
            ->getRepository(Product::class);
        
        // Query by its primary key (usually "id")    
        $product = $repository->find($id);

        /** Query for a single product matching the given name and price [description] **/
        // $product = $repository->findOneBy(
        //     array('name' => 'Keyboard', 'price' => 19.99)
        // );
        

        /** Query for multiple products matching the given name, ordered by price **/
        // $products = $repository->findBy(
        //     array('name' => 'Keyboard'),
        //     array('price' => 'ASC')
        // );

        if (!$product) {
            return new JsonResponse('Product not found');
        }

        $response = array(
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription()
        );

        return new JsonResponse($response);
    }

    

    /**
     * @Route("/product/update")
     * @Method("POST")
     */
    public function updateAction(Request $request)
    {
        $params = array();

        $content = $request->getContent();

        if (empty($content)) {
            return new JsonResponse('Invalid request parameters');
        }

        $params = json_decode($content, true);

        if (empty($params['id'])) {
            return new JsonResponse('Missing id on request parameters');
        }

        $name = isset($params['name']) ? $params['name'] : '';
        $price = isset($params['price']) ? $params['price'] : 0;
        $description = isset($params['description']) ? $params['description'] : '';

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($params['id']);

        if (!$product) {
            return new JsonResponse('Product not found');
        }

        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);

        // Tells Doctrine you want to save the Product (no queries yet)
        $em->persist($product);

        // Executes query
        $em->flush();

        return new JsonResponse('Updated product with id: ' . $product->getId());
    }

    /**
     * @Route("/product/delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request)
    {
        $params = array();

        $content = $request->getContent();

        if (empty($content)) {
            return new JsonResponse('Invalid Parameters');
        }

        $params = json_decode($content, true);

        if (empty($params['id'])) {
            return new JsonResponse('Product not found');
        }

        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($params['id']);
        
        // Notifies doctrine that you would like to remove the given object
        $em->remove($product);

        // Executes query
        $em->flush();

        return new JsonResponse('Removed product with id: ' . $params['id']);
    }    
}
