$(document).ready(function () {	

	var pjangoImageItem = '<div class="pjango_image ui-widget-content ui-corner-tr">';
	pjangoImageItem += '<img src="'+ADMIN_MEDIA_URL+'/img/noimage.jpg" width="96" height="72" class="pjango_image"/>';
	pjangoImageItem += '<a href="javascript:void(0)" title="Büyüt" class="ui-icon ui-icon-zoomin pjango_image_large_url">Büyüt</a>';
	pjangoImageItem += '<a href="javascript:void(0)" title="Sil" class="ui-icon ui-icon-trash pjango_image_delete_url">Sil</a>';
	pjangoImageItem += '<a href="javascript:void(0)" title="pjango_image" class="ui-icon ui-icon-folder-open pjango_image_open_url">Değiştir</a></div>';

	var pjangoImage = $(pjangoImageItem).appendTo($("#id_image").parent()); 
	$("#id_image").hide();
	
	$(".pjango_image_open_url").click(function( event ) {
    	mcImageManager.open('', '','','changeImage');
    });
	
	var pjangoImageVal = $("#id_image").val();
	
	if(pjangoImageVal.length>0){
		$(".pjango_image > .pjango_image").attr("src",$("#id_image").val());	
	}
	   
	
});

function changeImage(url, data) {    
	$(".pjango_image > .pjango_image").attr("src",url);
	$(".pjango_image > .pjango_image_large_url").attr("href",url);
	$("#id_image").val(url);
	
    $( ".pjango_image" ).click(function( event ) {
        var $item = $( this ), $target = $( event.target );

        if ( $target.is( "a.ui-icon-trash" ) ) {
            deleteImage($item);
        } else if ( $target.is( "a.ui-icon-zoomin" ) ) {
            viewLargerImage( $target );
        }

        return false;
    });   	
}

function viewLargerImage( $link ) {
	
    var src = $link.attr( "href" ),
        title = $link.siblings( "img" ).attr( "alt" ),
        $modal = $( "img[alt$='" + src + "']" );
    

    
    if ( $modal.length ) {
        $modal.dialog( "open" );
    } else {
        var img = $( "<img alt='" + src + "' width='384' height='288' style='display: none; padding: 8px;' />" )
            .attr( "src", src ).appendTo( "body" );
        setTimeout(function() {
            img.dialog({
                title: title,
                width: 800,
                height: 600,
                modal: true
            });
        }, 1 );
    }
    
}

function deleteImage($item) {
	$(".pjango_image > .pjango_image").attr("src",ADMIN_MEDIA_URL+'/img/blank.gif');   
	$("#id_image").val('');
}