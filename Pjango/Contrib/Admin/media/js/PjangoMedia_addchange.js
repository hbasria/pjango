$(document).ready(function () {	
	var imgVal = $("#id_file_path").val();
	if(imgVal.length<1) imgVal = ADMIN_MEDIA_URL+'/img/no-image.jpg';
	
	var imgElem = '<img id="id_image_preview" src="'+imgVal+'" width="100px" class="thumb" onclick="image_upload(\'id_file_path\', \'id_image_preview\');" />';
	var img = $(imgElem).appendTo($("#id_file_path").parent()); 
	$("#id_file_path").hide();	
	
});
