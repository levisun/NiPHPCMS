// 选择栏目字段
function fieldsCategory(t) {
	var id = jQuery(t).val();
	var t = jQuery(t).attr('data-type');
	jQuery.ajax({
		type: 'post',
		async: false,
		url: '?m=Admin&c=Category&a=fields',
		data: {id: id, type: t},
		success: function(data){
			if (t == 1) {
				jQuery('.op').remove();
			}
			jQuery('#category_id_' + t).after(data);
		}
	});
}

// 相册上传文件窗口
function uploadFile(idClass_) {
	NPUpload(idClass_, "?m=admin&c=account&a=upload");
}

// 删除上传文件input
function delAlbum(idClass_) {
	jQuery("#album-"+idClass_).remove();
}


jQuery(function($){
	// 添加相册
	jQuery(".np-album-add").click(function(){
		var timenow = new Date().getTime();
		var html = "<li id='album-"+timenow+"'><input type='text' name='album-image[]' id='album-image-"+timenow+"' class='form-control'><input type='hidden' name='album-thumb[]' id='album-thumb-"+timenow+"' class='form-control'><img src='' id='img-album-"+timenow+"' width='100' style='display:none'><button type='button' class='btn btn-success btn-sm np-upload' data-type='album' data-id='"+timenow+"' data-model='' onclick='uploadFile(this)'>上传</button><button type='button' class='btn btn-success btn-sm np-upload' onclick='delAlbum("+timenow+");'>删除</button></li>";
		jQuery("#album").append(html);
	});

	// 栏目子栏目
	jQuery('.category-pid').change(function(){
		var id = jQuery(this).val();
		jQuery.ajax({
			type: 'post',
			async: false,
			url: '?m=Admin&c=Category&a=category',
			data: {id: id},
			success: function(data){
				// jQuery('#'+type+' .op').remove();
				jQuery('.category-pid').after(data);
				alert(data);
			}
		});
	});

	/** 选择地址 */
	jQuery('.region').change(function(){
		var id = jQuery(this).val(), type = jQuery(this).attr('data-type');
		jQuery.ajax({
			type: 'post',
			async: false,
			url: '?m=Admin&c=User&a=member',
			data: {id: id},
			success: function(data){
				jQuery('#'+type+' .op').remove();
				jQuery('#'+type).append(data);
			}
		});
	});

	/** 上传窗口 */
	jQuery('#upload').click(function(){
		NPUpload('#upload', "?m=admin&c=account&a=upload");
	});
	jQuery('.np-upload').click(function(){
		NPUpload('.np-upload', "?m=admin&c=account&a=upload");
	});

	// 刷新验证码
	jQuery(".np-verify").click(function(){
		NPVerify(".np-verify");
	});
});