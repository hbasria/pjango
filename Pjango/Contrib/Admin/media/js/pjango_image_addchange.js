$(document).ready(function () {	
	var imgVal = $("#id_image").val();
	if(imgVal.length<1) imgVal = ADMIN_MEDIA_URL+'/img/no-image.jpg';
	
	var imgElem = '<img id="id_image_preview" src="'+imgVal+'" width="100px" class="thumb" onclick="image_upload(\'id_image\', \'id_image_preview\');" />';
	var img = $(imgElem).appendTo($("#id_image").parent()); 
	$("#id_image").hide();	


	   
	
});
