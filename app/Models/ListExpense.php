<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListExpense extends Model
{
    use HasFactory;
    protected $fillable = ['name','code','expenditure_id'];

    public function expenditure()
    {
        return $this->belongsTo('App\Models\ListDropdown', 'expenditure_id', 'id');
    }

    public function allotments()
    {
        return $this->hasMany('App\Models\AllotmentList', 'expense_id');
    } 

    public function disbursements()
    {
        return $this->hasMany('App\Models\Disbursement', 'expense_id');
    } 

    public function balances()
    {
        return $this->hasMany('App\Models\AllotmentBalance', 'expense_id');
    } 

    public function getUpdatedAtAttribute($value)
    {
        return date('M d, Y g:i a', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('M d, Y g:i a', strtotime($value));
    }
}
