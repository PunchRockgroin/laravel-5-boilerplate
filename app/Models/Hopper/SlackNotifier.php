<?php

namespace App\Models\Hopper;

use Illuminate\Database\Eloquent\Model;

class SlackNotifier extends Model
{
    //
	 protected $fillable = array(
        'name',
        'type',
        'username',
        'channel',
        'pretext',
        'text',
        'color',
        'fields',
        'link_names',
        'unfurl_links',
        'unfurl_links',
        'markdown_in_attachments',
    );
	 
	 /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'fields' => 'array',
        'markdown_in_attachments' => 'array',
    ];
}