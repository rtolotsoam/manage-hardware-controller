<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Service\EquipmentService;
use Doctrine\ORM\EntityRepository;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\EquipmentNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Manage Equipment",
 *     version="0.1",
 *     description="Description Manage Equipment"
 * )
 */
class EquipmentController extends AbstractController
{
    private EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }
    
    /**
     * @OA\Get(
     *     path="/api/equipments",
     *     summary="Get all equipments",
     *     tags={"Equipments"},
     *     operationId="getAllEquipments",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Equipment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Equipments not found",
     *     ),
     * )
     *
     * This method is called when the retrieving information all equipments
     *
     * @param EquipmentRepository $equipmentRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     *
     * @Route("/api/equipments", name="equipments", methods={"GET"})
     */
    public function getAllEquipments(
        EquipmentRepository $equipmentRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $equipmentList = $equipmentRepository->findAll();
        $jsonEquipmentList = $serializer->serialize($equipmentList, 'json', ['groups' => 'getEquipments']);
        return new JsonResponse($jsonEquipmentList, Response::HTTP_OK, [], true);
    }

    /**
     * This method is called to retrieve information about an equipment
     *
     * @param int $id
     * @param EquipmentRepository $equipmentRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/equipment/{id}",
     *     summary="Retrieve information about an equipment",
     *     operationId="getDetailEquipment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the equipment"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             ref="#/components/schemas/Equipment"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Equipment not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     * )
     *
     * @Route("/api/equipment/{id}", name="detailEquipment", methods={"GET"})
     */
    public function getDetailEquipment(
        int $id,
        EquipmentRepository $equipmentRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        try {
            $equipment = $equipmentRepository->find($id);
            if (!$equipment) {
                $errorResponse = $this->equipmentService->handleNotFound();
                return new JsonResponse($errorResponse, Response::HTTP_NOT_FOUND);
            }
            $jsonEquipment = $serializer->serialize($equipment, 'json', ['groups' => 'getEquipments']);
            return new JsonResponse($jsonEquipment, Response::HTTP_OK, [], true);
        } catch (EquipmentNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/equipment/delete/{id}",
     *     summary="Delete an equipment",
     *     tags={"Equipments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment to be deleted",
     *         @OA\Schema(type="integer", format="int64", example=1),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Equipment deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Equipment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Equipment not found"),
     *         ),
     *     ),
     * )
     * This method is used to delete a equipment
     *
     * @param Equipment $equipment
     * @param EntityManagerInterface $em
     * @return JsonResponse
     *
     * @Route("/api/equipment/delete/{id}", name="deleteEquipment", methods={"DELETE"})
     */
    public function deleteEquipment(
        int $id,
        EquipmentRepository $equipmentRepository,
        EntityManagerInterface $em
    ): JsonResponse
    {
        try {
            $equipment = $equipmentRepository->find($id);
            if (!$equipment) {
                $errorResponse = $this->equipmentService->handleNotFound();
                return new JsonResponse($errorResponse, Response::HTTP_NOT_FOUND);
            }
            $em->remove($equipment);
            $em->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (EquipmentNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/equipment/create",
     *     summary="Create a new equipment",
     *     tags={"Equipments"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Equipment data",
     *         @OA\JsonContent(ref="#/components/schemas/Equipment"),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Equipment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Equipment"),
     *         headers={
     *             @OA\Header(header="Location", description="URL of the created equipment", @OA\Schema(type="string")),
     *         },
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Bad request"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     *
     * This method is called then when the creation of a new equipment
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     *
     * @Route("/api/equipment/create", name="createEquipment", methods={"POST"})
     */
    public function createEquipment(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        try {
            $equipment = $serializer->deserialize($request->getContent(), Equipment::class, 'json');
            $em->persist($equipment);
            $em->flush();

            $jsonEquipment = $serializer->serialize($equipment, 'json', ['groups' => 'getEquipments']);

            $location = $urlGenerator->generate(
                'detailEquipment',
                ['id' => $equipment->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            return new JsonResponse($jsonEquipment, Response::HTTP_CREATED, ["Location" => $location], true);
        } catch (BadRequestHttpException $e) {
            $errorResponse = $this->equipmentService->handleBadRequest();
            return new JsonResponse($errorResponse, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $errorResponse = $this->equipmentService->handleUpdateError();
            return new JsonResponse($errorResponse, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/equipment/update/{id}",
     *     summary="Update an existing equipment",
     *     tags={"Equipments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment to be updated",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated equipment data",
     *         @OA\JsonContent(ref="#/components/schemas/Equipment"),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Equipment updated successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Bad request"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     *
     * This method is used to update an existing equipment
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param Equipment $currentEquipment
     * @param EntityManagerInterface $em
     * @return JsonResponse
     *
     * @Route("/api/equipment/update/{id}", name="updateEquipment", methods={"PUT"})
     */
    public function updateEquipment(
        Request $request,
        SerializerInterface $serializer,
        Equipment $currentEquipment,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $updatedEquipment = $serializer->deserialize(
                $request->getContent(),
                Equipment::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentEquipment]
            );

            $em->persist($updatedEquipment);
            $em->flush();

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (BadRequestHttpException $e) {
            $errorResponse = $this->equipmentService->handleBadRequest();
            return new JsonResponse($errorResponse, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $errorResponse = $this->equipmentService->handleUpdateError();
            return new JsonResponse($errorResponse, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
