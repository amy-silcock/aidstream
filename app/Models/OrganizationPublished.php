<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationPublished extends Model
{
    protected $table = "organization_published";
    protected $fillable = ['filename', 'published_to_register', 'organization_id', 'created_at', 'updated_at'];
}
