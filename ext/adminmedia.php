<?php
# For compatibility with Django.
class Admin_media_prefix_Tag extends H2o_Node {
    function render($context, $stream) {
        $stream->write($GLOBALS['ADMIN_MEDIA_URL']);
    }
}
h2o::addTag('admin_media_prefix');
