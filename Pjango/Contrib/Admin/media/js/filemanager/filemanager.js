function image_upload(field, preview) {
    $('#dialog').remove();

	$('#thirdLevelContent').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="'+ SITE_URL +'/admin/FileManager?field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
	    title: 'Image Manager',
	    close: function (event, ui) {
	        if ($('#' + field).attr('value')) {
	            $.ajax({
	                url: SITE_URL+'/admin/FileManager/image',
	                type: 'POST',
	                data: 'image=' + encodeURIComponent($('#' + field).attr('value')),
	                dataType: 'text',
	                success: function(data) {
	                	$('#' + field).val(data);
	                    $('#' + preview).replaceWith('<div id="' + preview + '" class="thumb" onclick="image_upload(\'' + field + '\', \'' + preview + '\');" ><img src="' + data + '" alt="" width="100px"/>');
	                    }
	                });
	            }
	        },  
	        bgiframe: false,
	        width: 800,
	        height: 400,
	        resizable: false,
	        modal: false
	    });
};