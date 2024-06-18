<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Bicycle;
use OpenApi\Annotations as OA; use Validator;

/**
 * Class Controller,
 * 
 * @author Vincent <vincent.4220232012@civitas.ukrida.ac.id>
 */

 /**
 * @OA\Info(
 *     title="GoWes API Documentation",
 *     version="1.0.0",
 *     description="API documentation for GoWes - an online bicycle store.",
 *     @OA\Contact(
 *         email="vincent.4220232012@civitas.ukrida.ac.id"
 *     ),
 *     @OA\License(
 *         name="MIT License",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 */
class BicycleController extends Controller
{
    /** 
     * @OA\Get(
     *     path="/api/bicycle",
     *     tags={"bicycle"},
     *     summary="Display a listing of the items",
     *     operationId="index",
     *     @OA\Response(
     *         response=200,
     *         description="successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="_page",
     *         in="query",
     *         description="current page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_limit",
     *         in="query",
     *         description="max item in a page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_search",
     *         in="query",
     *         description="word to search",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_manufacturer",
     *         in="query",
     *         description="search by manufacturer like name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="_sort_by",
     *         in="query",
     *         description="word to search",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="latest"
     *         )
     *     ),
     * )
     */
    public function index(Request $request)
    {
        try {
            $data['filter']       = $request->all();
            $page                 = $data['filter']['_page']  = (@$data['filter']['_page'] ? intval($data['filter']['_page']) : 1);
            $limit                = $data['filter']['_limit'] = (@$data['filter']['_limit'] ? intval($data['filter']['_limit']) : 1000);
            $offset               = ($page?($page-1)*$limit:0);
            $data['products']     = Bicycle::whereRaw('1 = 1');
            
            if($request->get('_search')){
                $data['products'] = $data['products']->whereRaw('(LOWER(model) LIKE "%'.strtolower($request->get('_search')).'%")');
            }
            if($request->get('_type')){
                $data['products'] = $data['products']->whereRaw('LOWER(type) = "'.strtolower($request->get('_type')).'"');
            }
            if($request->get('_sort_by')){
            switch ($request->get('_sort_by')) {
                default:
                case 'latest_production':
                $data['products'] = $data['products']->orderBy('production_year','DESC');
                break;
                case 'latest_added':
                $data['products'] = $data['products']->orderBy('created_at','DESC');
                break;
                case 'name_asc':
                $data['products'] = $data['products']->orderBy('model','ASC');
                break;
                case 'name_desc':
                $data['products'] = $data['products']->orderBy('model','DESC');
                break;
                case 'price_asc':
                $data['products'] = $data['products']->orderBy('price','ASC');
                break;
                case 'price_desc':
                $data['products'] = $data['products']->orderBy('price','DESC');
                break;
            }
            }
            $data['products_count_total']   = $data['products']->count();
            $data['products']               = ($limit==0 && $offset==0)?$data['products']:$data['products']->limit($limit)->offset($offset);
            // $data['products_raw_sql']       = $data['products']->toSql();
            $data['products']               = $data['products']->get();
            $data['products_count_start']   = ($data['products_count_total'] == 0 ? 0 : (($page-1)*$limit)+1);
            $data['products_count_end']     = ($data['products_count_total'] == 0 ? 0 : (($page-1)*$limit)+sizeof($data['products']));
           return response()->json($data, 200);

        } catch(\Exception $exception) {
            throw new HttpException(400, "Invalid data : {$exception->getMessage()}");
        }
    }

