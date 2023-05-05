<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CouponCodesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CouponCode';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CouponCode());
        
        $grid->model()->orderBy('created_at', 'desc');
        $grid->column('id', 'ID')->sortable();
        $grid->column('name', '名称');
        $grid->column('code', '优惠码');
        $grid->column('description', '描述');
        $grid->column('usage', '用量')->display(function () {
            return "{$this->used} / {$this->total}";
        });
        $grid->column('min_amount', '最低金额');
        $grid->column('enabled', '是否启用')->display(function ($enabled) {
            return $enabled ? '是' : '否';
        });
        $grid->column('created_at', '创建时间');
        $grid->actions(function($actions){
            //禁止查看
            $actions->disableView();
        });

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CouponCode());

        $form->display('id', 'ID');
        $form->text('name', '名称')->rules('required');
        $form->text('code', '优惠码')->rules('nullable|unique:coupon_codes');
        $form->radio('type', '类型')->options(CouponCode::$typeMap)->rules('required')->default(CouponCode::TYPE_FIXED);
        $form->decimal('value', '折扣')->rules(function($form){
            if(request()->input('type') === CouponCode::TYPE_PERCENT){
                return 'required|numeric|between:1,99';
            }else{
                return 'required|numeric|min:0.01';
            }
        });
        $form->text('total', '总量')->rules('required|numeric|min:0');
        $form->decimal('min_amount', '最低金额')->rules('required|numeric|min:0');
        $form->datetime('not_before', '开始时间')->default(date('Y-m-d H:i:s'));
        $form->datetime('not_after', '结束时间')->default(date('Y-m-d H:i:s'));
        $form->radio('enabled', '启用')->options(['1' => '是', '0' => '否'])->default(1);
        //监听模型事件，在数据即将保存时触发
        $form->saving(function(Form $form){
            if(!$form->code){
                $form->code = CouponCode::findAvailableCode();
            }
        });
        return $form;
    }
}
