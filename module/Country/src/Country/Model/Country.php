<?php
namespace Country\Model;

use Illuminate\Database\Eloquent\Model as EloquentZF2Model;

// class Album extends EloquentZF2Model implements InputFilterAwareInterface
    
class Country extends EloquentZF2Model {
    protected $table = 'chk_country';
    public $timestamps = false;

}