    /**
     * @OA\Post(
     *     path="/api/bicycle",
     *     tags={"bicycle"},
     *     summary="Store a newly created item",
     *     operationId="store",
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body description",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Bicycle",
     *             example={"model": "BOMBTRACK BEYOND 24 inch Junior Gravel Bike", "manufacturer": "Bombtrack", "nation": "Taiwan", "production_year": "2024",
     *                      "image": "https://www.bmtbonline.com/WebRoot/Store10/Shops/61513316/6210/7FB1/4374/F56C/CD21/0A0C/6D0E/9DBD/FBBeyondJunior_web_m.jpg",
     *                      "description": "Bombtrack tidak melupakan para penjelajah kecil di luar sana. Beyond Junior adalah versi Beyond yang diperkecil dengan ketinggian standover yang rendah sehingga si bungsu dapat melakukan petualangan kecilnya sendiri atau mengikuti tamasya kelompok. Rangka aluminium memiliki semua perlengkapan yang diperlukan dan garpu aluminium memungkinkan anak membawa sebanyak atau sesedikit yang mereka inginkan.",
     *                      "price": 15000000}
     *         ),
     *     ),
     *      security={{"passport_token_ready":{}, "passport":{}}}
     * )
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'model'  => 'required|unique:bicycle',
                'manufacturer'  => 'required|max:100',
            ]);
            if ($validator->fails()) {
                throw new HttpException(400, $validator->messages()->first());
            }
            $bicycle = new Bicycle;
            $bicycle->fill($request->all())->save();
            return $bicycle;

        } catch(\Exception $exception) {
            throw new HttpException(400, "Invalid Data : {$exception->getMessage()}");
        }
    }

    /**
     * @OA\Get(
     *     path="/api/bicycle/{id}",
     *     tags={"bicycle"},
     *     summary="Display the specified item",
     *     operationId="show",
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of item that needs to be displayed",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $bicycle = Bicycle::find($id);
        if(!$bicycle){
            throw new HttpException(404, 'Item not found');
        }
        return $bicycle;
    }

    /**
     * @OA\Put(
     *     path="/api/bicycle/{id}",
     *     tags={"bicycle"},
     *     summary="Update the specified item",
     *     operationId="update",
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of item that needs to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body description",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Bicycle",
     *             example={"model": "BOMBTRACK BEYOND 24 inch Junior Gravel Bike", "manufacturer": "Bombtrack", "nation": "Taiwan", "production_year": "2024",
     *                      "image": "https://www.bmtbonline.com/WebRoot/Store10/Shops/61513316/6210/7FB1/4374/F56C/CD21/0A0C/6D0E/9DBD/FBBeyondJunior_web_m.jpg",
     *                      "description": "Bombtrack tidak melupakan para penjelajah kecil di luar sana. Beyond Junior adalah versi Beyond yang diperkecil dengan ketinggian standover yang rendah sehingga si bungsu dapat melakukan petualangan kecilnya sendiri atau mengikuti tamasya kelompok. Rangka aluminium memiliki semua perlengkapan yang diperlukan dan garpu aluminium memungkinkan anak membawa sebanyak atau sesedikit yang mereka inginkan.",
     *                      "price": 15000000}
     *         ),
     *     ),
     *      security={{"passport_token_ready":{}, "passport":{}}}
     * )
     */
    public function update(Request $request, $id)
    {
        $bicycle = Bicycle::find($id);
        if(!$bicycle){
            throw new HttpException(404, 'Item not found');
        }

        try{
            $validator = Validator::make($request->all(), [
                'model'  => 'required|unique:bicycle',
                'manufacturer'  => 'required|max:100',
            ]);
            if ($validator->fails()) {
                throw new HttpException(400, $validator->messages()->first());
            }
           $bicycle->fill($request->all())->save();
           return response()->json(array('message'=>'Updated successfully'), 200);

        } catch(\Exception $exception) {
            throw new HttpException(400, "Invalid Data : {$exception->getMessage()}");
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/bicycle/{id}",
     *     tags={"bicycle"},
     *     summary="Remove the specified item",
     *     operationId="destroy",
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of item that needs to be removed",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *      security={{"passport_token_ready":{}, "passport":{}}}
     * )
     */
    public function destroy($id)
    {
        $bicycle = Bicycle::find($id);
        if(!$bicycle){
            throw new HttpException(404, 'Item not found');
        }

        try {
            $bicycle->delete();
            return response()->json(array('message'=>'Deleted successfully'), 200);

        } catch(\Exception $exception) {
            throw new HttpException(400, "Invalid data : {$exception->getMessage()}");
        }
    }
}