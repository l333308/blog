<?php


namespace App;


class TestingGoods extends Model
{
    public static $fullTableName = 'testing_goods';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'num'
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;

}