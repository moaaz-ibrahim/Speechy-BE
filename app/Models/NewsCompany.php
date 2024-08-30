<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCompany extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'url', 'news_url'];
}
