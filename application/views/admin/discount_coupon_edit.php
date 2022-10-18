<form id="voucher_add" action="<?php echo base_url('seller/discount/voucher/update/'.$voucher->id);?>" name="voucher_edit" novalidate method="post" enctype="multipart/form-data">
			<div class="row card-header d-flex align-items-center">
				<?php echo $this->lang->line('discount_coupon');?>
			</div>
			<div class="card-body">
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('discount_coupon');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" placeholder="<?php echo $this->lang->line('dicount_coupon');?>" id="voucher_code" name="voucher_code" readonly  value="<?php echo $voucher->code; ?>" />
						<label id="voucher_code-error" class="error" for="voucher_code"></label>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('title');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" placeholder="<?php echo $this->lang->line('enter_Shippingtitle');?>" id="discount_title" name="discount_title" value="<?php echo $voucher->title; ?>" />
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('limit');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="number" class="form-control" placeholder="<?php echo $this->lang->line('limit');?>" id="discount_limit" name="discount_limit" value="1" value="<?php echo $voucher->used_limit; ?>" />
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('valid_from');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" placeholder="<?php echo $this->lang->line('select_date');?>" id="txtFromDate" name="fromDate" style="cursor:pointer; background-color:#fff;" readonly value="<?php echo date('m/d/Y', $voucher->valid_from); ?>" />
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('valid_to');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" placeholder="<?php echo $this->lang->line('select_date');?>" id="txtToDate" name="toDate" style="cursor:pointer; background-color:#fff;" readonly value="<?php echo date('m/d/Y', $voucher->valid_to); ?>" />
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('type');?></label>
					</div>
					<div class="col-sm-4 mt-2">
						<div class="row">
							<div class="col-sm-4">
								<div class="custom-control custom-radio">
									<input type="radio" class="custom-control-input" name="discount_type" value="1" checked id="discount_type_percentage" <?php echo ($voucher->discount_type == 1)?'checked ':''; ?>/>
									<label class="custom-control-label" for="discount_type_percentage"><?php echo $this->lang->line('percent');?></label>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="custom-control custom-radio">
									<input type="radio" class="custom-control-input" name="discount_type" value="0" id="discount_type_fixed" <?php echo ($voucher->discount_type == 0)?'checked ':''; ?>/>
									<label class="custom-control-label" for="discount_type_fixed"><?php echo $this->lang->line('fixed');?></label>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('value');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" id="discount_value" placeholder="<?php echo $this->lang->line('value');?>" name="discount_value" value="<?php echo $voucher->discount; ?>" />
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('max_order_amount');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" id="min_price" placeholder="<?php echo $this->lang->line('max_order_amount');?>" name="min_price" value="<?php echo $voucher->min_price; ?>" />
						<small>leave zero for no minimum limit</small>
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('max_discount_amount');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" id="max_price" placeholder="<?php echo $this->lang->line('max_discount_amount');?>" name="max_price" value="<?php echo $voucher->max_price; ?>"  />
						<small>leave zero for no maximum limit</small>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('apply_on');?></label>
					</div>
					<div  class="col-sm-4">
						<select class="form-control" id="apply_on" name="apply_on">
							<option value="all" <?php echo ($voucher->apply_on == 'all')?' selected':''; ?>><?php echo $this->lang->line('all');?></option>
							<option value="product" <?php echo ($voucher->apply_on == 'product')?' selected':''; ?>><?php echo $this->lang->line('product');?></option>
							<option value="category" <?php echo ($voucher->apply_on == 'category')?' selected':''; ?>><?php echo $this->lang->line('category');?></option>
							<?php if($isAdmin){?>
							<option value="seller" <?php echo ($voucher->apply_on == 'seller')?' selected':''; ?>><?php echo $this->lang->line('seller');?></option>
							<option value="user" <?php echo ($voucher->apply_on == 'user')?' selected':''; ?>><?php echo $this->lang->line('user');?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label">
						<span class="apply_on" id="type_all"><?php echo $this->lang->line('all');?></span>
						<span class="apply_on d-none" id="type_product"><?php echo $this->lang->line('product');?></span>
						<span class="apply_on d-none" id="type_category"><?php echo $this->lang->line('category');?></span>
						<?php if($isAdmin){?>
						<span class="apply_on d-none" id="type_seller"><?php echo $this->lang->line('seller');?></span>
						<span class="apply_on d-none" id="type_user"><?php echo $this->lang->line('user');?></span>
						<?php } ?>
					</label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" placeholder="<?php echo $this->lang->line('any');?>" name="keyword" id="keyword" readonly value="<?php echo $voucher->apply_on_title; ?>" />
					</div>
				</div>
				<hr>
					<div class="row float-right mb-3">
					<button type="reset" class="btn btn-danger resetform" ><?php echo $this->lang->line('reset');?></button>
					<button type="button" class="btn btn-success  ml-3" id="Submit"><?php echo $this->lang->line('save');?></button>
					<input type="hidden" name="apply_id" id="apply_id" value="<?php echo $voucher->apply_id; ?>" />
				<div class="clearfix"></div>
			</div>
				</div>
			</div>
	</form>
</div>