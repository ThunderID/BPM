<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class WriterAktaTransformer extends TransformerAbstract
{
	public function transform(array $value)
	{
	    $major = [
			    'info' 		=> ['id' => $value['_id']],
	    		'elements' 	=> [
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'title',
					    											'value'	=> $value['title'],
																	'validation'	=> ['required' => true, 'max' => '255'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'writer_id',
					    											'value'	=> $value['writer']['_id'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'writer_name',
					    											'value'	=> $value['writer']['name'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'owner_id',
					    											'value'	=> $value['owner']['_id'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'owner_name',
					    											'value'	=> $value['owner']['name'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'datetime',
					    			'element-properties'	=> 	[
					    											'name'	=> 'created_at',
					    											'value'	=> $value['created_at'],
					    										],
					    		],
					    		[	
					    			'element-class'			=> 'input',
					    			'element-type'			=> 'string',
					    			'element-properties'	=> 	[
					    											'name'	=> 'updated_at',
					    											'value'	=> $value['updated_at'],
					    										],
					    		],
					    		]
		];

		$major['elements'] = array_merge($major['elements'], $minor['data']);

		return $major;
	}
}