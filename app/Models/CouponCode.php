<?php

namespace App\Models;

use App\Exceptions\CouponCodeUnavailableException;
use Carbon\Carbon;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class CouponCode extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    protected $appends = ['description'];

    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED => '固定金额',
        self::TYPE_PERCENT => '比例',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enable',
    ];

    protected $casts = [
        'enable' => 'boolean',
    ];

    protected $dates = [
        'not_before',
        'not_after',
    ];
    public static function findAvailableCode($length = 16)
    {
        do {
            $code = strtoupper(Str::random($length));

        }while(self::query()->where('code', $code)->exists());
        return $code;
    }

    public function getDescriptionAttribute()
    {
        $str = '';
        if($this->min_amount > 0){
            $str = '满'.str_replace('.00', '', $this->min_amount);
        }
        if($this->type === self::TYPE_PERCENT){
            return $str.'优惠'.str_replace('.00', '', $this->value).'%';
        }
        return $str.'减'.str_replace('.00', '', $this->value);
    }

    public function checkAvailable($orderAmount = null)
    {
        if(!$this->enabled){
            throw new CouponCodeUnavailableException('优惠券不存在');
        }
        if($this->total - $this->used <= 0){
            throw new CouponCodeUnavailableException('优惠券已兑换完');
        }
        if($this->not_before && $this->not_before->gt(Carbon::now())){
            throw new CouponCodeUnavailableException('优惠券已过期');
        }
        if($this->not_after && $this->not_after->lt(Carbon::now())){
            throw new CouponCodeUnavailableException('优惠券已过期');
        }
        if(!is_null($orderAmount) && $orderAmount < $this->min_amount){
            throw new CouponCodeUnavailableException('优惠券金额不足');
        } 
    }

    public function getAdjustedPrice($orderAmount)
    {
        if($this->type === self::TYPE_FIXED){
            return max(0.01,$orderAmount - $this->value);
        }
        return number_format($orderAmount * (100 - $this->value) / 100,2,'.','');
    }

    public function changeUsed($increase = true)
    {
        if($increase){
            return $this->where('id', $this->id)
            ->where('used', '<', $this->total)
            ->increment('used');
        }else{
            return $this->decrement('used');
        }
    }
}
