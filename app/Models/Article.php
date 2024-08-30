<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'path','image','original_url','parts' ,'news_company_id'];

    public function newsCompany(){
        return $this->belongsTo(NewsCompany::class , 'news_company_id');  // Assuming NewsCompany model has a foreign key 'news_company_id'
    }
}
