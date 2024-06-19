<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use OpenApi\Annotations as OA;

/**
 * Class Bicycle.
 * 
 * @author Vincent <vincent.4220232012@civitas.ukrida.ac.id>
 * 
 * @OA\Schema(
 *     description="Bicycle model",
 *     title="Bicycle model",
 *     required={"model", "manufacturer"},
 *     @OA\Xml(
 *         name="Bicycle"
 *     )
 * )
 */
class Bicycle extends Model
{
    // use HasFactory;
    use SoftDeletes;
    protected $table = 'bicycle';
    protected $primaryKey = 'ID';
    protected $fillable = [
        'model',
        'manufacturer',
        'nation',
        'image',
        'description',
        'price',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function data_adder(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}